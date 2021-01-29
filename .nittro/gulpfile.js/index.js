"use strict";

const gulp = require('gulp');
const nittro = require('gulp-nittro');
const scripts = require('./scripts');
const styles = require('./styles');

const builder = new nittro.Builder({
    vendor: {
        js: [],
        css: []
    },
    base: {
        core: true,
        datetime: true,
        neon: true,
        di: true,
        ajax: true,
        forms: true,
        page: true,
        flashes: true,
        routing: false
    },
    extras: {
        checklist: false,
        dialogs: true,
        confirm: true,
        dropzone: false,
        paginator: false,
        keymap: false,
        storage: false
    },
    libraries: {
        js: [],
        css: []
    },
    bootstrap: true,
    stack: true
});

exports.js = function nittroJs() {
    return scripts(builder, 'nittro.min.js');
};

exports.css = function nittroCss() {
    return styles(builder, 'nittro.min.css');
};

exports.default = gulp.parallel(exports.js, exports.css);
