'use strict';

// usage: node compare.js <js dump dir> <php dump dir>
const fs = require('fs');
const path = require('path');
const assert = require('assert');

const [jsDir, phpDir] = process.argv.slice(2);
let failed = 0;
const files = fs.readdirSync(jsDir).filter(function (f) { return f.endsWith('.json'); }).sort();

files.forEach(function (file) {
    const js = JSON.parse(fs.readFileSync(path.join(jsDir, file), 'utf8'));
    const phpFile = path.join(phpDir, file);
    if (!fs.existsSync(phpFile)) {
        failed++;
        console.log('MISSING ' + file);

        return;
    }
    const php = JSON.parse(fs.readFileSync(phpFile, 'utf8'));
    try {
        assert.deepStrictEqual(php, js);
    } catch (e) {
        failed++;
        console.log('DIFF ' + file + firstDifference(js, php, ''));
    }
});

function firstDifference(a, b, at) {
    if (typeof a !== typeof b || Array.isArray(a) !== Array.isArray(b) || a === null || b === null) {
        return a === b ? '' : ' at ' + at + ': js=' + short(a) + ' php=' + short(b);
    }
    if (typeof a !== 'object') {
        return a === b ? '' : ' at ' + at + ': js=' + short(a) + ' php=' + short(b);
    }
    const keys = Array.from(new Set(Object.keys(a).concat(Object.keys(b))));
    for (const k of keys) {
        if (!(k in a) || !(k in b)) return ' at ' + at + '.' + k + ': js=' + short(a[k]) + ' php=' + short(b[k]);
        const d = firstDifference(a[k], b[k], at + '.' + k);
        if (d) return d;
    }

    return '';
}

function short(v) {
    const s = JSON.stringify(v);

    return s === undefined ? 'undefined' : (s.length > 100 ? s.slice(0, 100) + '...' : s);
}

console.log((files.length - failed) + ' of ' + files.length + ' language definitions match highlight.js');
process.exit(failed ? 1 : 0);
