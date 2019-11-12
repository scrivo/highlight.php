<?php

namespace Highlight;

/**
 * @property string $terminatorEnd (DEPRECATED)
 *
 * @see https://highlightjs.readthedocs.io/en/latest/reference.html
 */
class Mode
{
    // Language definition set via language definition JSON files

    /** @var bool */
    public $case_insensitive = false;

    /** @var array */
    public $aliases = array();

    /** @var string */
    public $className = '';

    /** @var string */
    public $begin = '';

    /** @var RegEx */
    public $beginRe;

    /** @var string */
    public $end = '';

    /** @var RegEx */
    public $endRe;

    /** @var string */
    public $beginKeywords = '';

    /** @var bool */
    public $endsWithParent = false;

    /** @var bool */
    public $endsParent = false;

    /** @var bool */
    public $endSameAsBegin = false;

    /** @var string */
    public $lexemes = '';

    /** @var RegEx */
    public $lexemesRe;

    /** @var array<string, array> */
    public $keywords = array();

    /** @var string */
    public $illegal = '';

    /** @var RegEx */
    public $illegalRe;

    /** @var bool */
    public $excludeBegin = false;

    /** @var bool */
    public $excludeEnd = false;

    /** @var bool */
    public $returnBegin = false;

    /** @var bool */
    public $returnEnd = false;

    /** @var array */
    public $contains = array();

    /** @var string */
    public $starts = '';

    /** @var array */
    public $variants = array();

    /** @var string|array */
    public $subLanguage;

    /** @var bool */
    public $skip = false;

    /** @var bool */
    public $disableAutodetect = false;

    // Properties set at runtime by the language compilation process

    /** @var array */
    public $cachedVariants = array();

    /** @var Terminators */
    public $terminators;

    /** @var string */
    public $terminator_end = '';

    /** @var bool */
    public $compiled = false;

    /** @var int */
    public $relevance = 1;

    public function __construct(array $data)
    {
        $this->terminators = new Terminators();

        $patch = array(
            "begin" => true,
            "end" => true,
            "lexemes" => true,
            "illegal" => true,
        );

        foreach ($data as $key => $value) {
            if (isset($patch[$key])) {
                $value = str_replace("\\/", "/", $value);
                $value = str_replace("/", "\\/", $value);
            }

            $this->{$key} = $value;
        }
    }

    public function __get($name)
    {
        if ($name === 'terminatorEnd') {
            @trigger_error("The $name property has been deprecated; use `terminator_end` instead.", E_USER_DEPRECATED);

            return $this->terminator_end;
        }

        return null;
    }
}
