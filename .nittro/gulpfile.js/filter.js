"use strict";

const through2 = require('through2');

module.exports = function filter(predicate, factory) {
    return through2.obj(function(chunk, enc, callback) {
        if (!predicate(chunk)) {
            return callback(null, chunk);
        }

        const substream = factory();
        const self = this;

        substream.on('data', function(chunk) {
            self.push(chunk);
        });

        substream.on('error', function (err) {
            callback(err);
        });

        substream.on('end', function () {
            callback();
        });

        substream.end(chunk);
    });
};
