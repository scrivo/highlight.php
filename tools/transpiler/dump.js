'use strict';

/**
 * Canonical dump of a mode graph, shared format with dump.php.
 *
 * - Regular expressions are reduced to their source.
 * - Properties holding null/undefined and functions are dropped, keys sorted.
 * - Mode objects receive an `$id` on first visit; later visits emit `{$ref: id}`.
 * - Keyword maps (anything under a `keywords` key) are plain maps without identity.
 */
function canonicalize(root) {
    const ids = new Map();
    let next = 1;

    function walk(value, inKeywords) {
        if (value === null || value === undefined) {
            return null;
        }
        if (value instanceof RegExp) {
            return value.source;
        }
        if (Array.isArray(value)) {
            return value.map(function (v) { return walk(v, inKeywords); });
        }
        if (typeof value === 'object') {
            if (!inKeywords && ids.has(value)) {
                return { $ref: ids.get(value) };
            }
            const out = {};
            if (!inKeywords) {
                ids.set(value, next);
                out.$id = next++;
            }
            Object.keys(value).sort().forEach(function (key) {
                const v = value[key];
                if (v === null || v === undefined || typeof v === 'function') {
                    return;
                }
                out[key] = walk(v, inKeywords || key === 'keywords');
            });

            return out;
        }

        return value;
    }

    return walk(root, false);
}

module.exports = { canonicalize: canonicalize };

if (require.main === module) {
    // usage: node dump.js <highlight.js dir> <output dir> [language...]
    const fs = require('fs');
    const path = require('path');
    const { createHljs } = require('./hljs-env');

    const env = createHljs(process.argv[2]);
    const outDir = process.argv[3];
    const names = process.argv.length > 4 ? process.argv.slice(4) : env.languageNames();

    fs.mkdirSync(outDir, { recursive: true });
    names.forEach(function (name) {
        const dump = canonicalize(env.define(name));
        fs.writeFileSync(path.join(outDir, name + '.json'), JSON.stringify(dump, null, 1) + '\n');
    });
}
