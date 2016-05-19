var gulp = require('gulp');
var source = require('vinyl-source-stream'); // Used to stream bundle for further handling
var browserify = require('browserify');
var watchify = require('watchify');
var gulpif = require('gulp-if');
var notify = require('gulp-notify');
//var concat = require('gulp-concat');
var gutil = require('gulp-util');
//var shell = require('gulp-shell');
//var glob = require('glob');
var livereload = require('gulp-livereload');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var gulpIgnore = require('gulp-ignore');
var eslint = require('gulp-eslint');
//var config = require('./gulp/config');
var runSequence = require('run-sequence');

var browserifyTask = function (options, callback) {

  // Our app bundler
    var appBundler = browserify({
        entries: [options.src], // Only need initial file, browserify finds the rest
        standalone: 'partikkel_buy_button',
        debug: false, // Gives us sourcemapping
        cache: {}, packageCache: {}, fullPaths: true // Requirement of watchify
    });

  // The rebundle process
  var rebundle = function () {
    var start = Date.now();
    console.log('Building APP bundle');
    appBundler.bundle()
      .on('error', gutil.log)
      .pipe(source('partikkel_buy_button.js'))
      .pipe(gulp.dest(options.dest))
      .pipe(gulpif(options.development, livereload()))
      .pipe(notify(function () {
        console.log('APP bundle built in ' + (Date.now() - start) + 'ms');
      }));
  };

  // Fire up Watchify when developing
  if (options.development) {
    appBundler = watchify(appBundler);
    appBundler.on('update', rebundle);
  }
        
  rebundle();
};

eslint: {
    src: ['src/**/*.js', 'src/**/*.jsx']
  }

var lint = function() {
  return gulp.src(eslint.src)
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(eslint.failOnError());
};

gulp.task('lint', lint);
gulp.task('lintWatch', gulp.watch.bind(gulp, eslint.src, ['lint']));

gulp.task('copyCss', function(){
  return gulp.src('css/*.css')
    .pipe(gulp.dest('dist/css'))
    .pipe(notify(function () {
      console.log('CSS copied');
    }));    
});

gulp.task('deploy', ['deploy-build', 'copyCss'], function() {
  return gulp.src('dist/*.js')
    .pipe(gulpIgnore.exclude('*.min.js'))
    .pipe(uglify())
    .pipe(rename({ extname: '.min.js'}))
    .pipe(gulp.dest('dist'))
    .pipe(notify(function () {
      console.log('Bundles uglified');
    }));
});

// Starts our development workflow
gulp.task('build', function () {

  browserifyTask({
    development: true,
    src: './src/app.js',
    dest: './dist'
  });
});

gulp.task('deploy-build', function (callback) {

  browserifyTask({
    development: false,
    src: './src/app.js',
    dest: './dist'
  }, callback);
});

gulp.task('default', function(callback) {
  runSequence(
    'copyCss', ['build', 'lintWatch'],
    callback);
});