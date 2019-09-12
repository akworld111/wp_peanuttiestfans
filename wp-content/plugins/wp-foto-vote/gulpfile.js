///////////////
// Variables //
///////////////
var gulp         = require( 'gulp' ),
	// sass         = require( 'gulp-sass' ),
	// sourcemaps   = require( 'gulp-sourcemaps' ),
	autoprefixer = require( 'gulp-autoprefixer' ),
	uglify       = require( 'gulp-uglify' ),
	concat       = require( 'gulp-concat' ),
	rename = require('gulp-rename'),
	cleanCSS = require('gulp-clean-css'),
    path = require("path");
	// order        = require( 'gulp-order' );

var script_sources = [
    'assets/js/fv_main.js',
    'assets/js/fv_modal.js',
    'assets/js/fv_upload.js',
    'assets/js/fv_lib.js',
    /*
    // !!Rarely needed!!
    'assets/everc/js/everc.js',
    'assets/image-lightbox/jquery.image-lightbox.js',
    'assets/vendor/jquery.unveil.js',
    'addons/final-countdown/assets/fv-final-countdown.js',
    'addons/countdown-default/assets/fv-countdown-default.js',
    */
];

var	style_sources  = [
		'assets/css/fv_main.css',
        'assets/image-lightbox/jquery.image-lightbox.css',
	];

gulp.task( 'styles', function(callback) {


	// we use the array map function to map each
	// entry in our configuration array to a function
	var tasks = style_sources.map(function(src) {
		// the parameter we get is this very entry. In
		// that case, an object containing src, name and
		// dest.
		// So here we create a Gulp stream as we would
		// do if we just handle one set of files
		return gulp.src(src)
			.pipe(concat(path.basename(src)))
            .pipe(rename({suffix: '.min'}))
            .pipe(autoprefixer({
                browsers: [
                    'last 2 versions',
                    'ie 8',
                    'ie 9',
                    'android 2.3',
                    'android 4',
                    'opera 12'
                ]
            }))
            .pipe(cleanCSS({keepSpecialComments :1, advanced: true}))
            .pipe(gulp.dest(function(file) {
                return file.base;
            }, {overwrite: true}));
	});
	// tasks now includes an array of Gulp streams. Use
	// the `merge-stream` module to combine them into one
	//return merge(tasks);

    callback();

});

///////////
// Tasks //
///////////
// gulp.task( 'styles', function() {
//
//     gulp.src( style_sources )
//     	.pipe( sourcemaps.init() )
//         .pipe( sass( { outputStyle: 'compressed' } ).on( 'error', sass.logError ) )
//         .pipe( sourcemaps.write() )
//         .pipe( autoprefixer() )
//         .pipe( gulp.dest( style_target ) );
// } );
//
gulp.task( 'scripts', function(callback) {

    var tasks = script_sources.map(function(src) {

        return gulp.src(src)
            .pipe(concat(path.basename(src)))
            .pipe(rename({suffix: '.min'}))
            .pipe(uglify())
            .pipe(gulp.dest(function(file) {
                return file.base;
            }, {overwrite: true}));

    });

    // gulp.src( script_sources )
    //     .pipe( sourcemaps.init() )
    // 	.pipe( order( script_order, { base: 'assets/src/js/' } ) )
	// 	.pipe( concat( 'scripts.min.js' ) )
	// 	.pipe( uglify() )
    //     .pipe( sourcemaps.write() )
    //     .pipe( gulp.dest( script_target ) );

    callback();
} );

/////////////////////
// Default - Build //
/////////////////////
gulp.task( 'default', [ 'styles', 'scripts' ] );

