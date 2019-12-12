<?php

namespace Highlight;

/**
 * A PHP representation of a Mode in the JS library.
 *
 * @internal
 * @since 9.16.0.0
 * @mixin ModeDeprecations
 *
 * Language definition set via language definition JSON files
 *
 * @property bool $case_insensitive = false
 * @property array $aliases = array()
 * @property string $className = ""
 * @property string $begin = ""
 * @property RegEx|null $beginRe = null
 * @property string $end = ""
 * @property RegEx|null $endRe = null
 * @property string $beginKeywords = ""
 * @property bool $endsWithParent = false
 * @property bool $endsParent = false
 * @property bool $endSameAsBegin = false
 * @property string $lexemes = ""
 * @property RegEx|null $lexemesRe = null
 * @property array<string, array> $keywords = array()
 * @property string $illegal = ""
 * @property RegEx|null $illegalRe = null
 * @property bool $excludeBegin = false
 * @property bool $excludeEnd = false
 * @property bool $returnBegin = false
 * @property bool $returnEnd = false
 * @property Mode[] $contains = array()
 * @property Mode $starts = ""
 * @property array $variants = array()
 * @property int|null $relevance = null
 * @property string|array|null $subLanguage = null
 * @property bool $skip = false
 * @property bool $disableAutodetect = false
 *
 * Properties set at runtime by the language compilation process
 *
 * @property array $cachedVariants = array()
 * @property Terminators|null $terminators = null
 * @property string $terminator_end = ""
 * @property bool $compiled = false
 * @property Mode|null $parent = null
 * @property string $type = ''
 *
 * @see https://highlightjs.readthedocs.io/en/latest/reference.html
 */
abstract class Mode
{
    /**
     * Fill in the missing properties that this Mode does not have.
     *
     * @internal
     * @since 9.16.0.0
     *
     * @param \stdClass|null $obj
     */
    public static function _normalize(\stdClass &$obj)
    {
        // Don't overload our Modes if we've already normalized it
        if (isset($obj->__IS_COMPLETE)) {
            return;
        }

        if ($obj === null) {
            $obj = new \stdClass();
        }

        $patch = array(
            "begin" => true,
            "end" => true,
            "lexemes" => true,
            "illegal" => true,
        );

        // These values come in from JSON definition files
        $defaultValues = array(
            "case_insensitive" => false,
            "aliases" => array(),
            "className" => "",
            "begin" => "",
            "beginRe" => null,
            "end" => "",
            "endRe" => null,
            "beginKeywords" => "",
            "endsWithParent" => false,
            "endsParent" => false,
            "endSameAsBegin" => false,
            "lexemes" => "",
            "lexemesRe" => null,
            "keywords" => array(),
            "illegal" => "",
            "illegalRe" => null,
            "excludeBegin" => false,
            "excludeEnd" => false,
            "returnBegin" => false,
            "returnEnd" => false,
            "contains" => array(),
            "starts" => "",
            "variants" => array(),
            "relevance" => null,
            "subLanguage" => null,
            "skip" => false,
            "disableAutodetect" => false,
        );

        // These values are set at runtime
        $runTimeValues = array(
            "cachedVariants" => array(),
            "terminators" => null,
            "terminator_end" => "",
            "compiled" => false,
            "parent" => null,

            // This value is unique to highlight.php Modes
            "__IS_COMPLETE" => true,
        );

        foreach ($patch as $k => $v) {
            if (isset($obj->{$k})) {
                $obj->{$k} = str_replace("\\/", "/", $obj->{$k});
                $obj->{$k} = str_replace("/", "\\/", $obj->{$k});
            }
        }

        foreach ($defaultValues as $k => $v) {
            if (!isset($obj->{$k}) && is_object($obj)) {
                $obj->{$k} = $v;
            }
        }

        foreach ($runTimeValues as $k => $v) {
            if (is_object($obj)) {
                $obj->{$k} = $v;
            }
        }
    }

    /**
     * Set any deprecated properties values to their replacement values.
     *
     * @internal
     *
     * @param \stdClass $obj
     */
    public static function _handleDeprecations(\stdClass &$obj)
    {
        $deprecations = [
            // @TODO Deprecated since 9.16.0.0; remove at 10.x
            'caseInsensitive' => 'case_insensitive',
            'terminatorEnd' => 'terminator_end',
        ];

        foreach ($deprecations as $deprecated => $new) {
            $obj->{$deprecated} = &$obj->{$new};
        }
    }
}
