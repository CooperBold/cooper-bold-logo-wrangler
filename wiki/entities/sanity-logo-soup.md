---
title: Sanity Labs Logo Soup (npm)
type: entity
summary: The upstream @sanity-labs/logo-soup library (pinned 1.3.0) and how the plugin wires its lifecycle hooks.
created: 2026-06-12
updated: 2026-11-10
last_verified: 2026-11-10
tags: [npm, sanity, logos]
aliases: ["@sanity-labs/logo-soup"]
status: active
sources: [package.json, src/shared/logo-soup-lifecycle.js, src/shared/to-lw-props.js, src/shared/mount-soup-dom.js, src/shared/lw-sanitize.js, "commits 1468a42, a9168c2, 403645d", AI_SESSION_MEMORY.md]
---

# @sanity-labs/logo-soup

npm library that normalizes logo images and lays them out in a balanced strip.

## What it is

Dependency in `package.json` (pinned **1.3.0**). Used from `src/view.js` on the front end after PHP renders markup/data. Lifecycle hooks (`onError`, `onNormalized`) wired via `src/shared/logo-soup-lifecycle.js` (failures logging + `cb-lw-soup-loading` on mount nodes). Custom `renderImage` in `to-lw-props.js` sets `crossOrigin="anonymous"` to match upstream measurement requests.

Since 1.5.0 the frontend uses the library's **framework-free engine** (`createLogoSoup`, `getVisualCenterTransform` from `@sanity-labs/logo-soup`) through `src/shared/mount-soup-dom.js`, not the React component. The React component is still used by the block editor and the collection admin preview ([[0009-react-free-frontend]]). The measuring algorithm is unchanged — it still comes from the library; only the DOM writer is the plugin's own.

Pinned **exactly** (no caret) on purpose: upstream changes can shift measurements (1.3.0 changed the color-distance math, per `AI_SESSION_MEMORY.md` 2026-09-02 Risks). Failures always `console.warn`, not only in debug mode, so editors notice broken logo URLs (same entry, Decisions). React previews use the `useLogoSoupLifecycle` hook so `onError` keeps a stable identity across re-renders (commit `a9168c2`). Why the React component is bundled instead of ported in the editor: [[0005-bundle-upstream-react-component]]; why the frontend moved off it: [[0009-react-free-frontend]].

## How it's used in this project

- Front-end entry: `src/view.js` (built to `build/view.js`), which calls `mountSoupDom()` from `src/shared/mount-soup-dom.js`
- Editor and collection preview entry points still use the library's React component, via `src/shared/to-lw-props.js` and `src/shared/logo-soup-lifecycle.js`
- Block attributes in `src/block/block.json` mirror tuning params: `baseSize`, `scaleFactor`, `gap`, `densityAware`, etc.

## Related

- [[rendering-pipeline]] · [[0005-bundle-upstream-react-component]] · [[0009-react-free-frontend]]
- [[wordpress-plugin]]
- [[gutenberg-block-balanced-logos|gutenberg-block (cooper-bold/logo-wrangler)]]
