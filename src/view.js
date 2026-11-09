/* global MutationObserver, Splide */
/**
 * Frontend hydration: mount LogoSoup on strip and carousel markup.
 *
 * No React here on purpose: importing wp-element or JSX makes WordPress load
 * React on every page with a logo strip.
 */

import { mountSoupDom } from './shared/mount-soup-dom';

const mounted = new WeakMap();
const carouselGroups = new WeakSet();
const hydratedCarousels = new WeakSet();
const AUTOSCROLL_MARKER = 'logoSliderAutoscroll';
const HYDRATING_CLASS = 'cb-lw-hydrating';
const STANDALONE_SPLIDE_RETRY_MS = [ 0, 100, 500, 1500, 3000 ];
const HYDRATION_FAIL_OPEN_MS = 800;
const MIN_SLIDES_BEFORE_SPLIDE = 3;
const SPLIDE_REFRESH_DEBOUNCE_MS = 150;
const DEBUG_STORAGE_KEY = 'coopbolo-debug';
const LOG_PREFIX = '[Cooper Bold Logo Wrangler]';

/**
 * Whether opt-in frontend debug logging is enabled.
 *
 * @return {boolean}
 */
function isLWDebugEnabled() {
	if ( typeof window === 'undefined' ) {
		return false;
	}
	if ( window.COOPBOLO_DEBUG === true ) {
		return true;
	}
	try {
		if ( window.localStorage.getItem( DEBUG_STORAGE_KEY ) === '1' ) {
			return true;
		}
	} catch ( error ) {
		// localStorage may be blocked.
	}
	try {
		return (
			new URLSearchParams( window.location.search ).get(
				'coopbolo_debug'
			) === '1'
		);
	} catch ( error ) {
		return false;
	}
}

/**
 * @param {...*} args Log arguments when debug is enabled.
 */
function coopboLog( ...args ) {
	if ( ! isLWDebugEnabled() ) {
		return;
	}
	// eslint-disable-next-line no-console
	console.log( LOG_PREFIX, ...args );
}

/**
 * @param {...*} args Error when debug is enabled.
 */
function coopboDebugError( ...args ) {
	if ( ! isLWDebugEnabled() ) {
		return;
	}
	// eslint-disable-next-line no-console
	console.error( LOG_PREFIX, ...args );
}

/**
 * Log per-slide image load pass/fail counts.
 *
 * @param {HTMLElement[]} slides  Carousel slide nodes.
 * @param {string}        context Caller label for the check.
 */
function logSlidesImageStatus( slides, context ) {
	if ( ! isLWDebugEnabled() ) {
		return;
	}

	let pass = 0;
	let fail = 0;
	slides.forEach( ( slide ) => {
		const img = slide.querySelector( 'img' );
		if ( ! img || ! img.complete ) {
			fail++;
			return;
		}
		if (
			img.naturalWidth > 0 ||
			( typeof img.src === 'string' && img.src.startsWith( 'blob:' ) )
		) {
			pass++;
		} else {
			fail++;
		}
	} );
	coopboLog( 'slidesHaveLoadedImages', {
		context,
		pass,
		fail,
		total: slides.length,
	} );
}

/**
 * AutoScroll speed by viewport for standalone carousels.
 *
 * @return {number}
 */
function standaloneAutoscrollSpeed() {
	const w = window.innerWidth;
	if ( w < 480 ) {
		return 0.4;
	}
	if ( w < 768 ) {
		return 0.5;
	}
	if ( w < 992 ) {
		return 0.7;
	}
	return 0.9;
}

/**
 * Splide extension bundle when Auto Scroll is on the page.
 *
 * @return {object|undefined}
 */
function splideExtensions() {
	return window.splide && window.splide.Extensions
		? window.splide.Extensions
		: undefined;
}

/**
 * Whether this carousel was initialized by plugin autoscroll.
 *
 * @param {HTMLElement} carouselRoot Splide root element.
 * @return {boolean}
 */
function standaloneSplideMounted( carouselRoot ) {
	return carouselRoot.dataset[ AUTOSCROLL_MARKER ] === '1';
}

/**
 * Every slide must contain a loaded image before Splide mount.
 *
 * @param {HTMLElement[]} slides Carousel slide nodes.
 * @return {boolean}
 */
function slidesHaveLoadedImages( slides ) {
	if ( ! slides.length ) {
		coopboLog( 'slidesHaveLoadedImages', {
			slideCount: 0,
			ready: false,
		} );
		return false;
	}

	const ready = slides.every( ( slide ) => {
		const img = slide.querySelector( 'img' );
		if ( ! img || ! img.complete ) {
			return false;
		}
		if ( img.naturalWidth > 0 ) {
			return true;
		}
		// LogoSoup canvas output uses blob: URLs; accept when decoded.
		return typeof img.src === 'string' && img.src.startsWith( 'blob:' );
	} );

	coopboLog( 'slidesHaveLoadedImages', {
		slideCount: slides.length,
		ready,
	} );

	return ready;
}

