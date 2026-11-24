'use strict';

/**
 * Transpiles highlight.js (v10) language definitions into PHP classes.
 *
 * A highlight.js language is a function `function(hljs) { ...; return {...}; }`
 * written in a small subset of JavaScript. This tool translates that subset
 * into an equivalent PHP class implementing `Highlight\LanguageDefinition`.
 *
 * Mapping rules:
 *
 * - JS object literals that may be shared or mutated (bound to a variable,
 *   element of a variable-bound array, returned) become `new Mode([...])`;
 *   object literals nested inside other literals become plain PHP arrays which
 *   are turned into Mode objects when the language is loaded.
 * - Object literals only used as keyword maps stay plain PHP arrays.
 * - JS arrays are values in PHP. Arrays that are mutated after being stored in
 *   a mode are stored by reference (`&$VAR`) to keep the JS semantics.
 * - Regular expressions are emitted as strings holding the JS regex source;
 *   the PHP engine translates JS regex syntax to PCRE when compiling.
 *
 * Anything outside the supported subset throws, so that new constructs in
 * future highlight.js versions are noticed instead of silently mistranslated.
 */

const acorn = require('acorn');

const INDENT = '    ';
const MAX_LINE = 120;

const PHP_RESERVED = new Set(('abstract and array as break callable case catch class clone const continue declare default ' +
    'do echo else elseif empty enddeclare endfor endforeach endif endswitch endwhile enum eval exit extends final finally ' +
    'fn for foreach function global goto if implements include include_once instanceof insteadof interface isset list ' +
    'match namespace new or print private protected public readonly require require_once return static switch throw ' +
    'trait try unset use var while xor yield int float bool string true false null void iterable object mixed never ' +
    'parent self').split(' '));

const MODE_KEYS = new Set(['contains', 'variants', 'starts']);

class TranspileError extends Error {
    constructor(message, node) {
        super(message + (node && node.loc ? ' (line ' + node.loc.start.line + ')' : ''));
    }
}

/**
 * @param {string} languageId
 * @returns {string}
 */
function className(languageId) {
    const special = { '1c': 'OneC' };
    if (special[languageId]) {
        return special[languageId];
    }
    let name = languageId.split(/[^a-zA-Z0-9]+/).filter(Boolean)
        .map(function (p) { return p[0].toUpperCase() + p.slice(1); }).join('');
    if (/^[0-9]/.test(name) || PHP_RESERVED.has(name.toLowerCase())) {
        name = 'Lang' + name;
    }

    return name;
}

// ---------------------------------------------------------------------------
// PHP literal helpers

function phpString(value) {
    // eslint-disable-next-line no-control-regex
    if (/[\x00-\x1f\x7f]/.test(value)) {
        let out = '"';
        for (const ch of value) {
            const code = ch.codePointAt(0);
            if (ch === '\\') out += '\\\\';
            else if (ch === '"') out += '\\"';
            else if (ch === '$') out += '\\$';
            else if (ch === '\n') out += '\\n';
            else if (ch === '\r') out += '\\r';
            else if (ch === '\t') out += '\\t';
            else if (code < 0x20 || code === 0x7f) out += '\\x' + code.toString(16).padStart(2, '0');
            else out += ch;
        }

        return out + '"';
    }

    // Single quoted: only `\\` and `\'` are escapes. Keep regexes readable by
    // only doubling a backslash when PHP would otherwise misread it.
    let out = "'";
    for (let i = 0; i < value.length; i++) {
        const ch = value[i];
        if (ch === "'") {
            out += "\\'";
        } else if (ch === '\\') {
            const nextCh = value[i + 1];
            out += (nextCh === undefined || nextCh === '\\' || nextCh === "'") ? '\\\\' : '\\';
        } else {
            out += ch;
        }
    }

    return out + "'";
}

