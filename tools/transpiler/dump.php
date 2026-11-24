<?php

/*
 * Canonical dump of the PHP language definitions, in the same format as dump.js,
 * so both can be compared to verify the transpiled classes.
 *
 * usage: php dump.php <output dir> [language...]
 */

require __DIR__ . '/../../vendor/autoload.php';

use Highlight\Language;
use Highlight\Languages\Registry;
use Highlight\Mode;

function canonicalize($value)
{
    $ids = array();
    $next = 1;

    $walk = function ($value, $inKeywords) use (&$walk, &$ids, &$next) {
        if ($value === null) {
            return null;
        }
        if (is_array($value) && ($inKeywords || array_is_list($value))) {
            if (array_is_list($value)) {
                return array_map(fn ($v) => $walk($v, $inKeywords), $value);
            }
        }
        if (is_object($value) || is_array($value)) {
            $isMode = is_object($value) && !$inKeywords;
            if ($isMode && isset($ids[spl_object_id($value)])) {
                return array('$ref' => $ids[spl_object_id($value)]);
            }
            $out = array();
            if (!$inKeywords) {
                if ($isMode) {
                    $ids[spl_object_id($value)] = $next;
                }
                $out['$id'] = $next++;
            }
            $props = is_object($value) ? get_object_vars($value) : $value;
            ksort($props, SORT_STRING);
            foreach ($props as $key => $v) {
                if ($v === null || $v instanceof Closure) {
                    continue;
                }
                $out[$key] = $walk($v, $inKeywords || $key === 'keywords');
            }

            return (object) $out;
        }

        return $value;
    };

    return $walk($value, false);
}

$outDir = $argv[1];
$names = array_slice($argv, 2) ?: array_keys(Registry::LANGUAGES);

$languages = array();
$resolver = function ($name) use (&$languages, &$resolver) {
    if (!isset(Registry::LANGUAGES[$name])) {
        return null;
    }

    return $languages[$name] ??= new Language($name, Registry::LANGUAGES[$name], $resolver);
};

@mkdir($outDir, 0777, true);
foreach ($names as $name) {
    $language = new Language($name, Registry::LANGUAGES[$name], $resolver);
    $dump = canonicalize(Mode::finalize($language->rawDefinition()));
    file_put_contents("$outDir/$name.json", json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
}
