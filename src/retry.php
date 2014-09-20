<?php

namespace igorw;

class FailingTooHardException extends \Exception {
    public $exceptions;
    function __construct(array $exceptions) {
        parent::__construct('', 0, array_pop(array_values($exceptions)));
        $this->exceptions = $exceptions;
    }
}

function retry($retries, callable $fn)
{
    $exceptions = [];
    beginning:
    try {
        return $fn();
    } catch (\Exception $e) {
        $exceptions[] = $e;
        if (!$retries) {
            throw new FailingTooHardException($exceptions);
        }
        $retries--;
        goto beginning;
    }
}
