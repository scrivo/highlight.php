#!/bin/bash

HLJS_V="9.12.0"
DOJO_V="1.13.0"

HLJS_DL="https://api.github.com/repos/isagalaev/highlight.js/tarball/$HLJS_V"
DOJO_DL="http://download.dojotoolkit.org/release-$DOJO_V/dojo-release-$DOJO_V-src.tar.gz"

curl -L $HLJS_DL --output "lib_highlight.tar.gz"
curl -L $DOJO_DL --output "lib_dojo.tar.gz"

rm -rf lib_dojo lib_highlight 2> /dev/null

mkdir lib_dojo lib_highlight

tar xzf lib_dojo.tar.gz -C lib_dojo --strip-components 1
tar xzf lib_highlight.tar.gz -C lib_highlight --strip-components 1

cd lib_highlight
npm install
node tools/build.js -t node

cd ..
node launcher.js > languages.dat
php get_language_definitions.php
