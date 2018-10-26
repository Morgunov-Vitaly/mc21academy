const gulp = require('gulp'); 
const cleanCSS = require('gulp-clean-css'); // очистка и минимизация кода
const sourcemaps = require('gulp-sourcemaps'); //создание карты источников для дебага css
const autoprefixer = require('gulp-autoprefixer'); // расстановка вендорных префиксов
const sass = require('gulp-sass'); 
sass.compiler = require('node-sass');

/* Функция таска для оптимизации и преобразования нашего scss */
function styles(){
	return gulp.src('./scss/**/*.scss') /* берем источник ищет *.scss во всех вложенных дирректориях */
	/* .pipe() - преобразования пакета через вызов модулей gulp */
		.pipe(sourcemaps.init())
		.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
		.pipe(autoprefixer({
			browsers: ['>0.1%'],
			cascade: false
		}))
		.pipe(cleanCSS({
			level: 2
		}))
		.pipe(sourcemaps.write('./'))
		.pipe(gulp.dest('./')); /* вывод файлов в папку назначения */
}

gulp.task('styles', styles);

/* функция таска вотчера */
function watch(){
	gulp.watch('./scss/**/*.scss' , styles);		
}

gulp.task('watch', watch);