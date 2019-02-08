<?php

require __DIR__ . '/../vendor/autoload.php';

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