/**
 * Logo row rendered by LogoSoup inside the hidden reference strip.
 *
 * @param {HTMLElement} refContainer Hidden reference mount.
 * @return {HTMLElement|null}
 */
function getLogoSoupRow( refContainer ) {
	const child = refContainer.firstElementChild;
	return child instanceof HTMLElement ? child : null;
}

/**
 * Whether Splide finished mounting on a carousel root.
 *
 * @param {HTMLElement} carouselRoot Splide root element.
 * @return {boolean}
 */
function carouselSplideInitialized( carouselRoot ) {
	return (
		standaloneSplideMounted( carouselRoot ) ||
		carouselRoot.classList.contains( 'is-initialized' )
	);
}

/**
 * Read config JSON from current or legacy data attributes.
 *
 * @param {HTMLElement} el Mount node.
 * @return {string} Raw JSON attribute value.
 */
function getConfigAttr( el ) {
	return el.getAttribute( 'data-cb-lw' ) || '';
}

/**
 * Mount LogoSoup on a strip container (idempotent per node).
 *
 * @param {HTMLElement} container Mount node with data-cb-lw.
 */
function mountLogoSoup( container ) {
	if ( mounted.has( container ) ) {
		return;
	}

	const raw = getConfigAttr( container );
	if ( ! raw ) {
		return;
	}

	let config;
	try {
		config = JSON.parse( raw );
	} catch ( error ) {
		coopboDebugError( 'mountLogoSoup JSON parse failed', {
			error: error instanceof Error ? error.message : String( error ),
		} );
		return;
	}

	const instance = mountSoupDom( container, config, {
		context: { surface: 'strip' },
	} );
	if ( instance ) {
		mounted.set( container, instance );
	}
}

/**
 * Render a hidden reference strip and clone normalized logos into carousel slides.
 *
 * @param {HTMLElement} refContainer Hidden reference strip mount.
 * @param {string}      groupId      Carousel group id shared with slide nodes.
 */
