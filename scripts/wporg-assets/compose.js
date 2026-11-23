// Compose the WordPress.org icon and banners into assets/ from the Cooper Bold logo files
// (cooper-bold-wordmark.svg and cooper-bold-c.png, taken from cooperbold.com).
// Run: NODE_PATH=node_modules node scripts/wporg-assets/compose.js (needs network for the Inter font).
const pw = require('playwright-core'); const fs = require('fs'); const path = require('path');
const dir = __dirname, out = path.resolve(dir, '..', '..', 'assets');
const svg = fs.readFileSync(path.join(dir, 'cooper-bold-wordmark.svg'), 'utf8').replace('width="156" height="84"', 'viewBox="0 0 156 84"');
const c64 = fs.readFileSync(path.join(dir, 'cooper-bold-c.png')).toString('base64');
const font = '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@500;700;800&display=swap" rel="stylesheet">';
const banner = (w, h) => `<!doctype html><html><head>${font}<style>
 html,body{margin:0;width:${w}px;height:${h}px;background:#111;overflow:hidden;font-family:Inter,Helvetica,Arial,sans-serif}
 .wrap{position:absolute;inset:0;display:flex;align-items:center;padding:0 ${w*0.07}px;gap:${w*0.055}px}
 .mark{width:${w*0.205}px;flex:none} .mark svg{width:100%;height:auto;display:block}
 .rule{width:${Math.max(2,w/386)}px;height:${h*0.46}px;background:#2b2b2b;flex:none}
 h1{margin:0;color:#fff;font-weight:800;font-size:${h*0.2}px;letter-spacing:-0.02em;line-height:1}
 p{margin:${h*0.07}px 0 0;color:#D6C2AC;font-weight:500;font-size:${h*0.077}px;line-height:1.3}
 .dots{display:flex;gap:${h*0.03}px;margin-top:${h*0.085}px} .dots span{display:block;height:${h*0.034}px;border-radius:99px}
</style></head><body><div class="wrap"><div class="mark">${svg}</div><div class="rule"></div><div>
 <h1>Logo Wrangler</h1><p>Mismatched partner logos, balanced into one clean row.</p>
 <div class="dots"><span style="width:${h*0.13}px;background:#72DFB5"></span><span style="width:${h*0.07}px;background:#8481B8"></span><span style="width:${h*0.034}px;background:#D6C2AC"></span></div>
</div></div></body></html>`;
const icon = (s) => `<!doctype html><html><body style="margin:0;width:${s}px;height:${s}px;background:#111;display:flex;align-items:center;justify-content:center;overflow:hidden"><img src="data:image/png;base64,${c64}" style="width:${s*0.66}px;height:${s*0.66}px"></body></html>`;
(async () => {
	const b = await pw.chromium.launch();
	for (const [w, h, name] of [[772, 250, 'banner-772x250.png'], [1544, 500, 'banner-1544x500.png']]) {
		const p = await b.newPage({ viewport: { width: w, height: h } });
		await p.setContent(banner(w, h), { waitUntil: 'networkidle' });
		await p.evaluate(() => document.fonts.ready);
		console.log(name, 'font:', await p.evaluate(() => document.fonts.check('800 20px Inter') ? 'Inter' : 'fallback'));
		await p.screenshot({ path: path.join(out, name) }); await p.close();
	}
	for (const s of [256, 128]) {
		const p = await b.newPage({ viewport: { width: s, height: s } });
		await p.setContent(icon(s)); await p.screenshot({ path: path.join(out, `icon-${s}x${s}.png`) }); await p.close();
	}
	await b.close();
})();
