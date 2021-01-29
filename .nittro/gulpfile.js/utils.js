"use strict";

const filter = require('./filter');

exports.nonMinified = (factory) => filter(file => !/\.min\.[^.]+$/i.test(file.path), factory);
exports.onlyLess = (factory) => filter(file => /\.less$/i.test(file.path), factory);
exports.mapFile = (path) => path.replace(/\.[^.]+(?=\.map$)/, '');
