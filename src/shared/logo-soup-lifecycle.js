/**
 * Shared @sanity-labs/logo-soup lifecycle hooks (v1.3.0+ failures + loading).
 */

import { useCallback, useMemo } from '@wordpress/element';

import { clearLogoSoupLoading, onLogoSoupError } from './soup-loading';

export * from './soup-loading';

/**
 * Merge logo-soup lifecycle callbacks into props from toLwProps().
 *
 * @param {Object|null} soupProps Props from toLwProps().
 * @param {Object}      options   container + optional context label.
 * @return {Object|null} Props with onError / onNormalized wired.
 */
export function withLogoSoupLifecycle( soupProps, options = {} ) {
	if ( ! soupProps ) {
		return soupProps;
	}

	const { container, context = {} } = options;
	const priorOnError = soupProps.onError;
	const priorOnNormalized = soupProps.onNormalized;

	return {
		...soupProps,
		onError: ( failures ) => {
			onLogoSoupError( failures, context );
			clearLogoSoupLoading( container );
			if ( typeof priorOnError === 'function' ) {
				priorOnError( failures );
			}
		},
		onNormalized: ( normalized ) => {
			clearLogoSoupLoading( container );
			if ( typeof priorOnNormalized === 'function' ) {
				priorOnNormalized( normalized );
			}
		},
	};
}

/**
 * React hook: stable onError / onNormalized identities for logo-soup 1.3.0 effects.
 *
 * @param {Object|null} soupProps Props from toLwProps().
 * @param {Object}      options   container + optional context label.
 * @return {Object|null} Props with lifecycle callbacks merged.
 */
export function useLogoSoupLifecycle( soupProps, options = {} ) {
	const { container, context = {} } = options;
	const priorOnError = soupProps?.onError;
	const priorOnNormalized = soupProps?.onNormalized;

	const onError = useCallback(
		( failures ) => {
			onLogoSoupError( failures, context );
			clearLogoSoupLoading( container );
			if ( typeof priorOnError === 'function' ) {
				priorOnError( failures );
			}
		},
		[ context, container, priorOnError ]
	);

	const onNormalized = useCallback(
		( normalized ) => {
			clearLogoSoupLoading( container );
			if ( typeof priorOnNormalized === 'function' ) {
				priorOnNormalized( normalized );
			}
		},
		[ container, priorOnNormalized ]
	);

	return useMemo( () => {
		if ( ! soupProps ) {
			return soupProps;
		}
		return {
			...soupProps,
			onError,
			onNormalized,
		};
	}, [ soupProps, onError, onNormalized ] );
}
