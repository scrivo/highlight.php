#!/bin/bash
# Verifies the generated PHP language classes against highlight.js by
# comparing canonical dumps of the evaluated definitions.
#
# usage: tools/transpiler/verify.sh <highlight.js dir> [language...]
set -e
DIR="$(cd "$(dirname "$0")" && pwd)"
HLJS="$1"; shift
TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

node "$DIR/dump.js" "$HLJS" "$TMP/js" "$@"
php "$DIR/dump.php" "$TMP/php" "$@"
node "$DIR/compare.js" "$TMP/js" "$TMP/php"
