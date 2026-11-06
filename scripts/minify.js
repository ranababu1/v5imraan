/**
 * Builds the .min.css / .min.js copies the theme enqueues.
 *
 * Run from the theme root after editing any source file:
 *   npm i --no-save csso terser && node scripts/minify.js
 *
 * Sources stay as the files you edit; the .min files are generated.
 */
const fs = require('fs');
const path = require('path');
const csso = require('csso');
const { minify } = require('terser');

const root = path.resolve(__dirname, '..');
const css = ['style.css', 'style-rtl.css', 'css/blog.css', 'css/timeline.css'];
const js = ['js/main.js', 'js/navigation.js', 'js/timeline.js'];

(async () => {
	for (const f of css) {
		const src = fs.readFileSync(path.join(root, f), 'utf8');
		const out = csso.minify(src, { restructure: false, comments: false }).css;
		fs.writeFileSync(path.join(root, f.replace(/\.css$/, '.min.css')), out + '\n');
		console.log(f, src.length, '->', out.length);
	}
	for (const f of js) {
		const src = fs.readFileSync(path.join(root, f), 'utf8');
		const out = (await minify(src, { compress: true, mangle: true, format: { comments: false } })).code;
		fs.writeFileSync(path.join(root, f.replace(/\.js$/, '.min.js')), out + '\n');
		console.log(f, src.length, '->', out.length);
	}
})().catch((e) => { console.error(e); process.exit(1); });
