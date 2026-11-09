/**
 * Loading-class and failure-log helpers shared by the React previews and the frontend
 * DOM mount. No React here: the frontend bundle must not pull in wp-element.
 */

export const SOUP_LOADING_CLASS = 'cb-lw-soup-loading';
const LOG_PREFIX = '[Cooper Bold Logo Wrangler]';

/**
 * @param {HTMLElement|null|undefined} container Mount node to mark while measuring.
 */
export function markLogoSoupLoading( container ) {
	if ( container instanceof HTMLElement ) {
		container.classList.add( SOUP_LOADING_CLASS );
	}
}

/**
 * @param {HTMLElement|null|undefined} container Mount node cleared after normalization.
 */
export function clearLogoSoupLoading( container ) {
	if ( container instanceof HTMLElement ) {
		container.classList.remove( SOUP_LOADING_CLASS );
	}
}

/**
 * Log per-logo measurement/load failures reported by logo-soup 1.3.0+.
 *
 * @param {Array<{ src: string, error: unknown }>} failures Engine failures.
 * @param {Object}                               context Optional caller metadata.
 */
export function onLogoSoupError( failures, context = {} ) {
	if ( ! Array.isArray( failures ) || failures.length === 0 ) {
		return;
	}
	// eslint-disable-next-line no-console -- surface broken logo URLs to site editors.
	console.warn( LOG_PREFIX, 'logo load failures', {
		...context,
		failures,
	} );
}
