<?php

require __DIR__ . '/../vendor/autoload.php';

// Throw an exception when E_ERRORS occur to better debug the problem
function throwExceptionOnError($severity, $message, $file, $line)
{
    $errMessage = sprintf('Language error ("%s") thrown from %s:%s', $message, $file, $line);

    throw new \Exception($errMessage, $severity);
}

set_error_handler('throwExceptionOnError');