function pregQuote(value) {
    return value.replace(/[.\\+*?[^\]$(){}=!<>|:\-#/]/g, '\\$&');
}

// ---------------------------------------------------------------------------
// Comments

class Comments {
    constructor(comments, source) {
        this.comments = comments;
        this.source = source;
        this.used = new Set();
    }

    format(c) {
        if (c.type === 'Line') {
            // `?>` would end PHP mode inside a line comment
            if (c.value.includes('?>')) {
                return '/*' + c.value.replace(/\s+$/, '') + ' */';
            }

            return '//' + c.value.replace(/\s+$/, '');
        }

        return '/*' + c.value + '*/';
    }

    /** Comments in [from, to) not yet used. */
    take(from, to) {
        const out = [];
        for (const c of this.comments) {
            if (c.start >= from && c.end <= to && !this.used.has(c)) {
                this.used.add(c);
                out.push(c);
            }
        }

        return out;
    }

    lineOf(pos) {
        let line = 1;
        for (let i = 0; i < pos; i++) {
            if (this.source.charCodeAt(i) === 10) line++;
        }

        return line;
    }
}

// ---------------------------------------------------------------------------
// Scope / usage analysis

/**
 * Collects facts about the variables declared in a function body so that the
 * emitter can choose PHP representations.
 */
function analyse(fnBody, params) {
    const info = {
        vars: new Map(),        // name -> {init, kind, declIndex}
        mutated: new Set(),     // array variables mutated in place
        reassigned: new Set(),  // variables assigned after declaration
        modeUse: new Set(),     // object variables used where a mode is expected
        keywordUse: new Set(),  // object variables used as keyword maps
        keywordCount: new Map(), // number of modes using a keyword map variable
        functions: new Map(),   // name -> FunctionDeclaration / FunctionExpression
    };

    params.forEach(function (p) {
        info.vars.set(p, { init: null, kind: 'param', declIndex: -1 });
    });

    fnBody.body.forEach(function (stmt, index) {
        if (stmt.type === 'VariableDeclaration') {
            stmt.declarations.forEach(function (d) {
                info.vars.set(d.id.name, { init: d.init, kind: 'var', declIndex: index });
                if (d.init && d.init.type === 'FunctionExpression') {
                    info.functions.set(d.id.name, d.init);
                }
            });
        } else if (stmt.type === 'FunctionDeclaration') {
            info.vars.set(stmt.id.name, { init: stmt, kind: 'function', declIndex: index });
            info.functions.set(stmt.id.name, stmt);
        }
    });

    function rootName(node) {
        while (node.type === 'MemberExpression') {
            node = node.object;
        }

        return node.type === 'Identifier' ? node.name : null;
    }

    function markModeUse(node) {
        if (node && node.type === 'Identifier') {
            info.modeUse.add(node.name);
        }
        if (node && node.type === 'ArrayExpression') {
            node.elements.forEach(markModeUse);
        }
    }

    function visit(node, parent, key) {
        if (!node || typeof node.type !== 'string') {
            return;
        }

        if (node.type === 'Property') {
            const k = propKey(node);
            if (k === 'keywords' && node.value.type === 'Identifier') {
                info.keywordUse.add(node.value.name);
                info.keywordCount.set(node.value.name, (info.keywordCount.get(node.value.name) || 0) + 1);
            } else if (MODE_KEYS.has(k)) {
                markModeUse(node.value);
            }
        }

        if (node.type === 'ReturnStatement') {
            markModeUse(node.argument);
        }

        if (node.type === 'CallExpression') {
            const callee = node.callee;
            if (callee.type === 'MemberExpression') {
                const method = callee.property.name;
                if (['push', 'unshift', 'splice', 'reverse'].includes(method)) {
                    const name = rootName(callee.object);
                    if (callee.object.type === 'Identifier') {
                        info.mutated.add(name);
                    }
                    node.arguments.forEach(markModeUse);
                }
                if (callee.object.type === 'Identifier' && callee.object.name === 'hljs' && method === 'inherit') {
                    markModeUse(node.arguments[0]);
                }
                if (callee.object.type === 'Identifier' && callee.object.name === 'Object' && method === 'assign') {
                    markModeUse(node.arguments[0]);
                }
                if (method === 'concat') {
                    node.arguments.forEach(markModeUse);
                    markModeUse(callee.object);
                }
            }
        }

        if (node.type === 'AssignmentExpression') {
            if (node.left.type === 'Identifier') {
                info.reassigned.add(node.left.name);
            } else if (node.left.type === 'MemberExpression') {
                const name = rootName(node.left);
                if (name) {
                    info.mutated.add(name);
                    if (node.left.object.type === 'MemberExpression' || node.left.computed) {
                        // contains[0].contains = ...  -> array element is a mode
                        info.modeUse.add(name);
                    }
                    if (!node.left.computed && MODE_KEYS.has(node.left.property.name)) {
                        markModeUse(node.right);
                        if (node.left.object.type === 'Identifier') {
                            info.modeUse.add(node.left.object.name);
                        }
                    }
                }
            }
        }

        for (const k of Object.keys(node)) {
            if (k === 'loc' || k === 'start' || k === 'end') continue;
            const child = node[k];
            if (Array.isArray(child)) {
                child.forEach(function (c) { visit(c, node, k); });
            } else if (child && typeof child.type === 'string') {
                visit(child, node, k);
            }
        }
    }

    visit(fnBody, null, null);


    return info;
}

function propKey(prop) {
    if (prop.computed) {
        throw new TranspileError('Computed property keys are not supported', prop);
    }
    if (prop.key.type === 'Identifier') return prop.key.name;
    if (prop.key.type === 'Literal') return String(prop.key.value);
    throw new TranspileError('Unsupported property key ' + prop.key.type, prop);
}

/** Free identifiers referenced inside a function (excluding its params and locals). */
function freeVariables(fn) {
    const bound = new Set(fn.params.map(function (p) { return p.name; }));
    const free = new Set();

    function declare(node) {
        if (node.type === 'VariableDeclaration') {
            node.declarations.forEach(function (d) { bound.add(d.id.name); });
        }
    }
    (fn.body.body || []).forEach(declare);

    function visit(node, parent, key) {
        if (!node || typeof node.type !== 'string') return;
        if (node.type === 'Identifier') {
            const isProp = parent && parent.type === 'MemberExpression' && key === 'property' && !parent.computed;
            const isKey = parent && parent.type === 'Property' && key === 'key';
            if (!isProp && !isKey && !bound.has(node.name)) free.add(node.name);
            return;
        }
        if ((node.type === 'FunctionExpression' || node.type === 'ArrowFunctionExpression') && node !== fn) {
            freeVariables(node).forEach(function (v) {
                if (!bound.has(v)) free.add(v);
            });
            return;
        }
        for (const k of Object.keys(node)) {
            if (k === 'loc') continue;
            const child = node[k];
            if (Array.isArray(child)) child.forEach(function (c) { visit(c, node, k); });
            else if (child && typeof child.type === 'string') visit(child, node, k);
        }
    }
    visit(fn.body, null, null);

    return free;
}

// ---------------------------------------------------------------------------
// Emitter

class Emitter {
    constructor(source, comments, languageId) {
        this.source = source;
        this.comments = comments;
        this.languageId = languageId;
        this.scopes = [];
    }

    get scope() {
        return this.scopes[this.scopes.length - 1];
    }

    lookup(name) {
        for (let i = this.scopes.length - 1; i >= 0; i--) {
            if (this.scopes[i].info.vars.has(name)) {
                return { scope: this.scopes[i], decl: this.scopes[i].info.vars.get(name) };
            }
        }

        return null;
    }

    // ---- typing ---------------------------------------------------------

    /** @returns {'string'|'number'|'boolean'|'array'|'mode'|'map'|'lang'|'hljs'|'function'|'null'|'unknown'} */
    typeOf(node) {
        switch (node.type) {
            case 'Literal':
                if (node.regex) return 'string';
                if (node.value === null) return 'null';

                return typeof node.value;
            case 'TemplateLiteral':
                return 'string';
            case 'ArrayExpression':
                return 'array';
            case 'ObjectExpression':
                return 'mode';
            case 'BinaryExpression':
                if (node.operator === '+') {
                    const l = this.typeOf(node.left);
                    const r = this.typeOf(node.right);

                    return (l === 'string' || r === 'string') ? 'string' : (l === 'number' && r === 'number' ? 'number' : 'unknown');
                }

                return 'unknown';
            case 'LogicalExpression':
                return this.typeOf(node.left);
            case 'Identifier': {
                if (node.name === 'hljs') return 'hljs';
                if (node.name === 'undefined') return 'null';
                const found = this.lookup(node.name);
                if (!found) return 'unknown';
                const d = found.decl;
                if (d.kind === 'function') return 'function';
                if (d.kind === 'param') return d.type || 'unknown';
                if (!d.init) return found.scope.info.modeUse.has(node.name) ? 'mode' : 'unknown';
                if (d.init.type === 'ObjectExpression') return this.objectVarIsMode(node.name, found.scope) ? 'mode' : 'map';
                if (d.init.type === 'FunctionExpression') return 'function';

                return this.typeOf(d.init);
            }
            case 'MemberExpression': {
                const objType = this.typeOf(node.object);
                if (objType === 'hljs') {
                    const name = node.property.name;
                    if (/_RE$/.test(name)) return 'string';
                    if (name === 'COMMENT' || name === 'inherit') return 'function';

                    return 'mode';
                }
                if (node.computed) {
                    if (objType === 'array') {
                        const found = node.object.type === 'Identifier' && this.lookup(node.object.name);
                        if (found && found.scope.info.modeUse.has(node.object.name)) return 'mode';

                        return 'unknown';
                    }
                    if (objType === 'string') return 'string';

                    return 'unknown';
                }
                const prop = node.property.name;
                if (prop === 'length') return 'number';
                if (objType === 'mode') {
                    if (prop === 'keywords') return 'map';
                    if (MODE_KEYS.has(prop) && prop !== 'starts') return 'array';
                    if (prop === 'starts') return 'mode';
                    if (['begin', 'end', 'className', 'illegal', 'lexemes', 'name', 'beginKeywords'].includes(prop)) return 'string';

                    return 'unknown';
                }
                if (objType === 'map') return 'string';

                return 'unknown';
            }
            case 'CallExpression': {
                const callee = node.callee;
                if (callee.type === 'MemberExpression') {
                    const method = callee.property.name;
                    const objType = this.typeOf(callee.object);
                    if (objType === 'hljs') {
                        if (method === 'COMMENT' || method === 'inherit') return 'mode';
                        if (method === 'getLanguage' || method === 'requireLanguage') return 'lang';
                    }
                    if (method === 'rawDefinition') return 'mode';
                    if (['concat', 'split', 'map', 'slice', 'reverse'].includes(method)) {
                        return ((method === 'slice' || method === 'concat') && objType === 'string') ? 'string' : 'array';
                    }
                    if (['join', 'trim', 'replace', 'toString', 'toLowerCase', 'toUpperCase'].includes(method)) return 'string';
                }
                if (callee.type === 'Identifier') {
                    const found = this.lookup(callee.name);
                    if (found && found.decl.kind !== 'param') {
                        const fn = found.scope.info.functions.get(callee.name);
                        if (fn) return this.returnType(fn);
                    }
                }

                return 'unknown';
            }
            default:
                return 'unknown';
        }
    }

    returnType(fn) {
        if (fn.__returnType) return fn.__returnType;
        fn.__returnType = 'unknown';
        const ret = fn.body.body.find(function (s) { return s.type === 'ReturnStatement'; });
        if (ret && ret.argument) {
            this.withFunctionScope(fn, () => {
                fn.__returnType = this.typeOf(ret.argument);
                if (ret.argument.type === 'Identifier') {
                    const d = this.scope.info.vars.get(ret.argument.name);
                    if (d && d.init && d.init.type === 'ObjectExpression') fn.__returnType = 'mode';
                }
            });
        }

        return fn.__returnType;
    }

    objectVarIsMode(name, scope) {
        const info = scope.info;
        if (info.modeUse.has(name)) return true;
        if (info.keywordUse.has(name)) return false;
        const decl = info.vars.get(name);
        // Objects that look like modes are modes even when only read from.
        if (decl && decl.init && decl.init.type === 'ObjectExpression') {
            const keys = decl.init.properties.map(propKey);
            const modeish = ['begin', 'end', 'className', 'contains', 'variants', 'beginKeywords', 'keywords', 'starts', 'relevance', 'illegal'];
            if (keys.length === 0 || keys.some(function (k) { return modeish.includes(k); })) return true;
        }

        return false;
    }

    // ---- statements -----------------------------------------------------

    withFunctionScope(fn, cb) {
        const params = fn.params.map(function (p) {
            if (p.type !== 'Identifier') throw new TranspileError('Unsupported parameter', p);

            return p.name;
        });
        const body = fn.body.type === 'BlockStatement' ? fn.body : { type: 'BlockStatement', body: [] };
        this.scopes.push({ info: analyse(body, params), fn: fn });
        try {
            return cb();
        } finally {
            this.scopes.pop();
        }
    }

    emitBlock(statements, indent, endPos) {
        const lines = [];
        let prevEnd = statements.length ? statements[0].start : endPos;
        let prevLine = null;

        // function declarations are hoisted in JS
        const hoisted = statements.filter(function (s) { return s.type === 'FunctionDeclaration'; });
        const ordered = hoisted.concat(statements.filter(function (s) { return s.type !== 'FunctionDeclaration'; }));

        ordered.forEach((stmt) => {
            this.emitLeadingComments(lines, stmt, indent, prevLine);
            const code = this.emitStatement(stmt, indent);
            if (code === null) return;
            // keep blank lines between statements that were separated in the source
            if (lines.length && prevLine !== null && this.comments.lineOf(stmt.start) - prevLine > 1 &&
                lines[lines.length - 1] !== '') {
                lines.splice(lines.length - this.lastLeading, 0, '');
            }
            lines.push(code);
            this.emitTrailingComment(lines, stmt);
            prevEnd = stmt.end;
            prevLine = this.comments.lineOf(stmt.end);
        });

        return lines;
    }

    emitLeadingComments(lines, node, indent, prevLine) {
        const pad = INDENT.repeat(indent);
        const cs = this.comments.take(0, node.start).filter((c) => {
            // never pull a comment from before the enclosing statement's position
            return true;
        });
        this.lastLeading = 0;
        cs.forEach((c) => {
            if (prevLine !== null && this.comments.lineOf(c.start) === prevLine && lines.length) {
                lines[lines.length - 1] += ' ' + this.comments.format(c);
            } else {
                this.formatCommentLines(c, pad).forEach(function (l) { lines.push(l); });
                this.lastLeading++;
            }
        });
    }

    formatCommentLines(c, pad) {
        if (c.type === 'Line') return [pad + this.comments.format(c)];
        const body = c.value.split('\n');

        return ['/*' + body[0]].concat(body.slice(1).map(function (l) { return l.replace(/^\s*/, ' '); }))
            .join('\n').split('\n').map(function (l, i) { return (i === 0 ? pad : pad) + l.trimEnd(); })
            .map(function (l, i, all) { return i === all.length - 1 && !l.trim().endsWith('*/') ? l + ' */' : l; })
            .join('\n').replace(/\*\/ \*\/$/, '*/').split('\n');
    }

    emitTrailingComment(lines, node) {
        // comments on the same line right after the node
        const endLine = this.comments.lineOf(node.end);
        const cs = this.comments.comments.filter((c) => !this.comments.used.has(c) && c.start >= node.end &&
            this.comments.lineOf(c.start) === endLine && !/\S/.test(this.source.slice(node.end, c.start).replace(/[;,]/g, '')));
        cs.forEach((c) => {
            this.comments.used.add(c);
            lines[lines.length - 1] += ' ' + this.comments.format(c);
        });
    }

    emitStatement(stmt, indent) {
        const pad = INDENT.repeat(indent);
        switch (stmt.type) {
            case 'VariableDeclaration':
                return stmt.declarations.map((d) => {
                    if (d.init && d.init.type === 'FunctionExpression') {
                        return pad + '$' + d.id.name + ' = ' + this.emitClosure(d.init, indent, d.id.name) + ';';
                    }
                    const value = d.init ? this.emitValue(d.init, indent, { root: true, varName: d.id.name }) : 'null';
                    const byRef = d.init && d.init.type === 'MemberExpression' && this.scope.info.mutated.has(d.id.name);

                    return pad + '$' + d.id.name + (byRef ? ' = &' : ' = ') + value + ';';
                }).join('\n');
            case 'FunctionDeclaration':
                return pad + '$' + stmt.id.name + ' = ' + this.emitClosure(stmt, indent, stmt.id.name) + ';';
            case 'ReturnStatement':
                return pad + 'return' + (stmt.argument ? ' ' + this.emitValue(stmt.argument, indent, { root: true }) : '') + ';';
            case 'ExpressionStatement':
                return this.emitExpressionStatement(stmt.expression, indent);
            case 'EmptyStatement':
                return null;
            default:
                throw new TranspileError('Unsupported statement ' + stmt.type, stmt);
        }
    }

    emitExpressionStatement(expr, indent) {
        const pad = INDENT.repeat(indent);
        if (expr.type === 'AssignmentExpression') {
            const left = this.emitLValue(expr.left, indent);
            const leftType = this.typeOf(expr.left);
            if (expr.operator === '=') {
                return pad + left + ' = ' + this.emitValue(expr.right, indent, { root: true }) + ';';
            }
            if (expr.operator === '+=') {
                const isString = leftType === 'string' || this.typeOf(expr.right) === 'string';

                return pad + left + (isString ? ' .= ' : ' += ') + this.emitValue(expr.right, indent, { root: true }) + ';';
            }
            throw new TranspileError('Unsupported assignment operator ' + expr.operator, expr);
        }
        if (expr.type === 'CallExpression' && expr.callee.type === 'MemberExpression') {
            const method = expr.callee.property.name;
            const obj = expr.callee.object;
            if (method === 'push') {
                const target = this.emitLValue(obj, indent);

                return expr.arguments.map((a) => pad + target + '[] = ' + this.emitValue(a, indent, { root: true }) + ';').join('\n');
            }
            if (method === 'unshift') {
                return pad + 'array_unshift(' + this.emitLValue(obj, indent) + ', ' +
                    expr.arguments.map((a) => this.emitValue(a, indent, { root: true })).join(', ') + ');';
            }
            if (method === 'forEach') {
                const fn = expr.arguments[0];
                if (fn.type !== 'FunctionExpression' || fn.params.length !== 1) {
                    throw new TranspileError('Unsupported forEach callback', fn);
                }
                const param = fn.params[0].name;

                return this.withFunctionScope(fn, () => {
                    const body = this.emitBlock(fn.body.body, indent + 1, fn.body.end);

                    return pad + 'foreach (' + this.emitValue(obj, indent) + ' as $' + param + ') {\n' + body.join('\n') + '\n' + pad + '}';
                });
            }
            if (obj.type === 'Identifier' && obj.name === 'Object' && method === 'assign') {
                const target = this.emitLValue(expr.arguments[0], indent);
                const lines = [];
                expr.arguments.slice(1).forEach((src) => {
                    if (src.type !== 'ObjectExpression') throw new TranspileError('Object.assign needs object literals', src);
                    src.properties.forEach((p) => {
                        lines.push(pad + target + '->' + propKey(p) + ' = ' + this.emitValue(p.value, indent, { root: false }) + ';');
                    });
                });

                return lines.join('\n');
            }
        }
        if (expr.type === 'Literal') {
            // a no-op expression in the source, kept for fidelity
            return pad + this.emitValue(expr, indent, {}) + ';';
        }
        throw new TranspileError('Unsupported expression statement ' + expr.type, expr);
    }

    emitLValue(node, indent) {
        if (node.type === 'Identifier') return '$' + node.name;
        if (node.type === 'MemberExpression') return this.emitMember(node, indent);
        throw new TranspileError('Unsupported assignment target', node);
    }

    // ---- functions ------------------------------------------------------

    emitClosure(fn, indent, name) {
        const pad = INDENT.repeat(indent);
        // JS functions may be called with fewer arguments than declared
        const params = fn.params.map(function (p) { return '$' + p.name + (name ? ' = null' : ''); }).join(', ');
        const outerScope = this.scope;
        const free = Array.from(freeVariables(fn)).filter((v) => this.lookup(v) || v === 'hljs');
        const declIndex = name && outerScope.info.vars.has(name) ? outerScope.info.vars.get(name).declIndex : -1;

        const uses = free.filter(function (v) { return v !== name; }).map(function (v) {
            if (v === 'hljs') return '$hljs';
            const d = outerScope.info.vars.get(v);
            const later = d && (d.declIndex > declIndex || outerScope.info.reassigned.has(v) || outerScope.info.mutated.has(v));

            return (later ? '&$' : '$') + v;
        });
        if (name && free.includes(name)) uses.push('&$' + name); // recursion

        // The generated code must run on PHP 7.1, so no arrow functions (`fn () =>`, PHP 7.4)
        const head = 'function (' + params + ')' + (uses.length ? ' use (' + uses.join(', ') + ')' : '');

        return this.withFunctionScope(fn, () => {
            const body = fn.body.body;
            if (body.length === 1 && body[0].type === 'ReturnStatement' && body[0].argument) {
                // short callbacks stay on one line, like `function ($t) { return 'end' . $t; }`
                const ret = this.emitValue(body[0].argument, indent, { root: true });
                const oneLine = head + ' { return ' + ret + '; }';
                if (!ret.includes('\n') && oneLine.length + pad.length < MAX_LINE) {
                    return oneLine;
                }
            }
            const lines = this.emitBlock(body, indent + 1, fn.body.end);

            return head + ' {\n' + lines.join('\n') + '\n' + pad + '}';
        });
    }

    // ---- expressions ----------------------------------------------------

    /**
     * @param ctx.root   true when the value is not nested inside an object/array literal
     * @param ctx.keywords true when inside a keyword map
     */
    emitValue(node, indent, ctx) {
        ctx = ctx || {};
        switch (node.type) {
            case 'Literal':
                if (node.regex) {
                    if (node.regex.flags && node.regex.flags !== 'g') {
                        throw new TranspileError('Unsupported regex flags ' + node.regex.flags, node);
                    }

                    return phpString(node.regex.pattern);
                }
                if (typeof node.value === 'string') return phpString(node.value);
                if (node.value === null) return 'null';

                return String(node.value);
            case 'Identifier':
                if (node.name === 'undefined') return 'null';
                if (ctx.inLiteral && this.isRefArray(node.name)) return '&$' + node.name;
                if (ctx.inLiteral && ctx.keywordValue && this.isSharedKeywords(node.name)) return '&$' + node.name;

                return '$' + node.name;
            case 'ObjectExpression':
                return this.emitObject(node, indent, ctx);
            case 'ArrayExpression':
                return this.emitArray(node, indent, ctx);
            case 'BinaryExpression':
                return this.emitBinary(node, indent);
            case 'LogicalExpression':
                if (node.operator === '||') {
                    return this.emitValue(node.left, indent, {}) + ' ?: ' + this.emitValue(node.right, indent, {});
                }
                throw new TranspileError('Unsupported logical operator ' + node.operator, node);
            case 'MemberExpression':
                return this.emitMember(node, indent);
            case 'CallExpression':
                return this.emitCall(node, indent, ctx);
            case 'FunctionExpression':
                return this.emitClosure(node, indent, null);
            default:
                throw new TranspileError('Unsupported expression ' + node.type, node);
        }
    }

    /**
     * A keyword map stored in several modes is one shared object in JS, which
     * other languages may modify (e.g. arduino extends the cpp keywords).
     */
    isSharedKeywords(name) {
        const found = this.lookup(name);
        if (!found || found.decl.kind !== 'var' || this.typeOf({ type: 'Identifier', name: name }) !== 'map') return false;

        return (found.scope.info.keywordCount.get(name) || 0) > 1;
    }

    isRefArray(name) {
        const found = this.lookup(name);
        if (!found || found.decl.kind !== 'var') return false;
        const info = found.scope.info;

        return info.mutated.has(name) && this.typeOf({ type: 'Identifier', name: name }) === 'array';
    }

    sameLine(node) {
        return node.loc.start.line === node.loc.end.line;
    }

    /** Joins items either on one line or one per line, preserving comments. */
    emitList(node, items, open, close, indent) {
        const pad = INDENT.repeat(indent);
        const inner = INDENT.repeat(indent + 1);
        const rendered = items.map((item) => ({ item: item, code: item.render(indent + 1) }));

        const hasComments = this.comments.comments.some((c) => !this.comments.used.has(c) && c.start > node.start && c.end < node.end);
        const oneLine = open + rendered.map(function (r) { return r.code; }).join(', ') + close;
        if (!hasComments && (this.sameLine(node) || items.length === 0) && !oneLine.includes('\n') && oneLine.length + pad.length < MAX_LINE) {
            return oneLine;
        }

        const lines = [open];
        let prevLine = null;
        rendered.forEach((r) => {
            const start = r.item.node.start;
            this.comments.take(node.start, start).forEach((c) => {
                if (prevLine !== null && this.comments.lineOf(c.start) === prevLine) {
                    lines[lines.length - 1] += ' ' + this.comments.format(c);
                } else {
                    this.formatCommentLines(c, inner).forEach(function (l) { lines.push(l); });
                }
            });
            lines.push(inner + r.code + ',');
            prevLine = this.comments.lineOf(r.item.node.end);
        });
        this.comments.take(node.start, node.end).forEach((c) => {
            if (prevLine !== null && this.comments.lineOf(c.start) === prevLine) {
                lines[lines.length - 1] += ' ' + this.comments.format(c);
            } else {
                this.formatCommentLines(c, inner).forEach(function (l) { lines.push(l); });
            }
        });
        lines.push(pad + close);

        return lines.join('\n');
    }

    emitObject(node, indent, ctx) {
        const isKeywords = ctx.keywords || (ctx.varName && !this.objectVarIsMode(ctx.varName, this.scope));
        const items = node.properties.map((p) => {
            if (p.type !== 'Property' || p.kind !== 'init' || p.method || p.shorthand) {
                throw new TranspileError('Unsupported object property', p);
            }
            const key = propKey(p);

            return {
                node: p,
                render: (ind) => phpString(key) + ' => ' + this.emitValue(p.value, ind, {
                    inLiteral: true,
                    keywordValue: key === 'keywords',
                    keywords: isKeywords || key === 'keywords',
                    root: false,
                }),
            };
        });
        const arr = this.emitList(node, items, '[', ']', indent);
        if (ctx.root && !isKeywords) {
            return 'new Mode(' + (items.length ? arr : '') + ')';
        }

        return arr;
    }

    emitArray(node, indent, ctx) {
        const items = node.elements.map((el) => {
            if (el === null) throw new TranspileError('Array holes are not supported', node);
            if (el.type === 'SpreadElement') throw new TranspileError('Spread is not supported', el);

            return {
                node: el,
                // elements of an array that is not nested in a literal keep their identity
                render: (ind) => this.emitValue(el, ind, { inLiteral: true, root: !!ctx.root, keywords: ctx.keywords }),
            };
        });

        return this.emitList(node, items, '[', ']', indent);
    }

    emitBinary(node, indent) {
        if (node.operator !== '+') {
            throw new TranspileError('Unsupported operator ' + node.operator, node);
        }
        // flatten string concatenation chains, keep line breaks of the source
        const operands = [];
        const flatten = (n) => {
            if (n.type === 'BinaryExpression' && n.operator === '+' && this.typeOf(n) === 'string') {
                flatten(n.left);
                flatten(n.right);
            } else {
                operands.push(n);
            }
        };
        if (this.typeOf(node) !== 'string') {
            if (this.typeOf(node) === 'number') {
                return this.emitValue(node.left, indent, {}) + ' + ' + this.emitValue(node.right, indent, {});
            }
            throw new TranspileError('Cannot determine type of + expression', node);
        }
        flatten(node);
        const cont = INDENT.repeat(indent + 1);
        let out = '';
        operands.forEach((op, i) => {
            const code = this.emitValue(op, indent + 1, {});
            if (i === 0) {
                out = code;
            } else if (op.loc.start.line !== operands[i - 1].loc.end.line) {
                out += '\n' + cont + '. ' + code;
            } else {
                out += ' . ' + code;
            }
        });
        // trailing comments inside the chain
        return out;
    }

    emitMember(node, indent) {
        const obj = node.object;
        const objType = this.typeOf(obj);
        if (objType === 'hljs') {
            return '$hljs->' + node.property.name;
        }
        const target = obj.type === 'Identifier' ? '$' + obj.name : this.emitValue(obj, indent, {});
        if (node.computed) {
            return target + '[' + this.emitValue(node.property, indent, {}) + ']';
        }
        const prop = node.property.name;
        if (prop === 'length') {
            return (objType === 'string' ? 'strlen(' : 'count(') + target + ')';
        }
        if (objType === 'map') {
            return target + '[' + phpString(prop) + ']';
        }
        if (objType === 'mode' || objType === 'lang' || objType === 'unknown') {
            return target + '->' + prop;
        }
        throw new TranspileError('Unsupported member access on ' + objType, node);
    }

    emitArgs(args, indent) {
        return args.map((a) => this.emitValue(a, indent, { root: false })).join(', ');
    }

    emitCall(node, indent, ctx) {
        const callee = node.callee;
        const args = node.arguments;

        if (callee.type === 'Identifier') {
            const found = this.lookup(callee.name);
            if (!found) throw new TranspileError('Unknown function ' + callee.name, node);

            return '$' + callee.name + '(' + this.emitArgs(args, indent) + ')';
        }
        if (callee.type !== 'MemberExpression' || callee.computed) {
            throw new TranspileError('Unsupported call', node);
        }

        const method = callee.property.name;
        const obj = callee.object;
        const objType = this.typeOf(obj);

        if (objType === 'hljs') {
            if (method === 'inherit' || method === 'COMMENT') {
                // override objects passed to inherit/COMMENT are merged, their identity does not matter
                return '$hljs->' + method + '(' + args.map((a) => this.emitValue(a, indent, { root: false })).join(', ') + ')';
            }
            if (method === 'getLanguage' || method === 'requireLanguage') {
                return '$hljs->' + method + '(' + this.emitArgs(args, indent) + ')';
            }
            throw new TranspileError('Unsupported hljs method ' + method, node);
        }

        const self = () => this.emitValue(obj, indent, {});

        switch (method) {
            case 'rawDefinition':
                return self() + '->rawDefinition()';
            case 'concat': {
                if (objType === 'string') {
                    return [self()].concat(args.map((a) => this.emitValue(a, indent, {}))).join(' . ');
                }
                const parts = [this.emitValue(obj, indent, { root: ctx.root })].concat(args.map((a) => {
                    const t = this.typeOf(a);
                    if (t === 'array') return this.emitValue(a, indent, { root: ctx.root });
                    if (t === 'mode' || t === 'string') return '[' + this.emitValue(a, indent, { root: ctx.root }) + ']';
                    throw new TranspileError('Cannot determine type of concat argument', a);
                }));
                if (objType !== 'array') throw new TranspileError('concat on non-array', node);

                return 'array_merge(' + parts.join(', ') + ')';
            }
            case 'join':
                return 'implode(' + this.emitArgs(args, indent) + ', ' + self() + ')';
            case 'split': {
                const sep = args[0];
                if (sep.type === 'Literal' && sep.value === '') return 'str_split(' + self() + ')';
                if (sep.type === 'Literal' && typeof sep.value === 'string') return 'explode(' + phpString(sep.value) + ', ' + self() + ')';
                throw new TranspileError('Unsupported split separator', sep);
            }
            case 'trim':
                return 'trim(' + self() + ')';
            case 'toString':
                return self();
            case 'toLowerCase':
                return 'strtolower(' + self() + ')';
            case 'toUpperCase':
                return 'strtoupper(' + self() + ')';
            case 'reverse':
                return 'array_reverse(' + self() + ')';
            case 'slice': {
                const a = args.map((x) => this.emitValue(x, indent, {}));
                if (objType === 'string') return 'substr(' + self() + (a.length ? ', ' + a[0] : ', 0') + ')';
                if (args.length > 1) throw new TranspileError('slice with end is not supported', node);

                return 'array_slice(' + self() + ', ' + (a[0] || '0') + ')';
            }
            case 'map': {
                const fn = args[0];
                if (fn.type !== 'FunctionExpression') throw new TranspileError('map needs a function expression', fn);

                return 'array_map(' + this.emitClosure(fn, indent, null) + ', ' + self() + ')';
            }
            case 'replace': {
                const pattern = args[0];
                const replacement = this.emitValue(args[1], indent, {});
                if (pattern.type === 'Literal' && pattern.regex) {
                    const re = phpString('/' + pattern.regex.pattern.replace(/(^|[^\\])\//g, '$1\\/') + '/');
                    const limit = pattern.regex.flags.includes('g') ? '' : ', 1';

                    return 'preg_replace(' + re + ', ' + replacement + ', ' + self() + limit + ')';
                }
                if (pattern.type === 'Literal' && typeof pattern.value === 'string') {
                    return 'preg_replace(' + phpString('/' + pregQuote(pattern.value) + '/') + ', ' + replacement + ', ' + self() + ', 1)';
                }
                throw new TranspileError('Unsupported replace pattern', pattern);
            }
            default:
                throw new TranspileError('Unsupported method ' + method, node);
        }
    }
}

// ---------------------------------------------------------------------------

function headerDocBlock(comment) {
    const lines = comment.value.replace(/^\*?\s*\n/, '').replace(/\s+$/, '').split('\n');

    return ['/**'].concat(lines.map(function (l) {
        const t = l.replace(/^\s*\*?\s?/, '').replace(/\*\//g, '*\\/').trimEnd();

        return t ? ' * ' + t : ' *';
    })).concat([' */']).join('\n');
}

/**
 * @param {string} source       JS source of the language definition
 * @param {string} languageId   e.g. "c-like"
 * @param {object} options      {hljsVersion}
 * @returns {string} PHP source
 */
function transpile(source, languageId, options) {
    options = options || {};
    const comments = [];
    const ast = acorn.parse(source, {
        ecmaVersion: 2020,
        sourceType: 'module',
        locations: true,
        onComment: comments,
    });

    const exported = ast.body.find(function (n) { return n.type === 'ExportDefaultDeclaration'; });
    if (!exported || exported.declaration.type !== 'FunctionDeclaration') {
        throw new TranspileError('Expected `export default function(hljs)`', ast);
    }
    const fn = exported.declaration;
    if (fn.params.length !== 1) {
        throw new TranspileError('Expected a single `hljs` parameter', fn);
    }
    if (fn.params[0].name !== 'hljs') {
        throw new TranspileError('Expected the parameter to be called `hljs`', fn);
    }

    const cm = new Comments(comments, source);
    const header = comments.find(function (c) { return c.type === 'Block' && c.end <= exported.start; });
    if (header) cm.used.add(header);
    // comments before the export (other than the header) are dropped into the body
    comments.filter(function (c) { return c.end <= exported.start; }).forEach(function (c) { cm.used.add(c); });

    const emitter = new Emitter(source, cm, languageId);
    const body = emitter.withFunctionScope(fn, function () {
        return emitter.emitBlock(fn.body.body, 2, fn.body.end);
    });
    // comments after the last statement
    const rest = cm.take(fn.body.start, fn.body.end).map(function (c) { return INDENT.repeat(2) + cm.format(c); });

    const cls = className(languageId);
    const out = [
        '<?php',
        '',
        '/* This file is generated by tools/transpiler from highlight.js' +
            (options.hljsVersion ? ' ' + options.hljsVersion : '') + ' src/languages/' + languageId + '.js. */',
        '',
        'namespace Highlight\\Languages;',
        '',
        'use Highlight\\Hljs;',
        'use Highlight\\LanguageDefinition;',
        'use Highlight\\Mode;',
        '',
        header ? headerDocBlock(header) : null,
        'final class ' + cls + ' implements LanguageDefinition',
        '{',
        INDENT + 'public const ID = ' + phpString(languageId) + ';',
        '',
        INDENT + 'public function __invoke(Hljs $hljs): Mode',
        INDENT + '{',
        body.join('\n') + (rest.length ? '\n' + rest.join('\n') : ''),
        INDENT + '}',
        '}',
        '',
    ].filter(function (l) { return l !== null; });

    return out.join('\n');
}

module.exports = { transpile: transpile, className: className, TranspileError: TranspileError };
