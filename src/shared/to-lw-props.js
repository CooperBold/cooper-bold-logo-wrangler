/**
 * Shared Cooper Bold Logo Wrangler adapter — mirrors PHP attribute sanitization for JS previews.
 * Editor/admin only (JSX). The frontend uses lw-sanitize.js and mount-soup-dom.js.
 */

import { toSoupOptions } from './lw-sanitize';

export * from './lw-sanitize';

/**
 * @param {Object} config Sanitized config from PHP or sanitizePreviewConfig.
 * @return {Object|null} LogoSoup props or null when logos are missing.
 */
export function toLwProps( config ) {
	const options = toSoupOptions( config );
	if ( ! options ) {
		return null;
	}

	let renderIndex = 0;

	const props = {
		...options,
		renderImage: ( imageProps ) => {
			const { src, alt, width, height, style } = imageProps;
			if ( renderIndex >= config.logos.length ) {
				renderIndex = 0;
			}
			const logo = config.logos[ renderIndex ] || {};
			renderIndex += 1;
			const img = (
				<img
					src={ src }
					alt={ alt }
					width={ width }
					height={ height }
					style={ style }
					crossOrigin="anonymous"
					loading={ config.eagerLoad ? 'eager' : 'lazy' }
					decoding="async"
					onError={ ( event ) => {
						// eslint-disable-next-line no-console -- surface broken logo URLs during development.
						console.warn( 'Cooper Bold Logo Wrangler: failed to load image', src );
						event.currentTarget.remove();
					} }
				/>
			);
			const link = logo.link || '';
			if ( link ) {
				return (
					<a href={ link } rel="noopener noreferrer">
						{ img }
					</a>
				);
			}
			return img;
		},
	};

	return props;
}
