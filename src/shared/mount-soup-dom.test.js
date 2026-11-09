/**
 * @jest-environment jsdom
 */

import fs from 'fs';
import path from 'path';

import { mountSoupDom } from './mount-soup-dom';
import { SOUP_LOADING_CLASS } from './soup-loading';

jest.mock(
	'@sanity-labs/logo-soup',
	() => ( {
		createLogoSoup: jest.fn(),
		getVisualCenterTransform: ( logo, alignBy ) =>
			alignBy === 'bounds' ? undefined : logo.testTransform,
	} ),
	{ virtual: true }
);

const IDLE = { status: 'idle', normalizedLogos: [], error: null, failures: [] };

function createFakeEngine() {
	let snapshot = IDLE;
	const listeners = new Set();
	return {
		processed: null,
		destroyed: false,
		process( options ) {
			this.processed = options;
		},
		subscribe( listener ) {
			listeners.add( listener );
			return () => listeners.delete( listener );
		},
		getSnapshot: () => snapshot,
		destroy() {
			this.destroyed = true;
		},
		set( next ) {
			snapshot = { ...IDLE, ...next };
			listeners.forEach( ( listener ) => listener() );
		},
	};
}

function normalized( src, extra = {} ) {
	return {
		src,
		alt: 'Alt',
		normalizedWidth: 99,
		normalizedHeight: 35,
		...extra,
	};
}

function mount( config, options = {} ) {
	const container = document.createElement( 'div' );
	container.innerHTML = '<div><span><img></span></div>';
	const engine = createFakeEngine();
	const instance = mountSoupDom( container, config, {
		createEngine: () => engine,
		...options,
	} );
	return { container, engine, instance };
}

const CONFIG = {
	logos: [
		{ url: 'https://example.com/a.png', alt: 'A' },
		{
			url: 'https://example.com/b.png',
			alt: 'B',
			link: 'https://example.com/b',
		},
	],
	gap: 32,
	baseSize: 56,
	densityAware: true,
	densityFactor: 0.3,
	cropToContent: true,
	backgroundColor: '#112233',
};

