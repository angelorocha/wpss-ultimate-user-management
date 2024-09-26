'use strict';

const {series} = require('gulp');
const {parallel} = require('gulp');
const gulp = require('gulp');
const sass = require('gulp-sass')(require('node-sass'));
const uglifycss = require('gulp-uglifycss');
const uglifyjs = require('gulp-uglify');
const concat = require('gulp-concat');
const cleancss = require('gulp-clean-css');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');

/** Sass task */
function wpss_sass() {
    return gulp.src('./assets/src/sass/*.scss')
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', sass.logError))
        .pipe(uglifycss({"uglyComments": true}))
        .pipe(cleancss())
        .pipe(autoprefixer({
            cascade: false
        }))
        .pipe(concat('main.min.css'))
        .pipe(sourcemaps.write('./',{addComment: true}))
        .pipe(gulp.dest('./assets/css'))
}

/** JS task */
function wpss_jsmin() {
    return gulp.src('./assets/src/js/*.js')
        .pipe(sourcemaps.init())
        .pipe(uglifyjs())
        .pipe(concat('js.min.js'))
        .pipe(sourcemaps.write('./',{addComment: true}))
        .pipe(gulp.dest('./assets/js'))
}

/** Watch tasks */
function wpss_watch_css() {
    return gulp.watch('./assets/src/sass/*.scss', wpss_sass);
}

function wpss_watch_js() {
    return gulp.watch('./assets/src/js/*.js', wpss_jsmin);
}

/** Default tasks */
exports.watch = parallel(wpss_watch_css, wpss_watch_js);