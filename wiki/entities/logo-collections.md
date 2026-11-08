---
title: Logo Collections (coopbolo_collection)
type: entity
summary: The reusable logo-set custom post type, its meta keys (with legacy fallbacks), REST route, and admin UI.
status: active
created: 2026-10-19
updated: 2026-11-08
last_verified: 2026-11-08
sources: [includes/class-coopbolo-collections.php, readme.txt, "commit ee4d016", "commit a5bc12b", "RapidSOS repo deploy/wordpress/logo-soup/README.md"]
tags: [cpt, admin, collections, rest]
---

# Logo Collections

Logo Collections are the main way editors use the plugin, especially on page builders. You build a named set of Media Library logos once, then reference it with `[logo_wrangler collection="slug"]` or `id="123"`, or pick it in the block sidebar. Added in 1.1.0 (commit `ee4d016`, 2026-06-16).

## Storage

| Item | Value | Source |
|---|---|---|
| Post type | `coopbolo_collection` since 1.3.5; `cb_logo_collection` from 1.1.0 to 1.3.4 | `COOPBOLO_Collections::POST_TYPE`, `LEGACY_POST_TYPE` |
| Logos meta | `_coopbolo_logos`: ordered array of `{id, url, alt, link}` | `META_LOGOS` |
| Settings meta | `_coopbolo_settings`: baseSize, gap, layout, etc. | `META_SETTINGS` |
| Legacy meta, read only as fallback | `_cb_lw_*` only — the older `_cb_balanced_logos_*` and `_cb_logo_soup_*` fallbacks were dropped in 1.4.0 | `META_*_LEGACY_KEYS`, `get_meta_array()` |
| Schema option | `coopbolo_schema_version` (1 = 1.3.5 migration done) | `SCHEMA_OPTION` |
| REST | `GET /wp-json/coopbolo/v1/collections` (was `cb-lw/v1` in 1.3.0–1.3.4), needs `edit_posts`, published only, **max 100**, ordered by title | `register_rest_routes()` |
| Admin menu | Top-level **Logo Wrangler** | `register_admin_menu()` |

## 1.3.5 migration

On `init` (priority 20, after other plugins register custom statuses), while `coopbolo_schema_version` is below 1, `migrate_legacy_data()` moves every `cb_logo_collection` post (any status, trash included) to `coopbolo_collection` with `set_post_type()`, and **copies** `_cb_lw_logos` / `_cb_lw_settings` to the `_coopbolo_*` keys. Details:
- An existing new key is never overwritten.
- A non-array value (for example, corrupted by a search-replace) is not copied.
- The old keys stay until the collection is next saved, so a rollback only has to change the post type back.
- It checks `get_post_type()` before each move, because a theme's `pre_get_posts` can rewrite the query and return other posts.
- The option is set only when every move succeeded. Deactivating the plugin deletes the option, so the migration runs again after a reinstall.
- Any WordPress load runs it, WP-CLI included.

Older `_cb_balanced_logos_*` / `_cb_logo_soup_*` keys are no longer read at all — 1.4.0 dropped those fallbacks (commit `a5bc12b`). Collections saved by 1.3.x still migrate automatically, and the `_cb_lw_*` fallback stays. Rollback steps: [[rapidsos-deploy]]. See [[0007-coopbolo-prefix]], [[0008-remove-old-product-names]].

## Legacy meta behavior

- **Read:** the new key wins whenever it exists, even if it is empty. Legacy keys are used only when `_coopbolo_*` does not exist yet (`metadata_exists`).
- **Write:** saving a collection writes `_coopbolo_*` and **deletes** all legacy keys, "so an intentional empty save cannot resurrect old logos" (code comment in `save_post`).
- Effect: old collections keep rendering after an upgrade. But once a collection has been saved under 1.3.x, any outside tool that writes `_cb_logo_soup_logos` is silently ignored. RapidSOS has such tools; see [[rapidsos-site]].
- **Bug (pre-existing, unfixed):** `save_post` unslashes `$_POST`, then `update_post_meta()` unslashes again, so a backslash in alt text or a link is lost on save (seen in wp-env 2026-10-22).

## Admin UX facts

- Live preview in the editor uses the same sanitizer as the frontend (`src/admin/collection-preview.js`, `to-lw-props.js`). 1.1.11 fixed a preview/frontend mismatch.
- One-click copy for the shortcode on the edit screen and list table (1.1.1).
- There is no bulk import (no CLI, no write REST). RapidSOS filled a 70-logo collection with a one-shot WPCode snippet instead (RapidSOS repo `deploy/wordpress/logo-soup/`).
- Cooper Bold credit in the wp-admin footer on these screens only (`class-coopbolo-admin-branding.php`, 1.1.6–1.1.10).

## Related

- [[rendering-pipeline]] · [[wordpress-plugin]] · [[rapidsos-site]] · [[gotchas]]
