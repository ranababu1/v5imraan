#!/usr/bin/env php
<?php
/**
 * audit.md §2.1 — set Yoast og_image = the post's featured image, sitewide.
 *
 * One-off maintenance script (standalone CLI tool). It is NEVER loaded by
 * WordPress or the theme; keeping it in scripts/ is safe because of the
 * CLI-only guard below.
 *
 * Recipe (audit.md §2.1, verbatim):
 *   1. List published posts (standard WP REST /wp/v2/posts).
 *   2. Post has a featured image → set Yoast's per-post og_image to the
 *      featured image's original-file URL via the connected API
 *      (POST ewpa/yoast-update-seo, og_image param).
 *   3. Post has NO featured image → report only. Fix for those is the
 *      one-time global default image in Yoast admin:
 *      SEO → Social → Facebook → "Default settings image".
 *   4. After each write, clear the per-post cache (POST ewpa/clear-cache)
 *      because direct postmeta writes do not fire save_post.
 *
 * Usage:
 *   php scripts/set-og-images.php                     dry run (default)
 *   php scripts/set-og-images.php --apply             write og_image + clear cache
 *   php scripts/set-og-images.php --apply --limit=3   smoke test, first 3 posts
 *   php scripts/set-og-images.php --post-id=12309     single post only
 *
 * Config (environment variables, nothing hard-coded):
 *   V5_API_BASE    REST base URL, e.g. https://imraan.in/wp-json   (required)
 *   V5_API_TOKEN   bearer token                                   (optional)
 *   V5_API_USER    basic-auth user  ┐ alternative to a token       (optional)
 *   V5_API_PASS    basic-auth pass  ┘                              (optional)
 *
 * Safety:
 *   - Dry run by default; nothing is written without --apply.
 *   - Idempotent: og_image is always set to the post's own featured image
 *     URL, so re-running is safe and converges to the same values.
 *   - 150ms pause between writes, 2 attempts on transient 5xx/transport
 *     errors, per-post failures never abort the batch.
 *   - If your ewpa build expects a different key than `post_id`, change it
 *     ONLY in yoast_update_og_image() / clear_post_cache() below.
 *
 * Exit codes: 0 clean · 1 some posts failed · 2 config/usage error.
 */

if ( PHP_SAPI !== 'cli' ) {
	die( "This script may only be run from the command line.\n" );
}

$BASE  = rtrim( (string) getenv( 'V5_API_BASE' ), '/' );
$TOKEN = (string) getenv( 'V5_API_TOKEN' );
$USER  = (string) getenv( 'V5_API_USER' );
$PASS  = (string) getenv( 'V5_API_PASS' );

$OPTS   = getopt( '', [ 'apply', 'limit::', 'post-id::', 'help' ] );
$APPLY  = array_key_exists( 'apply', $OPTS );
$LIMIT  = ( isset( $OPTS['limit'] ) && false !== $OPTS['limit'] ) ? (int) $OPTS['limit'] : 0;
$POSTID = ( isset( $OPTS['post-id'] ) && false !== $OPTS['post-id'] ) ? (int) $OPTS['post-id'] : 0;

if ( array_key_exists( 'help', $OPTS ) ) {
	echo "Usage: php scripts/set-og-images.php [--apply] [--limit=N] [--post-id=N]\n" .
		"Config: V5_API_BASE (required), V5_API_TOKEN or V5_API_USER+V5_API_PASS\n" .
		"Default is a dry run; pass --apply to write og_image and clear caches.\n";
	exit( 0 );
}

$AUTH = [];
if ( '' !== $TOKEN ) {
	$AUTH[] = 'Authorization: Bearer ' . $TOKEN;
} elseif ( '' !== $USER && '' !== $PASS ) {
	$AUTH[] = 'Authorization: Basic ' . base64_encode( $USER . ':' . $PASS );
}

// ---------------------------------------------------------------- http ----

/**
 * One HTTP request, JSON in/out. Throws RuntimeException on transport errors.
 *
 * @return array{0:int,1:mixed} [ http status, decoded JSON body ]
 */
function http_json( $method, $url, $data, array $auth ) {
	$headers = array_merge( [ 'Accept: application/json', 'Content-Type: application/json' ], $auth );
	$payload = null === $data ? null : json_encode( $data );

	if ( function_exists( 'curl_init' ) ) {
		$ch = curl_init( $url );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $method );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		if ( null !== $payload ) {
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
		}
		$body = curl_exec( $ch );
		if ( false === $body ) {
			$err = curl_error( $ch );
			curl_close( $ch );
			throw new RuntimeException( 'transport error: ' . $err );
		}
		$status = (int) curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
		curl_close( $ch );
	} else {
		$context = stream_context_create( [
			'http' => [
				'method'        => $method,
				'header'        => implode( "\r\n", $headers ),
				'content'       => (string) $payload,
				'timeout'       => 30,
				'ignore_errors' => true,
			],
		] );
		$body = @file_get_contents( $url, false, $context );
		if ( false === $body ) {
			$err = error_get_last();
			$msg = isset( $err['message'] ) ? $err['message'] : 'request failed';
			throw new RuntimeException( 'transport error: ' . $msg );
		}
		$status = 0;
		if ( ! empty( $http_response_header )
			&& preg_match( '#HTTP/\S+\s+(\d{3})#', $http_response_header[0], $m ) ) {
			$status = (int) $m[1];
		}
	}

	return [ $status, json_decode( (string) $body, true ) ];
}

