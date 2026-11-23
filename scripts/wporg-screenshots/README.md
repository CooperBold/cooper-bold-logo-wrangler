# WordPress.org directory screenshots

Real captures for `assets/screenshot-1.png` (block editor) and `assets/screenshot-2.png`
(frontend strip), using fictional demo logos. Local wp-env only.

```bash
python3 scripts/wporg-screenshots/make-demo-logos.py          # uncommitted/demo-logos/*.png
WP_ENV_CORE=~/wp-env-wordpress-core npm run wp-env:start      # from ~/Repos/cooper-bold-logo-wrangler-wp-plugin
npx wp-env run cli -- wp option update blogname "Example Studio"
npx wp-env run cli -- wp eval-file wp-content/plugins/cooper-bold-logo-wrangler-wp-plugin/scripts/wporg-screenshots/seed.php
NODE_PATH=node_modules node scripts/wporg-screenshots/shoot.js <page id from seed> /tmp/shots
cp /tmp/shots/editor.png assets/screenshot-1.png; cp /tmp/shots/frontend.png assets/screenshot-2.png
```

Banner and icon: `NODE_PATH=node_modules node scripts/wporg-assets/compose.js` (from the Cooper Bold logo files in that folder).
