---
title: The frontend mounts without React
type: decision
summary: build/view.js uses the logo-soup engine and plain DOM, so pages with a logo strip no longer load WordPress's React; the editor and admin preview keep React.
status: active
created: 2026-11-09
updated: 2026-11-09
last_verified: 2026-11-09
sources: [src/view.js, src/shared/mount-soup-dom.js, src/shared/mount-soup-dom.test.js, tests/COOPBOLO_Assets_Test.php, build/view.asset.php, "node_modules/@sanity-labs/logo-soup/dist/react/index.js"]
tags: [architecture, performance, react, frontend]
---

# The frontend mounts without React

**Status:** active. Built as 1.5.0 on branch `feat/react-free-frontend`, 2026-11-09; not merged or uploaded yet. Replaces the frontend half of [[0005-bundle-upstream-react-component]].

## Why

Nicholas needed the plugin's public pages to stop loading React. `build/view.js` was only 20 KB, but it declared `react`, `react-jsx-runtime` and `wp-element` as dependencies, so WordPress loaded `react`, `react-dom`, `react-jsx-runtime` and `wp-element` on every page with a strip: 53 KB gzipped, 156 KB raw (measured from WordPress 7.1's minified files).

## Decision

- `src/shared/mount-soup-dom.js` calls the library's framework-free engine (`createLogoSoup`, `getVisualCenterTransform` from `@sanity-labs/logo-soup`) and builds the row with plain DOM calls. This is not a port of the measuring algorithm: that still comes from the library.
- It writes the same DOM the library's React component writes: `div[role=list] > span[role=listitem] > [a >] img`, same inline styles and attributes. Theme and Bricks CSS targets `.cb-lw-inner > div > span` ([[rendering-pipeline]]).
- The React-free helpers moved to `src/shared/lw-sanitize.js` and `src/shared/soup-loading.js`. `to-lw-props.js` and `logo-soup-lifecycle.js` re-export them and stay React-only, for the block editor and the collection preview.
- `build/view.asset.php` now lists no dependencies.

## Guards

- Jest (`mount-soup-dom.test.js`): the DOM shape, links, failure handling, and a check that `view.js` and its three shared modules import no React module.
- PHPUnit (`COOPBOLO_Assets_Test::test_frontend_view_script_does_not_depend_on_react`): fails if the built asset file lists a React handle.

## Verified (2026-11-09, wp-env, WP 7.1-alpha, PHP 7.4, logged out)

Captured the rendered DOM of the React build (1.4.0), then compared it with the new build on the same pages after normalizing blob URLs and Splide's moving state. Identical in all seven cases: collection strip (12 logos, crop on, links), standalone carousel, inline logos with one broken URL, all logos broken, background color with density off and a duplicate src, a 3-logo carousel with one broken logo, and a missing collection. `window.React` was undefined on the new build. The block editor and the collection preview still rendered 12 logos.

## Behavior changes

- A logo's link is matched by src. The React version matched by render position, so a failed logo shifted every later link by one.
- Rows are created synchronously at mount, not after React's first render.

## Known, not changed

- A carousel with 3 or fewer logos never starts Splide if one logo fails to load (it waits for 3 filled slides). The React build behaved the same.

## Related

- [[0005-bundle-upstream-react-component]] · [[rendering-pipeline]] · [[sanity-logo-soup]] · [[0006-php-owned-asset-enqueue]]
