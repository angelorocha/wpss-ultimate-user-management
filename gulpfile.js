'use strict';

const { series, parallel } = require('gulp');
const gulp = require('gulp');
const fs = require('fs');
const sass = require('gulp-sass')(require('sass-embedded'));
const uglifycss = require('gulp-uglifycss');
const uglifyjs = require('gulp-uglify');
const concat = require('gulp-concat');
const cleancss = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');

/** Task para ler a versão do PHP e gravar no package.json */
function sync_version_from_php(done) {
	const phpFilePath = './wpss-ultimate-user-management.php';
	const pkgFilePath = './package.json';
	if (fs.existsSync(phpFilePath)) {
		const phpContent = fs.readFileSync(phpFilePath, 'utf8');
		const match = phpContent.match(/(?:Version:|\* @version)\s*([\d\.]+)/i);
		if (match && match[1]) {
			const phpVersion = match[1];
			const pkg = JSON.parse(fs.readFileSync(pkgFilePath, 'utf8'));
			if (pkg.version !== phpVersion) {
				pkg.version = phpVersion;
				fs.writeFileSync(pkgFilePath, JSON.stringify(pkg, null, 2) + '\n');
				console.log(`\x1b[32m[Version Sync]\x1b[0m package.json updated to v${phpVersion}`);
			}
		}
	}
	done();
}

/** Sass task */
function wpss_sass() {
	return gulp.src('./assets/src/sass/*.scss')
		.pipe(sourcemaps.init())
		.pipe(sass().on('error', sass.logError))
		.pipe(uglifycss({"uglyComments": true}))
		.pipe(cleancss())
		.pipe(autoprefixer({ cascade: false }))
		.pipe(concat('main.min.css'))
		.pipe(sourcemaps.write('./',{addComment: true}))
		.pipe(gulp.dest('./assets/css'));
}

/** JS task */
function wpss_jsmin() {
	return gulp.src('./assets/src/js/*.js')
		.pipe(sourcemaps.init())
		.pipe(uglifyjs())
		.pipe(concat('js.min.js'))
		.pipe(sourcemaps.write('./',{addComment: true}))
		.pipe(gulp.dest('./assets/js'));
}

/** Watch tasks */
function wpss_watch_css() {
	return gulp.watch('./assets/src/sass/*.scss', wpss_sass);
}

function wpss_watch_js() {
	return gulp.watch('./assets/src/js/*.js', wpss_jsmin);
}

/** Exported tasks */
exports.syncVersion = sync_version_from_php;
exports.watch = series(sync_version_from_php, parallel(wpss_watch_css, wpss_watch_js));
exports.build = series(sync_version_from_php, parallel(wpss_sass, wpss_jsmin));
exports.default = series(sync_version_from_php, parallel(wpss_sass, wpss_jsmin));