function mountCarouselReference( refContainer, groupId ) {
	if ( carouselGroups.has( refContainer ) ) {
		return;
	}
	carouselGroups.add( refContainer );

	const raw = getConfigAttr( refContainer );
	if ( ! raw ) {
		return;
	}

	let config;
	try {
		config = JSON.parse( raw );
	} catch ( error ) {
		coopboDebugError( 'mountCarouselReference JSON parse failed', {
			groupId,
			error: error instanceof Error ? error.message : String( error ),
		} );
		return;
	}

	if ( ! Array.isArray( config.logos ) || ! config.logos.length ) {
		return;
	}

	// Cache slide nodes before Splide loop clones duplicate data attributes.
	const slides = Array.from(
		document.querySelectorAll(
			`[data-cb-lw-slide][data-cb-lw-carousel="${ groupId }"]`
		)
	);
	if ( ! slides.length ) {
		coopboLog( 'mountCarouselReference: no slides', { groupId } );
		return;
	}

	const configLogoCount = config.logos.length;
	coopboLog( 'mountCarouselReference', {
		groupId,
		slideCount: slides.length,
		configLogoCount,
	} );

	mountSoupDom(
		refContainer,
		{ ...config, eagerLoad: true },
		{ context: { surface: 'carousel-ref', groupId } }
	);

	const carouselRoot =
		refContainer.closest( '[data-cb-lw-splide]' ) ||
		document.querySelector(
			`[data-cb-lw-carousel="${ groupId }"][data-cb-lw-splide]`
		);
	if ( carouselRoot ) {
		carouselRoot.classList.add( HYDRATING_CLASS );
	}

	const finishCarouselHydration = () => {
		if ( ! carouselRoot || hydratedCarousels.has( carouselRoot ) ) {
			return;
		}

		const snippetOwns = externalAutoscrollOwnsSplide( carouselRoot );
		coopboLog( 'externalAutoscrollOwnsSplide', {
			groupId,
			owns: snippetOwns,
			snippetScriptPresent: !! document.getElementById(
				'rapidsos-logo-slider-fix-js'
			),
			fixFlagPresent: !! window.__rapidsosLogoSliderFix,
		} );
		if ( ! snippetOwns ) {
			maybeInitStandaloneSplide( carouselRoot );
		} else {
			scheduleStandaloneSplideFallback( carouselRoot );
		}

		hydratedCarousels.add( carouselRoot );
		carouselRoot.dispatchEvent(
			new CustomEvent( 'coopbolo-hydrated', { bubbles: true } )
		);
		coopboLog( 'coopbolo-hydrated fired', { groupId } );
	};

	const distribute = () => {
		const row = getLogoSoupRow( refContainer );
		const logoNodes = row
			? Array.from( row.children ).filter(
					( node ) =>
						node.querySelector( 'img' ) || node.matches( 'img, a' )
			  )
			: [];

		slides.forEach( ( slide, index ) => {
			const item = logoNodes[ index ];
			if ( ! item || slide.children.length ) {
				return;
			}
			slide.appendChild( item.cloneNode( true ) );
		} );

		const filled = slides.filter(
			( slide ) => slide.children.length > 0
		).length;
		const complete =
			logoNodes.length >= slides.length &&
			slides.every( ( slide ) => slide.children.length > 0 );

		coopboLog( 'distribute', {
			groupId,
			logoNodes: logoNodes.length,
			slides: slides.length,
			filled,
			complete,
		} );

		return { filled, complete, logoNodes: logoNodes.length };
	};

	let splideStarted = false;
	let hydrationComplete = false;
	let observer = null;
	let failOpenTimer = null;
	let refreshTimer = null;

	const scheduleSplideRefresh = () => {
		if ( ! carouselRoot || ! carouselRoot.splide ) {
			return;
		}
		if ( refreshTimer ) {
			window.clearTimeout( refreshTimer );
		}
		refreshTimer = window.setTimeout( () => {
			refreshTimer = null;
			try {
				carouselRoot.splide.refresh();
				coopboLog( 'splide refresh after progressive fill', {
					groupId,
				} );
			} catch ( error ) {
				coopboDebugError( 'splide refresh failed', {
					groupId,
					error:
						error instanceof Error
							? error.message
							: String( error ),
				} );
			}
		}, SPLIDE_REFRESH_DEBOUNCE_MS );
	};

	const bindImageLoadListeners = ( root ) => {
		if ( ! root ) {
			return;
		}
		root.querySelectorAll( 'img' ).forEach( ( img ) => {
			if ( img.complete ) {
				return;
			}
			img.addEventListener( 'load', attemptHydration, { once: true } );
			img.addEventListener( 'error', attemptHydration, { once: true } );
		} );
	};

	const startSplideWhenReady = ( result ) => {
		const minSlides = Math.min( MIN_SLIDES_BEFORE_SPLIDE, slides.length );
		if ( splideStarted || result.filled < minSlides ) {
			return;
		}

		coopboLog( 'completeHydration (early start)', {
			groupId,
			filled: result.filled,
			minSlides,
		} );
		splideStarted = true;
		finishCarouselHydration();
	};

	const completeHydration = () => {
		if ( hydrationComplete ) {
			return;
		}

		const result = distribute();
		if ( result.filled === 0 ) {
			return;
		}

		startSplideWhenReady( result );

		if ( ! result.complete ) {
			return;
		}

		coopboLog( 'completeHydration (all slides)', { groupId } );
		hydrationComplete = true;
		if ( failOpenTimer ) {
			window.clearTimeout( failOpenTimer );
			failOpenTimer = null;
		}
		if ( observer ) {
			observer.disconnect();
			observer = null;
		}
		if ( splideStarted ) {
			scheduleSplideRefresh();
		}
	};

	const attemptHydration = () => {
		if ( hydrationComplete ) {
			return true;
		}

		const result = distribute();
		if ( result.filled === 0 && result.logoNodes === 0 ) {
			bindImageLoadListeners( getLogoSoupRow( refContainer ) );
			return false;
		}

		startSplideWhenReady( result );

		if ( result.complete ) {
			completeHydration();
			return true;
		}

		if ( splideStarted ) {
			scheduleSplideRefresh();
		}

		return false;
	};

	observer = new MutationObserver( () => {
		attemptHydration();
	} );
	observer.observe( refContainer, { childList: true, subtree: true } );

	failOpenTimer = window.setTimeout( () => {
		coopboLog( 'fail-open timeout', {
			groupId,
			ms: HYDRATION_FAIL_OPEN_MS,
		} );
		completeHydration();
	}, HYDRATION_FAIL_OPEN_MS );

	window.requestAnimationFrame( () => {
		window.requestAnimationFrame( () => {
			attemptHydration();
		} );
	} );
}

/**
 * Whether a theme/footer autoscroll snippet should own Splide init.
 *
 * Checks legacy RapidSOS snippet globals when present; defers to nested Bricks sliders.
 *
 * @param {HTMLElement} carouselRoot Splide root element.
 * @return {boolean} True when an external snippet should initialize Splide instead of the plugin.
 */
function externalAutoscrollOwnsSplide( carouselRoot ) {
	return (
		typeof window !== 'undefined' &&
		window.__rapidsosLogoSliderFix &&
		document.getElementById( 'rapidsos-logo-slider-fix-js' ) &&
		! carouselRoot.closest( '.brxe-slider-nested' )
	);
}

