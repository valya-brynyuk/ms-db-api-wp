// Include gulp
var gulp = require('gulp'); 

// Include Our Plugins
//var jshint = require('gulp-jshint');
var sass = require('gulp-sass');
var concat = require('gulp-concat');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var chmod = require('gulp-chmod');
var sourcemaps = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var plumber = require('gulp-plumber');
var notify = require('gulp-notify');
var livereload = require('gulp-livereload');

////meh, lint in sublime instead
//Lint Task
// gulp.task('lint', function() {
//     return gulp.src('js/*.js')
//         .pipe(jshint())
//         .pipe(jshint.reporter('default'));
// });

// Compile Our Sass
gulp.task('sass', function() {

    var sassError = function(err) {
      notify.onError({
        title:    "Gulp Sass",
        subtitle: "Try again!",
        message:  "Error: <%= error.message %>",
        sound:    "Beep"
      })(err);
      this.emit('end');
    };

    return gulp.src('scss/*.scss')
        .pipe(plumber({errorHandler: sassError}))
        .pipe(sourcemaps.init())
        .pipe(sass())
        //.pipe(sourcemaps.write({includeContent: false})) //tempfix for bugs with sass + sourcemaps
        //.pipe(sourcemaps.init({loadMaps: true})) //tempfix for bugs with sass + sourcemaps
        .pipe(autoprefixer())
        .pipe(sourcemaps.write('maps'))
        .pipe(gulp.dest('./'))
        .pipe(livereload());
});

// Concatenate & Minify JS
gulp.task('scripts', function() {

    var jsConcatError = function(err) {
      notify.onError({
        title:    "Gulp jsConcat",
        subtitle: "Try again!",
        message:  "Error: <%= error.message %>",
        sound:    "Beep"
      })(err);
      this.emit('end');
    };

    return gulp.src(['js/!(functions)*.js','js/functions.js','!js/ie/*.js'])
        .pipe(plumber({errorHandler: jsConcatError}))
        .pipe(concat('all.js'))
        //.pipe(chmod(644))
        .pipe(gulp.dest('dist'))
        .pipe(rename('all.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('dist'));
});

// // Concatenate & Minify JS for IE ONLY
// gulp.task('scripts-ie', function() {

//     var jsIEError = function(err) {
//       notify.onError({
//         title:    "Gulp jsIE",
//         subtitle: "Try again!",
//         message:  "Error: <%= error.message %>",
//         sound:    "Beep"
//       })(err);
//       this.emit('end');
//     };

//     return gulp.src(['js/ie/!(ie-functions)*.js','js/ie/ie-functions.js'])
//     //return gulp.src(['js/ie/respond.js','js/ie/!(ie-functions)*.js','js/ie/ie-functions.js'])
//     //return gulp.src(['js/ie/respond.js','js/ie/modernizr.js','js/ie/rem.js','js/ie/ie-functions.js'])
//         .pipe(plumber({errorHandler: jsIEError}))
//         .pipe(concat('ie.js'))
//         .pipe(gulp.dest('dist'))
//         .pipe(rename('ie.min.js'))
//         .pipe(uglify())
//         .pipe(gulp.dest('dist'));
// });

// Watch Files For Changes
gulp.task('watch', function() {
    livereload.listen();
    gulp.watch('js/**/*.js', ['scripts']);
    gulp.watch('scss/**/*.scss', ['sass']);
});

// Default Task
gulp.task('default', ['sass','scripts','watch']);