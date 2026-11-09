/**
 * Shared Cooper Bold Logo Wrangler sanitizers and engine options. No React here: the
 * frontend bundle imports this file and must not pull in wp-element.
 */

const ALIGN_BY_VALUES = [
	'bounds',
	'visual-center',
	'visual-center-x',
	'visual-center-y',
];

const NAMED_CSS_COLORS = new Set(
	'aliceblue,antiquewhite,aqua,aquamarine,azure,beige,bisque,black,blanchedalmond,blue,blueviolet,brown,burlywood,cadetblue,chartreuse,chocolate,coral,cornflowerblue,cornsilk,crimson,cyan,darkblue,darkcyan,darkgoldenrod,darkgray,darkgreen,darkgrey,darkkhaki,darkmagenta,darkolivegreen,darkorange,darkorchid,darkred,darksalmon,darkseagreen,darkslateblue,darkslategray,darkslategrey,darkturquoise,darkviolet,deeppink,deepskyblue,dimgray,dimgrey,dodgerblue,firebrick,floralwhite,forestgreen,fuchsia,gainsboro,ghostwhite,gold,goldenrod,gray,green,greenyellow,grey,honeydew,hotpink,indianred,indigo,ivory,khaki,lavender,lavenderblush,lawngreen,lemonchiffon,lightblue,lightcoral,lightcyan,lightgoldenrodyellow,lightgray,lightgreen,lightgrey,lightpink,lightsalmon,lightseagreen,lightskyblue,lightslategray,lightslategrey,lightsteelblue,lightyellow,lime,limegreen,linen,magenta,maroon,mediumaquamarine,mediumblue,mediumorchid,mediumpurple,mediumseagreen,mediumslateblue,mediumspringgreen,mediumturquoise,mediumvioletred,midnightblue,mintcream,mistyrose,moccasin,navajowhite,navy,oldlace,olive,olivedrab,orange,orangered,orchid,palegoldenrod,palegreen,paleturquoise,palevioletred,papayawhip,peachpuff,peru,pink,plum,powderblue,purple,rebeccapurple,red,rosybrown,royalblue,saddlebrown,salmon,sandybrown,seagreen,seashell,sienna,silver,skyblue,slateblue,slategray,slategrey,snow,springgreen,steelblue,tan,teal,thistle,tomato,turquoise,violet,wheat,white,whitesmoke,yellow,yellowgreen'.split(
		','
	)
);

/**
 * @param {number} value    Raw value.
 * @param {number} min      Minimum.
 * @param {number} max      Maximum.
 * @param {number} fallback Fallback when non-numeric.
 * @return {number} Clamped numeric value.
 */
export function clamp( value, min, max, fallback ) {
	const n = Number( value );
	if ( Number.isNaN( n ) ) {
		return fallback;
	}
	return Math.max( min, Math.min( max, n ) );
}

/**
 * @param {string} value Raw user input.
 * @return {string} Plain text without HTML tags.
 */
export function stripPlainText( value ) {
	return String( value )
		.replace( /<[^>]*>/g, '' )
		.trim();
}

/**
 * @param {string} color Raw color string.
 * @return {string} Safe color or empty string.
 */
export function sanitizeCssColor( color ) {
	const trimmed = stripPlainText( color );
	if ( ! trimmed ) {
		return '';
	}
	if ( /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test( trimmed ) ) {
		return trimmed.toLowerCase();
	}
	if ( /^(rgb|rgba|hsl|hsla)\(\s*[\d.%\s,-]+\s*\)$/i.test( trimmed ) ) {
		return trimmed;
	}
	const lower = trimmed.toLowerCase();
	if ( lower === 'transparent' || lower === 'currentcolor' ) {
		return lower;
	}
	if ( /^[a-z]+$/.test( lower ) && NAMED_CSS_COLORS.has( lower ) ) {
		return lower;
	}
	return '';
}

/**
 * @param {string} alt User alt text.
 * @param {string} url Logo URL.
 * @return {string} Non-empty alt text.
 */
