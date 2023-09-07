/**
 * The Great GulpFile
 * @Author Vishwa LiyanaArachchi
 *
 * @project <%= userOpts.wpcpt_pluginName %> 
 * @package <%= userOpts.wpcpt_pluginSlug %>
 * @since <%= userOpts.wpcpt_pluginName %> 1.01
 * 
 * @functionality : Sass to CSS (minified)
 *                : Sourcemaps
 *                : JS Concatenate :: Incomplete
 *                : Watcher with Browser-sync
 */

const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const browserSync = require('browser-sync').create();
const postCSS = require("gulp-postcss")
const cssNano = require('cssnano');
// const terser        = require("gulp-terser");
const { src, dest, watch, series } = require('gulp');
// const renameFile    = require("gulp-rename");
const sourcemaps = require('gulp-sourcemaps');

// const connectPHP = require('gulp-connect-php');


// 0. Verify Gulp is workin
function theBasics(basic_cback) {
    console.log("The Great GulpFile is running...");
    basic_cback();
}

// 1. Compile and minify SCSS -> CSS
function sassCompiler(sassCallback) {
    console.log("Starting SASS Ops.");

    // Set source file
    //  return gulp.src(['./assets/style.scss'], { sourcemaps:true })
    return gulp.src(['./src/scss/styles.scss'], { sourcemaps: true })

        // Sourcemaps
        .pipe(sourcemaps.init())
        // Compile SASS
        .pipe(sass().on('error', sass.logError))

        // minify compiled
        .pipe( postCSS( [cssNano()] ) )

        // write sourcemap to a file
        .pipe(sourcemaps.write('.'))

        // save css file
        .pipe(gulp.dest('./src/css/'), { sourcemaps: '.' });

    // .pipe( console.log("Finished SASS Ops.") );
    // console.log("Finished SASS Ops.");

}

// 2. Minify and/or concatenate JS 
function jsCompiler(jsCallback) {
    console.log("Starting JS Tasks");
    jsCallback();
}

// 3. Watcher Tasks 
function watchProject(watcher_cb) {
    browserSync.init({
        proxy: "http://localhost/walk/wp-admin/"
    });

    gulp.watch('./**/*.scss', series(sassCompiler));

    gulp.watch("./**/*.scss").on('change', series(sassCompiler));
    gulp.watch("./**/*.scss").on('change', browserSync.reload);
    gulp.watch("./**/*.php").on('change', browserSync.reload);
    gulp.watch("./**/*.js").on('change', browserSync.reload);

    // connectPHP.server({}, function (){
    //     browserSync({
    //         proxy: '127.0.0.1:8000'
    //     });
    // });


    watcher_cb();
}


// =====================================
//              Task Actions 
// =====================================

exports.default = series(
    theBasics,
    sassCompiler,
    // jsCompiler,
    watchProject,
);

exports.doSASS = series(sassCompiler);
// exports.doJS = series(jsCompiler);
exports.watcher = series(sassCompiler, watchProject,);



 // exports.butcher = function() {
 //     // You can use a single task
 //     // watch('src/*.css', css);
 //     // Or a composed task
 //     watch('./assets/scss/*.scss', series(sassCompiler));
 // };
