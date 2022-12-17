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

require __DIR__ . '/../vendor/autoload.php';

if (!class_exists("PHPUnit_Framework_TestCase"))
{
    class_alias('\PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase');
}

if (!class_exists("PHPUnit_Framework_Error"))
{
    class_alias('PHPUnit\Framework\Error\Warning', 'PHPUnit_Framework_Error');
}

if (!class_exists("PHPUnit_Util_ErrorHandler"))
{
    class PHPUnit_Util_ErrorHandler {
        public static function handleError($errno, $errstr, $errfile, $errline)
        {
            $errorHandler = new \PHPUnit\Util\ErrorHandler(
                true,
                true,
                true,
                true
            );

            return $errorHandler($errno, $errstr, $errfile, $errline);
        }
    }
}

/**
 * A modified version of PHPUnit's TestCase to rid ourselves of deprecation
 * warnings since we're using two different versions of PHPUnit in this branch
 * (PHPUnit 4 and 5).
 */
class BC_PHPUnit_Framework_TestCase extends \PHPUnit_Framework_TestCase {
    public function bc_expectException($exception)
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException($exception);
        } elseif (method_exists($this, 'setExpectedException')) {
            $this->setExpectedException($exception);
        }
    }
}

// Throw an exception when E_ERRORs occur to better debug the problem
function throwExceptionOnError($severity, $message, $file, $line)
{
    $errMessage = sprintf('Language error ("%s") thrown from %s:%s', $message, $file, $line);

    throw new \Exception($errMessage, $severity);
}

// If `DISABLE_WARN_EXCEPTIONS` exists, then E_ERRORs will **not** be casted into exceptions
if (!getenv('DISABLE_WARN_EXCEPTIONS')) {
    set_error_handler('throwExceptionOnError');
}
