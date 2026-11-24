#!/bin/bash

set -e

HLJS_V="10.0.0"

HLJS_DL="https://api.github.com/repos/highlightjs/highlight.js/tarball/$HLJS_V"

curl -L $HLJS_DL --output "lib_highlight.tar.gz"

rm -rf lib_highlight 2> /dev/null
mkdir lib_highlight
tar xzf lib_highlight.tar.gz -C lib_highlight --strip-components 1

# Transpile the language definitions into PHP classes and verify them
(cd transpiler && npm install --silent)
rm -f ../src/Highlight/Languages/*.php
node transpiler/generate.js lib_highlight
bash transpiler/verify.sh lib_highlight

# Copy styles from highlight.js to our own styles directory
rm -r ../src/Highlight/styles/ 2> /dev/null
mkdir -p ../src/Highlight/styles/
cp -a lib_highlight/src/styles/. ../src/Highlight/styles/
php get_styles_colors.php

# Copy unit tests
rm -r ../test/Highlight/detect/ 2> /dev/null
rm -r ../test/Highlight/markup/ 2> /dev/null

cp -a lib_highlight/test/detect ../test/Highlight/detect
cp -a lib_highlight/test/markup ../test/Highlight/markup

rm ../test/Highlight/{detect,markup}/index.js 2> /dev/null

# Build a full browser build and copy it over to our demo
cd lib_highlight
npm install
node tools/build -t browser
cp build/highlight.js ../../demo/highlight.pack.js
cd ..