/**
 * http_json plus one retry on transient failures (transport errors, HTTP 5xx).
 *
 * @return array{0:int,1:mixed}
 */
function api_call( $method, $url, $data, array $auth ) {
	for ( $attempt = 1; $attempt <= 2; $attempt++ ) {
		try {
			[ $status, $decoded ] = http_json( $method, $url, $data, $auth );
		} catch ( RuntimeException $e ) {
			if ( 2 === $attempt ) {
				throw $e;
			}
			sleep( 2 );
			continue;
		}
		if ( $status >= 500 && 1 === $attempt ) {
			sleep( 2 );
			continue;
		}
		return [ $status, $decoded ];
	}
	throw new RuntimeException( 'unreachable' );
}

// ------------------------------------------------------ ewpa endpoints ---
// audit.md §2.1: og_image is settable per-post via ewpa/yoast-update-seo.
// If the connected API expects a different post key (e.g. `id`), adjust
// ONLY these two functions.

function yoast_update_og_image( $base, array $auth, $post_id, $image_url ) {
	return api_call(
		'POST',
		$base . '/ewpa/yoast-update-seo',
		[
			'post_id' => (int) $post_id,
			'og_image' => $image_url,
		],
		$auth
	);
}

function clear_post_cache( $base, array $auth, $post_id ) {
	return api_call(
		'POST',
		$base . '/ewpa/clear-cache',
		[ 'post_id' => (int) $post_id ],
		$auth
	);
}

// ----------------------------------------------------------- REST reads ---

/**
 * All published posts (id, slug, link, featured_media), or one post by id.
 *
 * @return array
 */
function fetch_posts( $base, array $auth, $single_id = 0 ) {
	if ( $single_id ) {
		[ $status, $body ] = api_call(
			'GET',
			$base . '/wp/v2/posts/' . (int) $single_id . '?_fields=id,slug,link,featured_media',
			null,
			$auth
		);
		if ( 200 !== $status || empty( $body['id'] ) ) {
			throw new RuntimeException( "post $single_id lookup failed (HTTP $status)" );
		}
		return [ $body ];
	}

	$all  = [];
	$page = 1;
	while ( true ) {
		[ $status, $batch ] = api_call(
			'GET',
			$base . '/wp/v2/posts?status=publish&per_page=100&_fields=id,slug,link,featured_media&page=' . $page,
			null,
			$auth
		);
		if ( 200 !== $status || ! is_array( $batch ) ) {
			throw new RuntimeException( "posts list failed (HTTP $status, page $page)" );
		}
		foreach ( $batch as $p ) {
			$all[] = $p;
		}
		if ( count( $batch ) < 100 ) {
			break;
		}
		$page++;
	}
	return $all;
}

/**
 * Original-file URL of one media item, or '' when it cannot be resolved.
 */
function fetch_media_url( $base, array $auth, $media_id ) {
	try {
		[ $status, $body ] = api_call(
			'GET',
			$base . '/wp/v2/media/' . (int) $media_id . '?_fields=source_url',
			null,
			$auth
		);
	} catch ( RuntimeException $e ) {
		return '';
	}
	if ( 200 !== $status || ! is_array( $body ) || empty( $body['source_url'] ) ) {
		return '';
	}
	return (string) $body['source_url'];
}

// ---------------------------------------------------------------- main ----

$mode      = $APPLY ? 'APPLY (writing og_image + clearing cache)' : 'DRY RUN (pass --apply to write)';
$auth_desc = $AUTH ? ( $TOKEN ? 'bearer token' : 'basic auth' ) : 'NONE (reads may work; writes need credentials)';

fwrite( STDOUT, "v5imraan OG-image batch fix - audit.md section 2.1\n" );
fwrite( STDOUT, "Mode:     $mode\n" );
fwrite( STDOUT, "API base: $BASE\n" );
fwrite( STDOUT, "Auth:     $auth_desc\n" );
fwrite( STDOUT, 'Target:   ' . ( $POSTID ? "post #$POSTID only" : 'all published posts' ) . "\n\n" );

if ( '' === $BASE ) {
	fwrite( STDERR, "ERROR: set V5_API_BASE (e.g. https://imraan.in/wp-json).\n" );
	exit( 2 );
}
if ( $APPLY && ! $AUTH ) {
	fwrite( STDERR, "ERROR: --apply needs credentials: set V5_API_TOKEN or V5_API_USER+V5_API_PASS.\n" );
	exit( 2 );
}