describe( 'mountSoupDom', () => {
	beforeEach( () => {
		jest.spyOn( console, 'warn' ).mockImplementation( () => {} );
	} );

	afterEach( () => {
		jest.restoreAllMocks();
	} );

	it( 'returns null and leaves the container alone when there are no logos', () => {
		const { container, instance } = mount( { logos: [] } );
		expect( instance ).toBeNull();
		expect( container.querySelector( 'img' ) ).not.toBeNull();
		expect( container.classList.contains( SOUP_LOADING_CLASS ) ).toBe(
			false
		);
	} );

	it( 'passes the same engine options the React preview uses', () => {
		const { engine } = mount( CONFIG );
		expect( engine.processed ).toEqual( {
			logos: [
				{ src: 'https://example.com/a.png', alt: 'A' },
				{ src: 'https://example.com/b.png', alt: 'B' },
			],
			baseSize: 56,
			scaleFactor: 0.5,
			contrastThreshold: 10,
			densityAware: true,
			densityFactor: 0.3,
			cropToContent: true,
			backgroundColor: '#112233',
		} );
	} );

	it( 'renders an empty row first, hidden while loading', () => {
		const { container, engine } = mount( CONFIG );
		const row = container.firstElementChild;

		expect( container.children ).toHaveLength( 1 );
		expect( row.getAttribute( 'role' ) ).toBe( 'list' );
		expect( row.getAttribute( 'data-logo-soup-loading' ) ).toBe( 'false' );
		expect( row.children ).toHaveLength( 0 );
		expect( container.classList.contains( SOUP_LOADING_CLASS ) ).toBe(
			true
		);

		engine.set( { status: 'loading' } );
		expect( row.getAttribute( 'data-logo-soup-loading' ) ).toBe( 'true' );
		expect( row.style.opacity ).toBe( '0' );
	} );

	it( 'renders div > span > img, with links matched by src', () => {
		const { container, engine } = mount( CONFIG );
		engine.set( {
			status: 'ready',
			normalizedLogos: [
				normalized( 'https://example.com/a.png', {
					croppedSrc: 'blob:cropped-a',
					testTransform: 'translate(0px, 1.8px)',
				} ),
				normalized( 'https://example.com/b.png' ),
			],
		} );

		const row = container.firstElementChild;
		expect( row.style.opacity ).toBe( '1' );
		expect( row.style.textAlign ).toBe( 'center' );
		expect( container.classList.contains( SOUP_LOADING_CLASS ) ).toBe(
			false
		);

		const items = Array.from( row.children );
		expect( items.map( ( item ) => item.tagName ) ).toEqual( [
			'SPAN',
			'SPAN',
		] );
		expect( items[ 0 ].getAttribute( 'role' ) ).toBe( 'listitem' );
		expect( items[ 0 ].style.padding ).toBe( '16px' );

		const first = items[ 0 ].firstElementChild;
		expect( first.tagName ).toBe( 'IMG' );
		expect( first.getAttribute( 'src' ) ).toBe( 'blob:cropped-a' );
		expect( first.getAttribute( 'width' ) ).toBe( '99' );
		expect( first.getAttribute( 'height' ) ).toBe( '35' );
		expect( first.getAttribute( 'loading' ) ).toBe( 'lazy' );
		expect( first.getAttribute( 'crossorigin' ) ).toBe( 'anonymous' );
		expect( first.style.width ).toBe( '99px' );
		expect( first.style.transform ).toBe( 'translate(0px, 1.8px)' );

		const link = items[ 1 ].firstElementChild;
		expect( link.tagName ).toBe( 'A' );
		expect( link.getAttribute( 'href' ) ).toBe( 'https://example.com/b' );
		expect( link.getAttribute( 'rel' ) ).toBe( 'noopener noreferrer' );
		expect( link.firstElementChild.tagName ).toBe( 'IMG' );
	} );

	it( 'keeps each link on its own logo when an earlier logo failed to load', () => {
		const { container, engine } = mount( CONFIG );
		engine.set( {
			status: 'ready',
			normalizedLogos: [ normalized( 'https://example.com/b.png' ) ],
			failures: [
				{ src: 'https://example.com/a.png', error: new Error( 'x' ) },
			],
		} );

		const items = container.firstElementChild.children;
		expect( items ).toHaveLength( 1 );
		expect( items[ 0 ].firstElementChild.getAttribute( 'href' ) ).toBe(
			'https://example.com/b'
		);
		// eslint-disable-next-line no-console
		expect( console.warn ).toHaveBeenCalledTimes( 1 );
	} );

	it( 'loads eagerly for the carousel reference strip', () => {
		const { container, engine } = mount( { ...CONFIG, eagerLoad: true } );
		engine.set( {
			status: 'ready',
			normalizedLogos: [ normalized( 'https://example.com/a.png' ) ],
		} );
		expect(
			container.querySelector( 'img' ).getAttribute( 'loading' )
		).toBe( 'eager' );
	} );

	it( 'removes the row and the loading class when every logo fails', () => {
		const { container, engine } = mount( CONFIG );
		engine.set( {
			status: 'error',
			error: new Error( 'all failed' ),
			failures: [
				{ src: 'https://example.com/a.png', error: new Error( 'x' ) },
			],
		} );
		expect( container.children ).toHaveLength( 0 );
		expect( container.classList.contains( SOUP_LOADING_CLASS ) ).toBe(
			false
		);
	} );

	it( 'removes an image that fails after render', () => {
		const { container, engine } = mount( CONFIG );
		engine.set( {
			status: 'ready',
			normalizedLogos: [ normalized( 'https://example.com/a.png' ) ],
		} );
		container.querySelector( 'img' ).dispatchEvent( new Event( 'error' ) );
		expect( container.querySelector( 'img' ) ).toBeNull();
	} );

	it( 'destroy() stops updates and tears down the engine', () => {
		const { container, engine, instance } = mount( CONFIG );
		instance.destroy();
		engine.set( {
			status: 'ready',
			normalizedLogos: [ normalized( 'https://example.com/a.png' ) ],
		} );
		expect( engine.destroyed ).toBe( true );
		expect( container.querySelector( 'img' ) ).toBeNull();
	} );
} );

describe( 'frontend bundle stays React-free', () => {
	const read = ( file ) =>
		fs.readFileSync( path.join( __dirname, '..', file ), 'utf8' );

	it.each( [
		'view.js',
		'shared/mount-soup-dom.js',
		'shared/lw-sanitize.js',
		'shared/soup-loading.js',
	] )( '%s imports no React, wp-element, or JSX module', ( file ) => {
		const source = read( file );
		expect( source ).not.toMatch(
			/from\s+'(react|react-dom|@wordpress\/element|@sanity-labs\/logo-soup\/react)[^']*'/
		);
		expect( source ).not.toMatch(
			/from\s+'\.\/(shared\/)?(to-lw-props|logo-soup-lifecycle)'/
		);
	} );
} );
