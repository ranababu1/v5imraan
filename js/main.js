/**
 * v5imraan theme interactions:
 * reading-progress bar, table of contents + scroll spy, copy-link
 * share buttons and homepage award detail switching.
 *
 * Progressive enhancement only — every feature degrades gracefully
 * and the important SEO/AIO markup is rendered in PHP, not here.
 */
( function () {
	'use strict';

	/* ---------------------------------------------------------------
	 * Reading progress bar (single post).
	 * ------------------------------------------------------------- */
	var progressBar = document.getElementById( 'sp-progress-bar' );

	if ( progressBar ) {
		var progressTicking = false;

		var updateProgress = function () {
			var doc = document.documentElement;
			var max = doc.scrollHeight - window.innerHeight;
			var pct = max > 0 ? ( window.scrollY / max ) * 100 : 0;
			progressBar.style.width = Math.max( 0, Math.min( 100, pct ) ) + '%';
			progressTicking = false;
		};

		window.addEventListener(
			'scroll',
			function () {
				if ( ! progressTicking ) {
					window.requestAnimationFrame( updateProgress );
					progressTicking = true;
				}
			},
			{ passive: true }
		);

		updateProgress();
	}

	/* ---------------------------------------------------------------
	 * Table of contents + scroll spy (single post).
	 * Server-side heading anchors (inc/blog.php) provide the ids.
	 * ------------------------------------------------------------- */
	var tocList = document.getElementById( 'sp-toc' );

	if ( tocList ) {
		var headings = Array.prototype.slice.call(
			document.querySelectorAll( '.sp-content h2[id], .sp-content h3[id]' )
		);

		if ( headings.length ) {
			var links = [];

			headings.forEach( function ( heading ) {
				var item = document.createElement( 'li' );
				item.className =
					'sp-toc__item' + ( heading.tagName === 'H3' ? ' sp-toc__item--sub' : '' );

				var link = document.createElement( 'a' );
				link.href = '#' + heading.id;
				link.textContent = heading.textContent;

				link.addEventListener( 'click', function () {
					window.setTimeout( function () {
						heading.setAttribute( 'tabindex', '-1' );
						heading.focus( { preventScroll: true } );
					}, 60 );
				} );

				item.appendChild( link );
				tocList.appendChild( item );
				links.push( { link: link, heading: heading } );
			} );

			var spyTicking = false;

			var updateSpy = function () {
				var current = null;

				headings.forEach( function ( heading ) {
					if ( heading.getBoundingClientRect().top <= 130 ) {
						current = heading.id;
					}
				} );

				links.forEach( function ( entry ) {
					entry.link.classList.toggle( 'is-active', entry.heading.id === current );
				} );

				spyTicking = false;
			};

			window.addEventListener(
				'scroll',
				function () {
					if ( ! spyTicking ) {
						window.requestAnimationFrame( updateSpy );
						spyTicking = true;
					}
				},
				{ passive: true }
			);

			updateSpy();
		} else {
			var wrap = tocList.closest( '.sp-toc-wrap' );

			if ( wrap ) {
				wrap.parentNode.removeChild( wrap );
			}
		}
	}
	/* ---------------------------------------------------------------
	 * Copy-link share buttons.
	 * ------------------------------------------------------------- */
	Array.prototype.forEach.call(
		document.querySelectorAll( '[data-share-copy]' ),
		function ( button ) {
			button.addEventListener( 'click', function () {
				var url = window.location.href;

				var done = function () {
					var originalLabel = button.getAttribute( 'data-original-label' );

					if ( ! originalLabel ) {
						button.setAttribute( 'data-original-label', button.getAttribute( 'aria-label' ) );
					}

					button.classList.add( 'is-copied' );
					button.setAttribute( 'aria-label', 'Link copied!' );
					window.setTimeout( function () {
						button.classList.remove( 'is-copied' );
						button.setAttribute( 'aria-label', button.getAttribute( 'data-original-label' ) );
					}, 2000 );
				};

				var fallback = function () {
					var field = document.createElement( 'textarea' );
					field.value = url;
					field.setAttribute( 'readonly', '' );
					field.style.position = 'fixed';
					field.style.left = '-9999px';
					document.body.appendChild( field );
					field.select();
					try {
						document.execCommand( 'copy' );
					} catch ( e ) {}
					document.body.removeChild( field );
					done();
				};

				if ( navigator.clipboard && navigator.clipboard.writeText ) {
					navigator.clipboard.writeText( url ).then( done, fallback );
				} else {
					fallback();
				}
			} );
		}
	);

	/* ---------------------------------------------------------------
	 * Homepage awards: hover/click/keyboard detail switcher.
	 * Replaces the old inline onmouseover handlers.
	 * ------------------------------------------------------------- */
	var awardTriggers = document.querySelectorAll( '.js-award' );

	if ( awardTriggers.length ) {
		var detailPanels = document.querySelectorAll( '.award-details' );

		var activateAward = function ( awardId ) {
			Array.prototype.forEach.call( detailPanels, function ( panel ) {
				panel.style.display = panel.id === awardId + 'details' ? 'block' : 'none';
			} );
			Array.prototype.forEach.call( awardTriggers, function ( trigger ) {
				var isActive = trigger.getAttribute( 'data-award' ) === awardId;
				trigger.classList.toggle( 'is-active', isActive );
				trigger.setAttribute( 'aria-pressed', isActive ? 'true' : 'false' );
			} );
		};

		Array.prototype.forEach.call( awardTriggers, function ( trigger ) {
			var awardId = trigger.getAttribute( 'data-award' );

			trigger.addEventListener( 'mouseenter', function () {
				activateAward( awardId );
			} );

			trigger.addEventListener( 'click', function () {
				activateAward( awardId );
			} );

			trigger.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Enter' || event.key === ' ' || event.key === 'Spacebar' ) {
					event.preventDefault();
					activateAward( awardId );
				}
			} );
		} );

		activateAward( awardTriggers[ 0 ].getAttribute( 'data-award' ) );
	}
} )();