export function resolveAlt( alt, url ) {
	const cleaned = stripPlainText( alt );
	if ( cleaned ) {
		return cleaned;
	}
	try {
		const path = new URL( url, window.location.origin ).pathname;
		const base =
			path
				.replace( /\.[^./]+$/, '' )
				.split( '/' )
				.pop() || '';
		const name = base
			.replace( /[-_]+/g, ' ' )
			.replace( /\b\w/g, ( c ) => c.toUpperCase() );
		if ( name ) {
			return name;
		}
	} catch ( error ) {
		// Filename fallback unavailable; use generic alt below.
	}
	return 'Logo';
}

/**
 * @param {string} link Raw link URL.
 * @return {string} Safe link or empty string.
 */
export function sanitizeLink( link ) {
	const trimmed = stripPlainText( link );
	if ( ! trimmed || /^javascript:/i.test( trimmed ) ) {
		return '';
	}
	return trimmed;
}

/**
 * Mirror PHP sanitize_attributes for editor preview.
 *
 * @param {Object} attributes Block attributes.
 * @return {Object} Sanitized config shaped like data-cb-lw JSON.
 */
export function sanitizePreviewConfig( attributes ) {
	const logosIn = Array.isArray( attributes.logos ) ? attributes.logos : [];
	const logos = logosIn.reduce( ( acc, logo, index ) => {
		if ( ! logo || typeof logo !== 'object' ) {
			return acc;
		}
		const url = stripPlainText( logo.url || '' );
		if ( ! url ) {
			return acc;
		}
		const link = sanitizeLink( logo.link || '' );
		const row = {
			id: logo.id || index + 1,
			url,
			alt: resolveAlt( logo.alt || '', url ),
		};
		if ( link ) {
			row.link = link;
		}
		acc.push( row );
		return acc;
	}, [] );

	const alignBy = ALIGN_BY_VALUES.includes( attributes.alignBy )
		? attributes.alignBy
		: 'visual-center-y';

	let gap = attributes.gap;
	if ( typeof gap === 'string' && /[a-z%]/i.test( gap ) ) {
		gap = 28;
	}
	gap = clamp( gap, 0, 96, 28 );

	const densityAware = Boolean( attributes.densityAware );

	const config = {
		logos,
		baseSize: clamp( attributes.baseSize, 16, 256, 48 ),
		scaleFactor: clamp( attributes.scaleFactor, 0, 1, 0.5 ),
		contrastThreshold: clamp( attributes.contrastThreshold, 0, 255, 10 ),
		densityAware,
		densityFactor: densityAware
			? clamp( attributes.densityFactor, 0, 1, 0.5 )
			: 0,
		cropToContent: Boolean( attributes.cropToContent ),
		alignBy,
		gap,
	};

	const bg = sanitizeCssColor( attributes.backgroundColor || '' );
	if ( bg ) {
		config.backgroundColor = bg;
	}

	return config;
}

/**
 * Engine and layout options shared by the React preview and the frontend DOM mount.
 *
 * @param {Object} config Sanitized config from PHP or sanitizePreviewConfig.
 * @return {Object|null} Options or null when logos are missing.
 */
export function toSoupOptions( config ) {
	if ( ! config?.logos?.length ) {
		return null;
	}

	const options = {
		logos: config.logos.map( ( logo ) => ( {
			src: logo.url,
			alt: logo.alt || resolveAlt( '', logo.url ),
		} ) ),
		baseSize: config.baseSize ?? 48,
		scaleFactor: config.scaleFactor ?? 0.5,
		contrastThreshold: config.contrastThreshold ?? 10,
		densityAware: Boolean( config.densityAware ),
		densityFactor: config.densityAware ? config.densityFactor ?? 0.5 : 0,
		cropToContent: Boolean( config.cropToContent ),
		alignBy: config.alignBy ?? 'visual-center-y',
		gap: config.gap ?? 28,
	};

	if ( config.backgroundColor ) {
		options.backgroundColor = config.backgroundColor;
	}

	return options;
}
