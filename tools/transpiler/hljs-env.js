'use strict';

/**
 * Builds a minimal `hljs` object from a highlight.js source checkout so that
 * language definitions can be evaluated in Node without a build step.
 *
 * Only what language definitions use at definition time is provided: the
 * common regexes and modes, `inherit`, `COMMENT`, `getLanguage` and
 * `requireLanguage`.
 */

const fs = require('fs');
const path = require('path');

function stripModuleSyntax(code) {
    return code
        .replace(/^import .*$/gm, '')
        .replace(/^export default function/m, 'module.exports = function')
        .replace(/^export (const|function) /gm, '$1 ');
}

function evaluate(code, exportNames) {
    const module = { exports: {} };
    const body = stripModuleSyntax(code) + '\n' +
        (exportNames ? 'Object.assign(module.exports, {' + exportNames.join(',') + '});' : '');
    // eslint-disable-next-line no-new-func
    new Function('module', 'inherit', body)(module, module.exports.inherit);

    return module.exports;
}

function exportedNames(code) {
    const names = [];
    const re = /^export (?:const|function) ([A-Za-z_$][\w$]*)/gm;
    let m;
    while ((m = re.exec(code)) !== null) {
        names.push(m[1]);
    }

    return names;
}

function deepFreeze(o) {
    Object.freeze(o);
    Object.getOwnPropertyNames(o).forEach(function (prop) {
        if (o[prop] !== null && typeof o[prop] === 'object' && !Object.isFrozen(o[prop])) {
            deepFreeze(o[prop]);
        }
    });

    return o;
}

/**
 * @param {string} hljsDir path to a highlight.js checkout
 */
function createHljs(hljsDir) {
    const libDir = path.join(hljsDir, 'src', 'lib');
    const langDir = path.join(hljsDir, 'src', 'languages');

    const utilsCode = fs.readFileSync(path.join(libDir, 'utils.js'), 'utf8');
    const utils = evaluate(utilsCode, exportedNames(utilsCode));

    const modesCode = stripModuleSyntax(fs.readFileSync(path.join(libDir, 'modes.js'), 'utf8'));
    const modeNames = exportedNames(fs.readFileSync(path.join(libDir, 'modes.js'), 'utf8'));
    const modesModule = { exports: {} };
    // eslint-disable-next-line no-new-func
    new Function('module', 'inherit', modesCode + '\nObject.assign(module.exports, {' + modeNames.join(',') + '});')(
        modesModule,
        utils.inherit
    );
    const MODES = modesModule.exports;

    const languages = Object.create(null);
    const aliases = Object.create(null);
    const definitions = Object.create(null);

    const hljs = {
        inherit: utils.inherit,
    };

    function loadDefinition(name) {
        if (!definitions[name]) {
            const file = path.join(langDir, name + '.js');
            if (!fs.existsSync(file)) {
                return null;
            }
            definitions[name] = evaluate(fs.readFileSync(file, 'utf8'));
        }

        return definitions[name];
    }

    function registerLanguage(name) {
        if (languages[name]) {
            return languages[name];
        }
        const fn = loadDefinition(name);
        if (!fn) {
            return null;
        }
        const lang = fn(hljs);
        lang.rawDefinition = fn.bind(null, hljs);
        languages[name] = lang;
        (lang.aliases || []).forEach(function (alias) {
            aliases[alias] = name;
        });

        return lang;
    }

    hljs.getLanguage = function (name) {
        name = (name || '').toLowerCase();

        return languages[name] || languages[aliases[name]] || registerLanguage(name);
    };

    hljs.requireLanguage = function (name) {
        const lang = hljs.getLanguage(name);
        if (lang) {
            return lang;
        }
        throw new Error("The '" + name + "' language is required, but not loaded.");
    };

    for (const key in MODES) {
        if (typeof MODES[key] === 'object') {
            deepFreeze(MODES[key]);
        }
    }
    Object.assign(hljs, MODES);

    return {
        hljs: hljs,
        /** Evaluates a language definition from scratch, as `rawDefinition()` would. */
        define: function (name) {
            return loadDefinition(name)(hljs);
        },
        languageNames: function () {
            return fs.readdirSync(langDir)
                .filter(function (f) { return f.endsWith('.js'); })
                .map(function (f) { return f.slice(0, -3); })
                .sort();
        },
    };
}

module.exports = { createHljs: createHljs };
