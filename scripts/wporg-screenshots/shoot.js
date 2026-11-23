// node shoot.js <pageId> <outDir>  (local wp-env only)
const pw = require('playwright-core');
const [pageId, out] = process.argv.slice(2);
(async () => {
	const browser = await pw.chromium.launch();
	const ctx = await browser.newContext({ viewport: { width: 1200, height: 900 }, deviceScaleFactor: 1 });
	const page = await ctx.newPage();
	const errors = [];
	page.on('pageerror', (e) => errors.push(String(e).slice(0, 160)));
	// Frontend, logged out.
	await page.goto(`http://localhost:8888/?page_id=${pageId}`);
	await page.waitForFunction(() => { const r = document.querySelector('.cb-lw-inner [role=list]'); return r && getComputedStyle(r).opacity === '1' && [...r.querySelectorAll('img')].every((i) => i.complete && i.naturalWidth > 0) && r.querySelectorAll('img').length === 8; }, null, { timeout: 20000 });
	await page.waitForTimeout(800);
	const info = await page.evaluate(() => ({ react: typeof window.React, imgs: [...document.querySelectorAll('.cb-lw-inner img')].map((i) => [i.alt, i.width, i.height]) }));
	await page.screenshot({ path: `${out}/frontend.png`, clip: { x: 0, y: 0, width: 1200, height: 660 } });
	// Editor, logged in with wp-env's documented local defaults.
	await page.goto('http://localhost:8888/wp-login.php');
	await page.fill('#user_login', 'admin');
	await page.fill('#user_pass', 'password');
	await page.click('#wp-submit');
	await page.waitForLoadState('networkidle');
	await page.goto(`http://localhost:8888/wp-admin/post.php?post=${pageId}&action=edit`);
	await page.waitForFunction(() => window.wp && wp.data && wp.data.select('core/block-editor').getBlocks().length > 0, null, { timeout: 30000 });
	await page.evaluate(() => {
		const prefs = wp.data.dispatch('core/preferences');
		prefs.set('core/edit-post', 'welcomeGuide', false);
		prefs.set('core', 'fixedToolbar', true);
		const blocks = wp.data.select('core/block-editor').getBlocks();
		const lw = blocks.find((b) => b.name === 'cooper-bold/logo-wrangler');
		wp.data.dispatch('core/block-editor').selectBlock(lw.clientId);
		wp.data.dispatch('core/edit-post').openGeneralSidebar('edit-post/block');
	});
	await page.keyboard.press('Escape').catch(() => {});
	const frame = page.frameLocator('iframe[name="editor-canvas"]');
	await frame.locator('[data-type="cooper-bold/logo-wrangler"] img').nth(7).waitFor({ timeout: 20000 });
	await page.waitForTimeout(2500);
	const editorInfo = await page.evaluate(() => ({ sidebar: !!document.querySelector('.interface-complementary-area'), modal: !!document.querySelector('.components-modal__frame') }));
	await page.screenshot({ path: `${out}/editor.png` });
	console.log(JSON.stringify({ info, editorInfo, errors }));
	await browser.close();
})().catch((e) => { console.error('FAILED', e.message); process.exit(1); });
