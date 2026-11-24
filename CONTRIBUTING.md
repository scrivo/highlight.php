# Contributing

Welcome to your server-side syntax highlighting solution! If you're interested in contributing to the project, here are a few things to keep in mind!

## Language Definitions + Styles

We do not accept PRs with new or updated language definitions or stylesheets. These should be contributed to the `highlight.js` project instead. The process of maintaining styles and transforming language definitions is completely automated and left up to project maintainers to run whenever a new version of `highlight.js` is released.

## Updates from `highlight.js`

If you'd like to make a PR containing behavior changes made in the `highlight.js` project, please make sure you link to the appropriate `highlight.js` commits and change logs. Because this project is a direct port, we need do our best to keep the behavior as identical as possible.

PRs reflecting changes made in `highlight.js` should **only** be made after `highlight.js` has had a release tagged with those changes. This project will always push out updates _after_ `highlight.js` releases.

We make no guarantees that the latest master `highlight.php` will be compatible with the latest master version of `highlight.js`.

## Project structure

The project contains the following folders:

1. [Highlight](#highlight)
2. [styles](#styles)
3. [demo](#demo)
4. [test](#test)
5. [tools](#tools)

## Highlight

This folder contains the main source and the following files (classes):

### Highlight.php (Highlight)

This is the one that does the highlighting for you and the one you'll probably look for.

### Language.php (Language)

Auxiliary class used in the Highlight class. Instances of these classes represent the rather complex structures of regular expressions needed to scan through programming code in order to highlight them.

You don't need this class.

### Hljs.php (Hljs)

The `hljs` object that is passed to language definitions. It offers the common regular expressions and modes (`$hljs->C_LINE_COMMENT_MODE`, `$hljs->IDENT_RE`, ...) and the helpers `inherit()`, `COMMENT()`, `getLanguage()` and `requireLanguage()`, with the same names as in highlight.js.

### LanguageDefinition.php (LanguageDefinition) and Mode.php (Mode)

A language is defined by a class implementing `LanguageDefinition`: an invokable that receives an `Hljs` instance and returns the `Mode` tree of the language. This is the PHP counterpart of a highlight.js language function `function(hljs) { return {...}; }`. Regular expressions in language definitions use JavaScript syntax; `RegExUtils::jsToPcre()` translates them to PCRE when a language is compiled.

### JsonRef.php (JsonRef)

Auxiliary class to decode JSON files containing path-based references, as created by [dojox.json.ref](https://dojotoolkit.org/reference-guide/1.9/dojox/json/ref.html). Up to highlight.php 9.x the language definitions were JSON files in this format; it is kept to load such legacy language files.

### the Languages folder

This folder contains all language definitions: one PHP class for each language, plus `Registry.php` which maps language IDs to their classes and aliases. These files are not hand coded but generated from the original [highlight.js](http://www.highlightjs.org) language definitions by the transpiler in `tools/transpiler`.

## Styles

These are the the CSS files needed to actually color the code. Not much to say about: these are just copied from the [highlight.js](https://github.com/isagalaev/highlight.js/tree/master/src/styles) project.

## Demo

This folder contains two demo pages that can be accessed through your browser.

### demo.php

A test page showing all supported languages and styles.

### compare.php

Much like [demo.php](#demo-php) but this page focuses on the comparison between _highlight.php_ and _highlight.js_. Both should yield the same results.

## Test

This folder contains the unit tests for _highlight.php_. They require PHP 8.1 or newer and PHPUnit 10.5 or newer. To run them, install the development dependencies and run the test script from the project root:

```bash
composer install
composer test
```

Note that the following tests for _highlight.js_ were not rewritten for _highlight.php_:

### special explicitLanguage

Controlling language selection by setting an attribute to the containing `<div>` is irrelevant for _highlight.php_

### special customMarkup

In _highlight.js_, code may contain additional HTML markup like in the following PHP fragment: `$sum = <b>$a</b> + $b;`. Currently this is not supported by _highlight.php_ which can only highlight (unescaped) code. Also highlighting `<br>` (HTML break element) is not supported. _highlight.php_ does however support tab replacement (which defaults to 4 spaces).

### special noHighlight

There is no need to turn off highlighting through a class name on the code container.

### special buildClassName

_highlight.php_ does not modify class names of code containers.

## Tools

A collection of scripts that are used to extract data from the original [highlight.js](http://www.highlightjs.org) project.

The process of bringing in languages, styles and tests from highlight.js has been put into a single script. This script requires that you have cURL, PHP, and node.js installed on your machine. This is only necessary if you'd like to update languages or bring in new languages, it's **not** required for using this library in your application.

```bash
cd tools
bash process.sh
```

### The language transpiler

Since highlight.js 10, language definitions may contain code, so they can't be exported as data anymore. Instead, `tools/transpiler` translates the JavaScript of each language definition (`src/languages/*.js` in highlight.js) into an equivalent PHP class. It supports the subset of JavaScript that language definitions are written in, and fails loudly on anything else, so that new constructs are noticed when updating highlight.js.

```bash
cd tools/transpiler
npm install
node generate.js /path/to/highlight.js              # (re)generates src/Highlight/Languages
./verify.sh /path/to/highlight.js                   # verifies the generated classes
```

The transpiler and its verification may require a recent PHP version, but the generated classes must stay compatible with PHP 7.1, like the rest of highlight.php 10.x.

`verify.sh` evaluates every language definition in both JavaScript and PHP and compares the resulting mode trees, including which modes are shared and which arrays are shared. Every language must match before the generated classes are committed.

## Why don't we generally use `mb_*` functions?

PHP offers `mb_` prefixed string functions to support multi-byte strings, this means supporting unicode characters. However, the PREG functions in PHP calculate [string lengths and positions in _bytes_, and not character lengths](https://www.php.net/manual/en/function.preg-match.php#refsect1-function.preg-match-parameters). For that reason, we will generally **not** use the multi-byte variants of string functions; there are exceptions to this policy.

An exception to this policy is the use of functions that aren't used for calculating string lengths or positions; e.g. `mb_strtolower`.
