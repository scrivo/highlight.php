<?php

/* Copyright (c) 2019 Geert Bergman (geert@scrivo.nl), highlight.php
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 *    this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 * 3. Neither the name of "highlight.js", "highlight.php", nor the names of its
 *    contributors may be used to endorse or promote products derived from this
 *    software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Highlight;

/**
 * A PHP representation of a Mode in the JS library.
 *
 * @internal
 *
 * @since 9.16.0.0
 *
 * Language definition set via language definition JSON files
 *
 * @property bool                                  $case_insensitive  = false
 * @property string[]                              $aliases           = array()
 * @property string|null                           $className         = null
 * @property string|null                           $begin             = null
 * @property RegEx|null                            $beginRe           = null
 * @property string|null                           $end               = null
 * @property RegEx|null                            $endRe             = null
 * @property string|null                           $beginKeywords     = null
 * @property bool                                  $endsWithParent    = false
 * @property bool                                  $endsParent        = false
 * @property bool                                  $endSameAsBegin    = false
 * @property string|null                           $lexemes           = null
 * @property RegEx|null                            $lexemesRe         = null
 * @property array<string, array<int, string|int>> $keywords          = array()
 * @property string|null                           $illegal           = null
 * @property RegEx|null                            $illegalRe         = null
 * @property bool                                  $excludeBegin      = false
 * @property bool                                  $excludeEnd        = false
 * @property bool                                  $returnBegin       = false
 * @property bool                                  $returnEnd         = false
 * @property Mode[]                                $contains          = array()
 * @property Mode|null                             $starts            = null
 * @property Mode[]                                $variants          = array()
 * @property int|null                              $relevance         = null
 * @property string|string[]|null                  $subLanguage       = null
 * @property bool                                  $skip              = false
 * @property bool                                  $disableAutodetect = false
 *
 * Properties set at runtime by the language compilation process
 * @property array<int, Mode> $cachedVariants = array()
 * @property Terminators|null $terminators    = null
 * @property string           $terminator_end = ""
 * @property bool             $compiled       = false
 * @property Mode|null        $parent         = null
 * @property string           $type           = ''
 *
 * @see https://highlightjs.readthedocs.io/en/latest/reference.html
 */
class Mode extends \stdClass
{
    /** @var string[] properties that hold (lists of) modes */
    private const MODE_PROPERTIES = array('contains', 'variants', 'starts');

    /**
     * Creates a mode from an array of properties, as an object literal would in
     * a highlight.js language definition.
     *
     * Elements of $properties that are PHP references stay references, so that
     * an array shared by several modes (and modified afterwards) behaves like a
     * shared JavaScript array while the language definition is being built.
     * Language::finalize() breaks these references once the definition is done.
     *
     * Nested arrays describing modes (in `contains`, `variants` and `starts`)
     * are converted to Mode objects right away, so they keep their identity
     * when the mode is copied with `Hljs::inherit()`, as in JavaScript.
     *
     * @param array<string, mixed> $properties
     */
    public function __construct(array $properties = array())
    {
        foreach ($properties as $key => &$value) {
            $this->{$key} = &$value;
        }
        unset($value);

        self::convertNestedModes($this);
    }

    /**
     * Converts arrays describing modes in `contains`, `variants` and `starts`
     * into Mode objects (not recursively into existing Mode objects).
     *
     * @return void
     */
    public static function convertNestedModes(Mode $mode)
    {
        foreach (self::MODE_PROPERTIES as $key) {
            if (!isset($mode->{$key}) || !is_array($mode->{$key})) {
                continue;
            }

            if ($key === 'starts' || !self::isList($mode->{$key})) {
                $mode->{$key} = new Mode($mode->{$key});

                continue;
            }

            // write through the property, it may be a reference to a shared array
            foreach (array_keys($mode->{$key}) as $i) {
                if (is_array($mode->{$key}[$i])) {
                    $mode->{$key}[$i] = new Mode($mode->{$key}[$i]);
                }
            }
        }
    }

    /**
     * array_is_list() for PHP < 8.1.
     *
     * @param array<mixed> $array
     *
     * @return bool
     */
    private static function isList(array $array)
    {
        $i = 0;
        foreach ($array as $key => $value) {
            if ($key !== $i++) {
                return false;
            }
        }

        return true;
    }

    /**
     * Turns a freshly defined mode graph into a plain graph of Mode objects.
     *
     * - mode definitions given as arrays are converted to Mode objects;
     * - references used while building the definition are broken.
     *
     * Conversion happens before references are broken, so a mode array that is
     * shared through a reference becomes a single shared Mode object.
     *
     * @return Mode
     */
    public static function finalize(Mode $root)
    {
        $modes = new \SplObjectStorage();
        self::convertModeArrays($root, $modes);

        foreach ($modes as $mode) {
            $properties = get_object_vars($mode);
            foreach ($properties as $key => $value) {
                unset($mode->{$key});
            }
            foreach ($properties as $key => $value) {
                $mode->{$key} = $value;
            }
        }

        return $root;
    }

    /**
     * @param \SplObjectStorage<Mode, bool> $visited
     *
     * @return void
     */
    private static function convertModeArrays(Mode $mode, \SplObjectStorage $visited)
    {
        if (isset($visited[$mode])) {
            return;
        }
        $visited[$mode] = true;

        // modes may have been assigned arrays after they were created
        self::convertNestedModes($mode);

        foreach (self::MODE_PROPERTIES as $key) {
            if (!isset($mode->{$key})) {
                continue;
            }
            $children = $mode->{$key} instanceof Mode ? array($mode->{$key}) : $mode->{$key};
            foreach ($children as $child) {
                if ($child instanceof Mode) {
                    self::convertModeArrays($child, $visited);
                }
            }
        }
    }

    /**
     * Fill in the missing properties that this Mode does not have.
     *
     * @internal
     *
     * @param \stdClass|null $obj
     *
     * @since 9.16.0.0
     *
     * @return void
     */
    public static function _normalize(&$obj)
    {
        // Don't overload our Modes if we've already normalized it
        if (isset($obj->__IS_COMPLETE)) {
            return;
        }

        if ($obj === null) {
            $obj = new \stdClass();
        }

        // These values come in from JSON definition files
        $defaultValues = array(
            "case_insensitive" => false,
            "aliases" => array(),
            "className" => null,
            "begin" => null,
            "beginRe" => null,
            "end" => null,
            "endRe" => null,
            "beginKeywords" => null,
            "endsWithParent" => false,
            "endsParent" => false,
            "endSameAsBegin" => false,
            "lexemes" => null,
            "lexemesRe" => null,
            "keywords" => array(),
            "illegal" => null,
            "illegalRe" => null,
            "excludeBegin" => false,
            "excludeEnd" => false,
            "returnBegin" => false,
            "returnEnd" => false,
            "contains" => array(),
            "starts" => null,
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
}
