"use strict";

const gulp = require('gulp'),
    nittro = require('gulp-nittro'),
    { nonMinified, mapFile } = require('./utils'),
    uglify = require('gulp-uglify'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat');


module.exports = function scripts(builder, dest) {
    return nittro('js', builder)
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(nonMinified(() => uglify({compress: false, mangle: false})))
        .pipe(concat(dest))
        .pipe(sourcemaps.write('.', { mapFile }))
        .pipe(gulp.dest('public/js'));
};
