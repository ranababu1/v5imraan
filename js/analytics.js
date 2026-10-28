/**
 * v5imraan GA4 custom events.
 *
 * Sends events through gtag() when Site Kit has loaded it, otherwise
 * queues them on dataLayer in gtag format so a late gtag.js still picks
 * them up. Every event is also pushed to dataLayer as a plain
 * { event: 'v5.<name>', ...params } object for GTM triggers.
 * GA4 itself applies Google consent mode, so nothing here bypasses it.
 *
 * Events: scroll_depth, copy_code, share, sign_up, generate_lead,
 * select_content, page_not_found, web_vitals, search.
 *
 * Config comes from window.v5Analytics (inc/analytics.php).
 * Debug: add ?v5debug=1 to any URL to log events and flag them for
 * GA4 DebugView.
 */
( function () {
	'use strict';

	var cfg = window.v5Analytics || {};
	var debug = /[?&]v5debug=1(?:&|$)/.test( window.location.search );
	var started = false;

	/* ---------------------------------------------------------------
	 * Sending.
	 * ------------------------------------------------------------- */
	function queueGtag() {
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push( arguments );
	}

	function track( name, params ) {
		var data = {};
		var key;

		params = params || {};
		for ( key in params ) {
			if ( Object.prototype.hasOwnProperty.call( params, key ) && null !== params[ key ] && undefined !== params[ key ] ) {
				data[ key ] = params[ key ];
			}
		}
		if ( debug ) {
			data.debug_mode = true;
		}

		try {
			if ( 'function' === typeof window.gtag ) {
				window.gtag( 'event', name, data );
			} else {
				queueGtag( 'event', name, data );
			}
		} catch ( e ) {}

		try {
			var layer = { event: 'v5.' + name };
			for ( key in data ) {
				if ( Object.prototype.hasOwnProperty.call( data, key ) ) {
					layer[ key ] = data[ key ];
				}
			}
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push( layer );
		} catch ( e ) {}

		if ( debug && window.console ) {
			window.console.log( '[v5 analytics]', name, data );
		}
	}

	window.v5Track = track;

	function closest( el, selector ) {
		while ( el && 1 === el.nodeType ) {
			if ( ( el.matches || el.msMatchesSelector ).call( el, selector ) ) {
				return el;
			}
			el = el.parentNode;
		}
		return null;
	}

	function postTitle() {
		return cfg.postTitle || document.title;
	}

	/* ---------------------------------------------------------------
	 * scroll_depth: 25 / 50 / 75 % of the article body (single posts).
	 * GA4 enhanced measurement already reports the 90% page scroll.
	 * ------------------------------------------------------------- */
	function initScrollDepth() {
		if ( ! cfg.isSingle ) {
			return;
		}
		var content = document.querySelector( '.sp-content' );
		var marks = [ 25, 50, 75 ];
		var ticking = false;

		function depth() {
			var doc = document.documentElement;
			var bottom = window.scrollY + window.innerHeight;
			if ( content ) {
				var rect = content.getBoundingClientRect();
				var top = rect.top + window.scrollY;
				return rect.height > 0 ? ( ( bottom - top ) / rect.height ) * 100 : 0;
			}
			return doc.scrollHeight > 0 ? ( bottom / doc.scrollHeight ) * 100 : 0;
		}

		function check() {
			ticking = false;
			var pct = depth();
			while ( marks.length && pct >= marks[ 0 ] ) {
				track( 'scroll_depth', { percent_scrolled: marks.shift() } );
			}
			if ( ! marks.length ) {
				window.removeEventListener( 'scroll', onScroll );
			}
		}

		function onScroll() {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( check );
			}
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		// Short posts can already be past a mark without any scrolling;
		// wait until the reader has been on the page for a moment.
		window.setTimeout( check, 2000 );
	}

	/* ---------------------------------------------------------------
	 * Copy button on code blocks + copy_code.
	 * ------------------------------------------------------------- */
	function codeLanguage( pre ) {
		var code = pre.querySelector( 'code' );
		var sources = [ pre, code ];
		var i;
		var m;

		for ( i = 0; i < sources.length; i++ ) {
			var el = sources[ i ];
			if ( ! el ) {
				continue;
			}
			var attr = el.getAttribute( 'data-lang' ) || el.getAttribute( 'data-language' );
			if ( attr ) {
				return attr.toLowerCase();
			}
			m = /(?:^|\s)(?:language|lang)-([\w#+.-]+)/.exec( el.className || '' ) || /brush:\s*([\w#+-]+)/.exec( el.className || '' );
			if ( m ) {
				return m[ 1 ].toLowerCase();
			}
		}

		// The classic editor's "Code block" format adds no language class,
		// so fall back to a light guess from the first lines.
		var text = ( ( code || pre ).textContent || '' ).replace( /^\s+/, '' ).slice( 0, 600 );
		if ( /^<\?php/.test( text ) ) {
			return 'php';
		}
		if ( /^[{[]\s*["{[\]}]/.test( text ) ) {
			return 'json'; // Includes MongoDB shell output (extended JSON).
		}
		if ( /^(select|insert|update|delete|create|alter|with|drop)\s/i.test( text ) ) {
			return 'sql';
		}
		if ( /^(\$ |# |sudo |npm |npx |yarn |pnpm |pip3? |curl |wget |git |docker |kubectl |helm |mongosh|mongo |mongodump|mongorestore|mongoimport|mongoexport|dotnet |cd |ls |export |brew |apt(-get)? )/m.test( text ) ) {
			return 'shell';
		}
		if ( /using System|Console\.Write|async Task|namespace [\w.]+|\bpublic (sealed |static |partial )*class\b[^{]*\{[\s\S]*\bget;/.test( text ) ) {
			return 'csharp';
		}
		if ( /^</.test( text ) ) {
			return /<(html|head|body|div|span|p|a|script|style|table)[\s>]/i.test( text ) ? 'html' : 'xml';
		}
		if ( /\bdb\.\w+|\b(const|let|var)\s+\w+\s*=|=>|\bfunction\b|require\(|console\.log|\bawait\b/.test( text ) ) {
			return 'javascript';
		}
		if ( /^[\w.-]+:(\s|$)/m.test( text ) && ! /[{};]/.test( text ) ) {
			return 'yaml';
		}
		return 'plain';
	}

	function copyText( text ) {
		if ( navigator.clipboard && navigator.clipboard.writeText && window.isSecureContext ) {
			return navigator.clipboard.writeText( text );
		}
		return new Promise( function ( resolve, reject ) {
			var field = document.createElement( 'textarea' );
			field.value = text;
			field.setAttribute( 'readonly', '' );
			field.style.position = 'fixed';
			field.style.left = '-9999px';
			document.body.appendChild( field );
			field.select();
			var ok = false;
			try {
				ok = document.execCommand( 'copy' );
			} catch ( e ) {}
			document.body.removeChild( field );
			if ( ok ) {
				resolve();
			} else {
				reject();
			}
		} );
	}

	function initCopyButtons() {
		var blocks = document.querySelectorAll( '.sp-content pre, .entry-content pre' );
		Array.prototype.forEach.call( blocks, function ( pre ) {
			if ( pre.parentNode && pre.parentNode.classList && pre.parentNode.classList.contains( 'v5-code' ) ) {
				return;
			}
			var wrap = document.createElement( 'div' );
			wrap.className = 'v5-code';
			pre.parentNode.insertBefore( wrap, pre );
			wrap.appendChild( pre );

			var button = document.createElement( 'button' );
			button.type = 'button';
			button.className = 'v5-code__copy';
			button.setAttribute( 'aria-label', 'Copy code to clipboard' );
			button.innerHTML = '<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" focusable="false"><path fill="currentColor" d="M16 1H4a2 2 0 0 0-2 2v14h2V3h12V1zm3 4H8a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H8V7h11v14z"/></svg><span class="v5-code__label">Copy</span>';

			var status = document.createElement( 'span' );
			status.className = 'screen-reader-text';
			status.setAttribute( 'role', 'status' );
			status.setAttribute( 'aria-live', 'polite' );

			wrap.appendChild( button );
			wrap.appendChild( status );

			var label = button.querySelector( '.v5-code__label' );
			var timer;

			button.addEventListener( 'click', function () {
				var code = pre.querySelector( 'code' ) || pre;
				var text = code.textContent || '';

				copyText( text ).then(
					function () {
						button.classList.add( 'is-copied' );
						label.textContent = 'Copied';
						status.textContent = 'Code copied to clipboard';
						window.clearTimeout( timer );
						timer = window.setTimeout( function () {
							button.classList.remove( 'is-copied' );
							label.textContent = 'Copy';
							status.textContent = '';
						}, 2000 );
						track( 'copy_code', {
							post_title: postTitle(),
							code_language: codeLanguage( pre ),
						} );
					},
					function () {
						label.textContent = 'Press Ctrl+C';
						var range = document.createRange();
						range.selectNodeContents( code );
						var sel = window.getSelection();
						sel.removeAllRanges();
						sel.addRange( range );
					}
				);
			} );
		} );
	}

	/* ---------------------------------------------------------------
	 * Clicks: share, select_content.
	 * ------------------------------------------------------------- */
	function sameSitePath( link ) {
		var url;
		try {
			url = new URL( link.getAttribute( 'href' ), window.location.href );
		} catch ( e ) {
			return null;
		}
		if ( ! /^https?:$/.test( url.protocol ) || url.hostname.replace( /^www\./, '' ) !== window.location.hostname.replace( /^www\./, '' ) ) {
			return null;
		}
		// Ignore same-page anchors (table of contents, footnotes).
		if ( url.pathname === window.location.pathname && url.search === window.location.search ) {
			return null;
		}
		if ( /^\/wp-(admin|login)/.test( url.pathname ) ) {
			return null;
		}
		return url;
	}

	function onLinkActivate( event ) {
		if ( 'auxclick' === event.type && 1 !== event.button ) {
			return;
		}
		var target = event.target;

		var shareBtn = closest( target, '[data-share-method]' );
		if ( shareBtn ) {
			track( 'share', {
				method: shareBtn.getAttribute( 'data-share-method' ),
				content_type: 'article',
				item_id: cfg.postId ? String( cfg.postId ) : window.location.pathname,
			} );
			return;
		}

		var link = closest( target, 'a[href]' );
		if ( ! link ) {
			return;
		}

		var type = null;
		if ( closest( link, '.sp-related' ) ) {
			type = 'related';
		} else if ( closest( link, '.sp-postnav' ) ) {
			type = 'post_nav';
		} else if ( closest( link, '.sp-content [class*="series"], .sp-content [id*="series"], .series-nav, .post-series' ) ) {
			type = 'series';
		} else if ( closest( link, '.sp-content, .entry-content' ) ) {
			type = 'internal_link';
		}
		if ( ! type ) {
			return;
		}

		var url = sameSitePath( link );
		if ( ! url ) {
			return;
		}
		track( 'select_content', {
			content_type: type,
			link_url: url.href,
		} );
	}

	/* ---------------------------------------------------------------
	 * sign_up: comment submitted with a Jetpack "notify me" box ticked.
	 * ------------------------------------------------------------- */
	function onSubmit( event ) {
		var form = event.target;
		if ( ! form || 'commentform' !== form.id ) {
			return;
		}
		var comments = form.querySelector( '#subscribe_comments, input[name="subscribe_comments"]' );
		var blog = form.querySelector( '#subscribe_blog, input[name="subscribe_blog"]' );
		var types = [];
		if ( comments && comments.checked ) {
			types.push( 'comments' );
		}
		if ( blog && blog.checked ) {
			types.push( 'blog' );
		}
		if ( ! types.length ) {
			return;
		}
		track( 'sign_up', {
			method: 'comment_notify',
			subscription_type: types.join( '+' ),
		} );
	}

	/* ---------------------------------------------------------------
	 * generate_lead: Jetpack contact form success, either after the
	 * classic redirect (?contact-form-sent=) or the AJAX success state
	 * (.contact-form-ajax-submission gains .submission-success).
	 * ------------------------------------------------------------- */
	function initLeads() {
		var params = new URLSearchParams( window.location.search );
		var fired = false;

		function fire( key, formId ) {
			if ( fired ) {
				return;
			}
			fired = true;
			if ( key ) {
				try {
					var storeKey = 'v5_lead_' + key;
					if ( window.sessionStorage.getItem( storeKey ) ) {
						return;
					}
					window.sessionStorage.setItem( storeKey, '1' );
				} catch ( e ) {}
			}
			track( 'generate_lead', { form: 'contact', form_id: formId || undefined } );
		}

		if ( params.get( 'contact-form-sent' ) ) {
			fire( params.get( 'contact-form-sent' ), params.get( 'contact-form-id' ) );
			return;
		}

		var done = document.querySelector( '.contact-form-ajax-submission.submission-success, .contact-form-submission:not(.contact-form-ajax-submission), #contact-form-success-header' );
		if ( done ) {
			fire( null, null );
			return;
		}

		var ajax = document.querySelectorAll( '.contact-form-ajax-submission' );
		if ( ! ajax.length || ! ( 'MutationObserver' in window ) ) {
			return;
		}
		var observer = new MutationObserver( function ( mutations ) {
			mutations.forEach( function ( m ) {
				if ( m.target.classList && m.target.classList.contains( 'submission-success' ) ) {
					var id = ( m.target.id || '' ).replace( 'contact-form-success-', '' );
					fire( null, id || null );
					observer.disconnect();
				}
			} );
		} );
		Array.prototype.forEach.call( ajax, function ( el ) {
			observer.observe( el, { attributes: true, attributeFilter: [ 'class' ] } );
		} );
	}

	/* ---------------------------------------------------------------
	 * page_not_found.
	 * ------------------------------------------------------------- */
	function init404() {
		if ( cfg.is404 || document.body.classList.contains( 'error404' ) ) {
			track( 'page_not_found', {
				page_location: window.location.href,
				page_referrer: document.referrer || '(direct)',
			} );
		}
	}

	/* ---------------------------------------------------------------
	 * search: an Algolia autocomplete suggestion was picked.
	 * The WP Search with Algolia plugin's autocomplete.js fires a
	 * bubbling DOM event 'autocomplete:selected' on the input and sets
	 * the input to the suggestion first, so remember what was typed.
	 * ------------------------------------------------------------- */
	function initAlgolia() {
		var typed = '';
		document.addEventListener(
			'input',
			function ( event ) {
				var t = event.target;
				if ( t && t.tagName === 'INPUT' && ( 'search' === t.type || 's' === t.name || ( t.className && /\baa-input\b/.test( t.className ) ) ) ) {
					typed = t.value;
				}
			},
			true
		);
		document.addEventListener(
			'autocomplete:selected',
			function ( event ) {
				var input = event.target;
				var term = typed || ( input && input.value ) || '';
				var suggestion = event._args && event._args[ 0 ];
				track( 'search', {
					search_term: String( term ).trim().slice( 0, 100 ),
					search_type: 'autocomplete',
					link_url: suggestion && suggestion.permalink ? suggestion.permalink : undefined,
				} );
			},
			true
		);
	}

	/* ---------------------------------------------------------------
	 * Core Web Vitals (LCP, CLS, INP) from the self-hosted web-vitals
	 * build, loaded after window load so it never competes with the page.
	 * ------------------------------------------------------------- */
	function sendVital( metric ) {
		track( 'web_vitals', {
			metric_name: metric.name,
			value: Math.round( 'CLS' === metric.name ? metric.value * 1000 : metric.value ),
			metric_rating: metric.rating,
			metric_id: metric.id,
		} );
	}

	function initWebVitals() {
		if ( ! cfg.webVitalsSrc ) {
			return;
		}
		function load() {
			var s = document.createElement( 'script' );
			s.src = cfg.webVitalsSrc;
			s.async = true;
			s.onload = function () {
				var wv = window.webVitals;
				if ( ! wv ) {
					return;
				}
				wv.onLCP( sendVital );
				wv.onCLS( sendVital );
				wv.onINP( sendVital );
			};
			document.head.appendChild( s );
		}
		var schedule = function () {
			if ( 'requestIdleCallback' in window ) {
				window.requestIdleCallback( load, { timeout: 3000 } );
			} else {
				window.setTimeout( load, 1 );
			}
		};
		if ( 'complete' === document.readyState ) {
			schedule();
		} else {
			window.addEventListener( 'load', schedule );
		}
	}

	/* ---------------------------------------------------------------
	 * Start.
	 * ------------------------------------------------------------- */
	function start() {
		if ( started ) {
			return;
		}
		started = true;
		document.addEventListener( 'click', onLinkActivate, true );
		document.addEventListener( 'auxclick', onLinkActivate, true );
		document.addEventListener( 'submit', onSubmit, true );
		initAlgolia();
		initCopyButtons();
		initScrollDepth();
		initLeads();
		init404();
		initWebVitals();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
} )();