try {
	$posts = fetch_posts( $BASE, $AUTH, $POSTID );
} catch ( RuntimeException $e ) {
	fwrite( STDERR, 'ERROR: ' . $e->getMessage() . "\n" );
	exit( 2 );
}

$total = count( $posts );
if ( 0 === $total ) {
	fwrite( STDOUT, "No published posts matched. Nothing to do.\n" );
	exit( 0 );
}
fwrite( STDOUT, "Found $total published post(s).\n\n" );

$done = 0;
$missing = [];
$no_url = [];
$failed = [];
$cache_failed = [];

foreach ( $posts as $i => $p ) {
	$n     = $i + 1;
	$id    = (int) $p['id'];
	$slug  = isset( $p['slug'] ) ? (string) $p['slug'] : '';
	$media = isset( $p['featured_media'] ) ? (int) $p['featured_media'] : 0;
	$tag   = sprintf( '[%3d/%d] #%-6d %-42s', $n, $total, $id, $slug );

	if ( 0 === $media ) {
		$missing[] = "#$id $slug";
		fwrite( STDOUT, "$tag no featured image -> needs global Yoast default\n" );
		continue;
	}

	$image_url = fetch_media_url( $BASE, $AUTH, $media );
	if ( '' === $image_url ) {
		$no_url[] = "#$id $slug";
		fwrite( STDOUT, "$tag FAILED to resolve media #$media URL\n" );
		continue;
	}

	if ( ! $APPLY ) {
		$done++;
		fwrite( STDOUT, "$tag would set og_image = $image_url\n" );
	} else {
		try {
			[ $status, $resp ] = yoast_update_og_image( $BASE, $AUTH, $id, $image_url );
		} catch ( RuntimeException $e ) {
			$failed[] = "#$id $slug (" . $e->getMessage() . ')';
			fwrite( STDOUT, "$tag FAILED: " . $e->getMessage() . "\n" );
			continue;
		}
		if ( $status >= 400 ) {
			$failed[] = "#$id $slug (HTTP $status)";
			fwrite( STDOUT, "$tag FAILED: HTTP $status from ewpa/yoast-update-seo\n" );
			continue;
		}

		$cache_note = '';
		try {
			[ $c_status ] = clear_post_cache( $BASE, $AUTH, $id );
			if ( $c_status >= 400 ) {
				$cache_failed[] = "#$id (HTTP $c_status)";
				$cache_note = ' [cache clear HTTP ' . $c_status . ']';
			}
		} catch ( RuntimeException $e ) {
			$cache_failed[] = "#$id (" . $e->getMessage() . ')';
			$cache_note = ' [cache clear failed]';
		}

		$done++;
		fwrite( STDOUT, "$tag og_image SET = $image_url$cache_note\n" );
		usleep( 150000 );
	}

	if ( $LIMIT > 0 && $done >= $LIMIT ) {
		fwrite( STDOUT, "\nStopped early: --limit=$LIMIT reached.\n" );
		break;
	}
}

// -------------------------------------------------------------- summary ---

fwrite( STDOUT, "\n--- Summary (audit.md section 2.1) ---\n" );
fwrite( STDOUT, sprintf( "Posts processed:   %d\n", $total ) );
fwrite( STDOUT, sprintf( "%s%s: %d\n", $APPLY ? 'og_image written' : 'Would set og_image', '', $done ) );
if ( $missing ) {
	fwrite( STDOUT, sprintf( "No featured image: %d -> set the global Yoast default image (SEO -> Social -> Facebook -> \"Default settings image\"):\n", count( $missing ) ) );
	foreach ( $missing as $m ) {
		fwrite( STDOUT, "    $m\n" );
	}
}
if ( $no_url ) {
	fwrite( STDOUT, sprintf( "Media URL unresolved: %d (check the uploads, then re-run):\n", count( $no_url ) ) );
	foreach ( $no_url as $m ) {
		fwrite( STDOUT, "    $m\n" );
	}
}
if ( $failed ) {
	fwrite( STDOUT, sprintf( "Write failures:      %d:\n", count( $failed ) ) );
	foreach ( $failed as $m ) {
		fwrite( STDOUT, "    $m\n" );
	}
}
if ( $cache_failed ) {
	fwrite( STDOUT, sprintf( "Cache-clear issues:  %d (og_image written; flush site cache manually):\n", count( $cache_failed ) ) );
	foreach ( $cache_failed as $m ) {
		fwrite( STDOUT, "    $m\n" );
	}
}
if ( ! $APPLY ) {
	fwrite( STDOUT, "\nDRY RUN complete - nothing was written. Re-run with --apply to write.\n" );
}

exit( $failed ? 1 : 0 );