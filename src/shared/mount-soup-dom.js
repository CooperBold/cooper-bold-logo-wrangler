/**
 * Frontend mount for a logo strip, built on the framework-free logo-soup engine.
 *
 * Produces the same DOM as the library's React <LogoSoup> component
 * (div[role=list] > span[role=listitem] > [a >] img), because theme CSS targets
 * `.cb-lw-inner > div > span`. No React here: this is the frontend bundle.
 */

import {
	createLogoSoup,
	getVisualCenterTransform,
} from '@sanity-labs/logo-soup';

import { toSoupOptions } from './lw-sanitize';
import {
	clearLogoSoupLoading,
	markLogoSoupLoading,
	onLogoSoupError,
} from './soup-loading';

/**
 * Pair each logo the engine returns with its configured link.
 *
 * The engine drops logos that fail to load, so match by src, not by position.
 *
 * @param {Array<{ url: string, link?: string }>} configLogos Logos from the PHP config.
 * @return {function(string): string} Returns the next link for a src ('' when none).
 */
function createLinkLookup( configLogos ) {
	const queues = new Map();
	configLogos.forEach( ( logo ) => {
		const queue = queues.get( logo.url ) || [];
		queue.push( logo.link || '' );
		queues.set( logo.url, queue );
	} );
	return ( src ) => {
		const queue = queues.get( src );
		return queue && queue.length ? queue.shift() : '';
	};
}

/**
 * @param {Object}  logo             Normalized logo from the engine.
 * @param {Object}  settings         Layout settings.
 * @param {string}  settings.alignBy Alignment mode.
 * @param {string}  settings.halfGap CSS padding around each logo.
 * @param {boolean} settings.eager   Load images eagerly (carousel reference strip).
 * @param {string}  link             Optional link URL.
 * @return {HTMLSpanElement} List item for one logo.
 */
function createLogoItem( logo, { alignBy, halfGap, eager }, link ) {
	const item = document.createElement( 'span' );
	item.setAttribute( 'role', 'listitem' );
	item.style.display = 'inline-block';
	item.style.verticalAlign = 'middle';
	item.style.padding = halfGap;

	const src = logo.croppedSrc || logo.src;
	const img = document.createElement( 'img' );
	img.setAttribute( 'crossorigin', 'anonymous' );
	// Set before src so lazy loading applies to the first fetch.
	img.setAttribute( 'loading', eager ? 'eager' : 'lazy' );
	img.setAttribute( 'decoding', 'async' );
	img.setAttribute( 'alt', logo.alt );
	img.setAttribute( 'width', String( logo.normalizedWidth ) );
	img.setAttribute( 'height', String( logo.normalizedHeight ) );
	img.style.display = 'block';
	img.style.objectFit = 'contain';
	img.style.width = `${ logo.normalizedWidth }px`;
	img.style.height = `${ logo.normalizedHeight }px`;
	const transform = getVisualCenterTransform( logo, alignBy );
	if ( transform ) {
		img.style.transform = transform;
	}
	img.addEventListener( 'error', () => {
		// eslint-disable-next-line no-console -- surface broken logo URLs during development.
		console.warn( 'Cooper Bold Logo Wrangler: failed to load image', src );
		img.remove();
	} );
	img.setAttribute( 'src', src );

	if ( link ) {
		const anchor = document.createElement( 'a' );
		anchor.setAttribute( 'href', link );
		anchor.setAttribute( 'rel', 'noopener noreferrer' );
		anchor.appendChild( img );
		item.appendChild( anchor );
	} else {
		item.appendChild( img );
	}

	return item;
}

/**
 * Mount a normalized logo row inside a container and keep it in sync with the engine.
 *
 * @param {HTMLElement} container            Mount node (its children are replaced).
 * @param {Object}      config               Sanitized config from the data-cb-lw attribute.
 * @param {Object}      options              Optional settings.
 * @param {Object}      options.context      Caller label for failure logs.
 * @param {Function}    options.createEngine Engine factory (tests inject a fake).
 * @return {{ engine: Object, row: HTMLElement, destroy: Function }|null} Null when there are no logos.
 */
export function mountSoupDom( container, config, options = {} ) {
	const soupOptions = toSoupOptions( config );
	if ( ! soupOptions ) {
		return null;
	}

	const { context = {}, createEngine = createLogoSoup } = options;
	const { alignBy, gap, logos, ...processOptions } = soupOptions;
	const settings = {
		alignBy,
		halfGap:
			typeof gap === 'number' ? `${ gap / 2 }px` : `calc(${ gap } / 2)`,
		eager: Boolean( config.eagerLoad ),
	};

	const row = document.createElement( 'div' );
	row.setAttribute( 'role', 'list' );
	row.setAttribute( 'data-logo-soup-loading', 'false' );
	row.style.textAlign = 'center';
	row.style.setProperty( 'text-wrap', 'balance' );
	row.style.opacity = '1';
	row.style.transition = 'opacity 0.2s ease-in-out';

	markLogoSoupLoading( container );
	container.replaceChildren( row );

	const engine = createEngine();
	let renderedLogos = null;
	let reportedFailures = null;

	const sync = () => {
		const state = engine.getSnapshot();
		const isLoading = state.status === 'loading';

		if ( state.error ) {
			row.remove();
		} else if ( row.parentNode !== container ) {
			container.replaceChildren( row );
		}

		row.setAttribute( 'data-logo-soup-loading', String( isLoading ) );
		row.style.opacity =
			isLoading && state.normalizedLogos.length === 0 ? '0' : '1';

		if ( state.normalizedLogos !== renderedLogos ) {
			renderedLogos = state.normalizedLogos;
			const nextLink = createLinkLookup( config.logos );
			row.replaceChildren(
				...renderedLogos.map( ( logo ) =>
					createLogoItem( logo, settings, nextLink( logo.src ) )
				)
			);
		}

		if ( state.status === 'ready' ) {
			clearLogoSoupLoading( container );
		}

		if (
			state.failures.length > 0 &&
			state.failures !== reportedFailures
		) {
			reportedFailures = state.failures;
			onLogoSoupError( state.failures, context );
			clearLogoSoupLoading( container );
		}
	};

	const unsubscribe = engine.subscribe( sync );
	engine.process( { logos, ...processOptions } );
	sync();

	return {
		engine,
		row,
		destroy: () => {
			unsubscribe();
			engine.destroy();
		},
	};
}
