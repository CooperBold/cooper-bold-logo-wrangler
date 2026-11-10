---
title: Bundle the upstream React component, don't port it
type: decision
summary: The frontend bundles @sanity-labs/logo-soup's React component and hydrates PHP markup, instead of reimplementing the algorithm in vanilla JS or PHP.
status: superseded
created: 2026-10-19
updated: 2026-11-10
last_verified: 2026-11-10
sources: [README.md, AGENTS.md, docs/SAR-REVIEW.md, package.json]
tags: [architecture, react, dependencies]
---

# Bundle the upstream React component

**Status:** superseded for the frontend by [[0009-react-free-frontend]] (2026-11-09). Still true for the block editor and the collection preview, and for "use the upstream library, don't reimplement the algorithm".

## Decision

`build/view.js` bundles the upstream `LogoSoup` React component with WordPress's React (`@wordpress/element`) and mounts it on PHP-rendered nodes ([[rendering-pipeline]]). README: the component "is bundled rather than ported to vanilla JS to preserve upstream behavior with minimal maintenance." `AGENTS.md` used to say not to port unless upstream API changes make React bundling untenable; since 1.5.0 it says the opposite for the frontend — don't reimplement the logo-soup measuring algorithm, use the library's engine.

## Why

- The value is the upstream measuring algorithm (visual weight, density, visual center). A port would drift from it with every upstream release. Upgrades are a version bump instead, like 1.2.2 → 1.3.0 in commit `1468a42`.
- The SAR judge picked this shape (Candidate C: dynamic PHP block + thin React adapter); see [[sar-review]]. The gzipped `view.js` was about 5 KB at the time.

## Alternatives

- A vanilla-JS port: rejected for maintenance cost.
- Server-side (PHP) normalization: not considered viable, since measuring needs the browser canvas.

## Consequences

- `@sanity-labs/logo-soup` is pinned exactly (`1.3.0`), and upgrades can shift measurements slightly (1.3.0 changed the color-distance math; `AI_SESSION_MEMORY.md` 2026-09-02 Risks). Check a real collection after any bump.
- Canvas measuring needs same-origin or CORS-enabled images ([[gotchas]]).

## Related

- [[sanity-logo-soup]] · [[0006-php-owned-asset-enqueue]]
