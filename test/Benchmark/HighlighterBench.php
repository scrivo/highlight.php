<?php

namespace Highlight\Benchmark;

use Highlight\Highlighter;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;

class HighlighterBench
{
    /**
     * @Revs(500)
     * @Iterations(5)
     */
    public function benchLoadNoLanguages()
    {
        new Highlighter(false);
        Highlighter::clearAllLanguages();
    }

    /**
     * @Revs(500)
     * @Iterations(5)
     */
    public function benchLoadAllLanguages()
    {
        new Highlighter(true);
        Highlighter::clearAllLanguages();
    }
}
