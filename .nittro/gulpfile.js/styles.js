"use strict";

const gulp = require('gulp'),
    nittro = require('gulp-nittro'),
    { nonMinified, onlyLess, mapFile } = require('./utils'),
    less = require('gulp-less'),
    postcss = require('gulp-postcss'),
    autoprefixer = require('autoprefixer'),
    cssnano = require('cssnano'),
    sourcemaps = require('gulp-sourcemaps'),
    concat = require('gulp-concat');


module.exports = function styles(builder, dest) {
    return nittro('css', builder)
        .pipe(sourcemaps.init({ loadMaps: true }))
        .pipe(onlyLess(() => less()))
        .pipe(nonMinified(() => postcss([ autoprefixer(), cssnano() ])))
        .pipe(concat(dest))
        .pipe(sourcemaps.write('.', { mapFile }))
        .pipe(gulp.dest('public/css'));
};
