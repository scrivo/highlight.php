#!/usr/bin/env bash

HLJS_V="11.8.0"
HLJS_DL="https://api.github.com/repos/highlightjs/highlight.js/tarball/$HLJS_V"

rm -rf lib_highlight/ lib_highlight.tar.gz ../src/Highlight/styles/ 2> /dev/null
mkdir lib_highlight/ ../src/Highlight/styles/

curl -L $HLJS_DL --output "lib_highlight.tar.gz"
tar xzf lib_highlight.tar.gz -C lib_highlight --strip-components 1

# Build a full browser build and copy it over to our demo
cd lib_highlight/
npm install
node tools/build -t browser
cp build/highlight.min.js ../../demo/

# Build highlight.js
node tools/build.js -t node

# Clean up after ourselves
cd ..
rm lib_highlight.tar.gz

# Copy styles from highlight.js to our own styles directory
rm -r ../src/Highlight/styles/ 2> /dev/null
mkdir -p ../src/Highlight/styles/
cp -a lib_highlight/src/styles/ ../src/Highlight/styles/
php get_styles_colors.php

# Copy unit tests
rm -r ../test/Highlight/detect/ 2> /dev/null
rm -r ../test/Highlight/markup/ 2> /dev/null

mkdir -p ../test/Highlight/{detect,markup}/
cp -a lib_highlight/test/detect/ ../test/Highlight/detect/
cp -a lib_highlight/test/markup/ ../test/Highlight/markup/

rm ../test/Highlight/{detect,markup}/index.js 2> /dev/null

# Translate Language Definitions
rm -rf vendor/
composer install
composer translate
rm output/*.js.php
cp output/* ../src/Highlight/languages/