/**
 * Mount Splide when an external autoscroll snippet deferred but never initialized.
 *
 * @param {HTMLElement} carouselRoot Splide root element.
 */
function scheduleStandaloneSplideFallback( carouselRoot ) {
	coopboLog( 'scheduleStandaloneSplideFallback', {
		retries: STANDALONE_SPLIDE_RETRY_MS,
	} );
	STANDALONE_SPLIDE_RETRY_MS.forEach( ( delay ) => {
		window.setTimeout( () => {
			if (
				! carouselRoot.isConnected ||
				carouselSplideInitialized( carouselRoot )
			) {
				coopboLog( 'standalone Splide fallback retry skipped', {
					delay,
					connected: carouselRoot.isConnected,
					initialized: carouselSplideInitialized( carouselRoot ),
				} );
				return;
			}
			coopboLog( 'standalone Splide fallback retry', { delay } );
			maybeInitStandaloneSplide( carouselRoot );
		}, delay );
	} );
}

/**
 * Init Splide on standalone carousel wrappers when theme Splide is available.
 *
 * @param {HTMLElement} carouselRoot Splide root element.
 * @return {boolean} True when Splide mounted on this root.
 */
function maybeInitStandaloneSplide( carouselRoot ) {
	const extensions = splideExtensions();
	const splideAvailable = typeof Splide !== 'undefined';
	const hasAutoScroll = !! ( extensions && extensions.AutoScroll );

	if ( carouselRoot.closest( '.brxe-slider-nested' ) ) {
		coopboLog( 'maybeInitStandaloneSplide skipped (nested slider)', {
			splideAvailable,
			hasAutoScroll,
		} );
		return false;
	}
	if ( ! splideAvailable ) {
		coopboLog( 'maybeInitStandaloneSplide: Splide unavailable', {
			hasAutoScroll,
		} );
		return false;
	}
	if ( carouselSplideInitialized( carouselRoot ) ) {
		coopboLog( 'maybeInitStandaloneSplide: already initialized', {
			hasAutoScroll,
		} );
		return true;
	}

	if (
		carouselRoot.splide &&
		typeof carouselRoot.splide.destroy === 'function'
	) {
		try {
			carouselRoot.splide.destroy();
		} catch ( error ) {
			coopboDebugError( 'splide destroy failed', {
				error: error instanceof Error ? error.message : String( error ),
			} );
		}
		carouselRoot.splide = null;
		carouselRoot.classList.remove( 'is-initialized', 'is-active' );
	}

	const options = {
		type: 'loop',
		direction: 'ltr',
		height: 'auto',
		gap: '0px',
		autoWidth: true,
		drag: 'free',
		arrows: false,
		pagination: false,
		keyboard: false,
		autoplay: false,
	};

	if ( extensions && extensions.AutoScroll ) {
		options.autoScroll = {
			speed: standaloneAutoscrollSpeed(),
			pauseOnHover: false,
			pauseOnFocus: false,
			autoStart: true,
		};
	}

	carouselRoot.dataset[ AUTOSCROLL_MARKER ] = '1';
	carouselRoot.setAttribute( 'data-splide', JSON.stringify( options ) );

	const splide = new Splide( carouselRoot, options );
	splide.mount( extensions || undefined );
	carouselRoot.splide = splide;

	const list = carouselRoot.querySelector( '.splide__list' );
	if ( list ) {
		list.style.transition = 'none';
	}
	carouselRoot.classList.remove( HYDRATING_CLASS );
	coopboLog( 'maybeInitStandaloneSplide: mounted', {
		splideAvailable,
		hasAutoScroll,
		mounted: true,
	} );
	return true;
}

/**
 * Initialize hidden reference strips and standalone Splide carousels.
 */
function initCarousels() {
	const refs = document.querySelectorAll( '[data-cb-lw-ref]' );
	coopboLog( 'carousel groups found', refs.length );
	refs.forEach( ( refContainer ) => {
		const groupId = refContainer.getAttribute( 'data-cb-lw-ref' );
		if ( ! groupId ) {
			return;
		}
		mountCarouselReference( refContainer, groupId );
	} );
}

/**
 * Mount all strip containers, then initialize carousels.
 */
function init() {
	const strips = document.querySelectorAll( '[data-cb-lw]' );
	const refs = document.querySelectorAll( '[data-cb-lw-ref]' );
	coopboLog( 'init', {
		stripCount: strips.length,
		carouselRefCount: refs.length,
	} );

	strips.forEach( ( el ) => {
		if ( el.hasAttribute( 'data-cb-lw-ref' ) ) {
			return;
		}
		mountLogoSoup( el );
	} );
	initCarousels();
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
