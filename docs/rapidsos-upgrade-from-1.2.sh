#!/usr/bin/env bash
# One-time upgrade of a RapidSOS site from Logo Soup 1.2.11 to Cooper Bold Logo Wrangler 1.5.2+.
#
# Usage:  WP="wp --path=/path/to/site" docs/rapidsos-upgrade-from-1.2.sh <plugin-zip> [--dry-run]
# WP is the WP-CLI command for the target site (default: wp).
# Take a database backup first: it is the only way back to 1.2.11. The collections change post
# type, and saving one in 1.5.2 deletes its 1.2.x meta.
#
# What it does, in order:
#   1. Installs the Logo Wrangler zip, deactivates the old plugin, activates the new one.
#      The plugin itself moves the collections and their 1.2.x logo lists (1.5.2+).
#   2. Content: [logo_soup ...] -> [logo_wrangler ...]  (Bricks page content, the Logo Reel
#      component in bricks_components, posts). 1.4.0+ no longer registers [logo_soup].
#   3. CSS/markup names: cb-logo-soup -> cb-lw  (Bricks custom CSS on the software-integrations
#      grid, the Logo Reel edge fade, the "Logo slider smooth scroll" WPCode snippet).
#      The rsos_bricks_components_backup_* options get the same rename, so they stay restorable.
#   4. Regenerates Bricks CSS files and flushes the object cache.
#
# The logo pages look broken from step 1 until step 4 finishes (a few minutes). Purge Breeze,
# Varnish and the Cloudways cache right after the script so no page cached in that window stays.
set -euo pipefail

ZIP="${1:?plugin zip path required}"
DRY="${2:-}"
WP="${WP:-wp}"
P="$($WP db prefix)"
TABLES=("${P}postmeta" "${P}options" "${P}posts")

run() { echo "+ $*"; [ "$DRY" = "--dry-run" ] || "$@"; }

echo "== before"
$WP plugin list --fields=name,status,version | grep -iE 'logo' || true

echo "== 1. plugin"
run $WP plugin install "$ZIP" --force
run $WP plugin deactivate cooper-bold-logo-soup
run $WP plugin activate cooper-bold-logo-wrangler

SR_FLAGS=(--precise --report-changed-only)
[ "$DRY" = "--dry-run" ] && SR_FLAGS+=(--dry-run)

echo "== 2. shortcodes"
# Matches [logo_soup], [logo_soup attr=…] and a tag followed by a line break.
$WP search-replace '\[logo_soup(?=[\s\]])' '[logo_wrangler' "${TABLES[@]}" --regex "${SR_FLAGS[@]}"

echo "== 3. class and attribute names"
$WP search-replace 'cb-logo-soup' 'cb-lw' "${TABLES[@]}" "${SR_FLAGS[@]}"

echo "== 4. rebuild"
run $WP bricks regenerate_assets
run $WP cache flush

echo "== after"
$WP plugin list --fields=name,status,version | grep -iE 'logo' || true
$WP post list --post_type=coopbolo_collection --fields=ID,post_name,post_status
