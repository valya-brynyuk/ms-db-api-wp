const {
  src,
  dest,
  parallel,
  series,
  watch
} = require('gulp');

// Load plugins

const terser = require('gulp-terser');
const rename = require('gulp-rename');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const concat = require('gulp-concat');
const changed = require('gulp-changed');
const sourcemaps = require('gulp-sourcemaps');
// const cssnano = require('gulp-cssnano');
// const clean = require('gulp-clean');
// const imagemin = require('gulp-imagemin');

//adding
const order = require('gulp-order');

const browsersync = require('browser-sync').create();

// Clean assets

// function clear() {
//   return src('./assets/*', {
//           read: false
//       })
//       .pipe(clean());
// }

// JS function 

function js() {
  const source = 'js/**/*.js';
  // const source = ['js/!(functions)*.js','js/functions.js'];

  return src(source)
      .pipe(changed(source))
      .pipe(order([
        'js/!(functions)*.js',
        'js/functions.js'
      ], { base: './' }))
      .pipe(concat('all.js'))
      .pipe(dest('dist'))
      .pipe(terser())
      .pipe(rename({
          extname: '.min.js'
      }))
      .pipe(dest('dist'))
      .pipe(browsersync.stream());

    return merge ()
}

// CSS function 

function css() {
  const source = 'scss/*.scss';

  return src(source)
      .pipe(sourcemaps.init())
      .pipe(changed(source))
      .pipe(sass())
      .pipe(autoprefixer())
      .pipe(sourcemaps.write('maps'))
      // .pipe(rename({
      //     extname: '.min.css'
      // }))
      // .pipe(cssnano())
      .pipe(dest('./'))
      // .pipe(browsersync.stream());
}

// Optimize images

// function img() {
//   return src('./src/img/*')
//       .pipe(imagemin())
//       .pipe(dest('./assets/img'));
// }

// Watch files

function watchFiles() {
  watch('scss/**/*.scss', css);
  watch('js/**/*.js', js);
  // watch('./src/img/*', img);
}

// BrowserSync

// function browserSync() {
//   browsersync.init({
//       server: {
//           baseDir: './'
//       },
//       port: 3000
//   });
// }

// Tasks to define the execution of the functions simultaneously or in series

exports.watch = parallel(watchFiles);
exports.default = parallel(js, css);