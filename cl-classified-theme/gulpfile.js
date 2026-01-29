const project = require('./package.json');
const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));

const SassAutoprefix = require('less-plugin-autoprefix');
const autoprefix = new SassAutoprefix({browsers: ["> 1%", "last 2 versions"]});

const rtlcss = require('gulp-rtlcss');
var wpPot = require('gulp-wp-pot');
const minify = require("gulp-minify");
const uglify = require("gulp-uglify");
const cleanCSS = require("gulp-clean-css");
const beautify = require('gulp-jsbeautifier');
const clean = require('gulp-clean');
const rollup = require('gulp-better-rollup');
var zip = require('gulp-zip');


gulp.task('scss', function () {
    return gulp.src(['main.scss', 'rtl.scss'], {cwd: 'src/scss', sourcemaps: true})
        .pipe(sass({
            plugins: [autoprefix]
        }))
        .pipe(beautify({
            indent_char: '\t',
            indent_size: 1
        }))
        .pipe(gulp.dest('assets/css/', {sourcemaps: '.'}));
});

gulp.task('rtl', function () {
    return gulp.src([
        'assets/css/*.css',
        '!assets/css/font-awesome-all.min.css',
        '!assets/css/rtl.css'
    ])
        .pipe(rtlcss())
        .pipe(gulp.dest('assets/css-rtl/'));
});

gulp.task('minify-js', function () {
    return gulp.src([
        'assets/js/*.js',
        '!assets/js/bootstrap.bundle.js',
        '!assets/js/select2.js',
        '!assets/js/slick.func.js'
    ])
        .pipe(uglify())
        .pipe(gulp.dest('assets/js/minified'));
});

gulp.task("minify-css", function () {
    return (
        gulp.src(
            'assets/css/*.css',
            '!assets/css/all.min.css',
            '!assets/css/bootstrap.css',
            '!assets/css/select2.css',
        )
            .pipe(cleanCSS())
            .pipe(gulp.dest("assets/css/minified"))
    );
});

gulp.task('clean', function () {
    return gulp.src('__build/*.*', {read: false})
        .pipe(clean());
});

// Generate pot file
gulp.task('pot', function () {
    return gulp.src(['**/*.php', '!__*/**', '!src/**', '!assets/**'])
        .pipe(wpPot({
            domain: project.name,
            bugReport: 'techlabpro15@gmail.com',
            team: 'RadiusTheme <info@radiustheme.com>'
        }))
        .pipe(gulp.dest('languages/' + project.name + '.pot'));
});

gulp.task('zip', function () {
    return gulp.src(['**', '!__*/**', '!node_modules/**', '!composer.lock', '!package-lock.json', '!todo.txt', '!sftp-config.json'], {base: '..'})
        .pipe(zip(project.name + '.zip'))
        .pipe(gulp.dest('__build'));
});

gulp.task('watch', function () {
    gulp.watch('src/scss/**/*.scss', gulp.series('scss', 'rtl', 'minify-css', 'minify-js'));
});

// Build package
gulp.task('run', gulp.series('scss'));
gulp.task('build', gulp.series(gulp.parallel('run', 'pot', 'clean'), gulp.parallel('minify-css', 'minify-js', 'rtl'), 'zip'));

// Default Task
gulp.task('default', gulp.series('run', 'watch'));