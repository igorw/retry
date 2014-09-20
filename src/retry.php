<?php

namespace igorw;

class Result
{
    public $success;
    public $value;
    public $exceptions;
    function __construct($success, $value, array $exceptions)
    {
        $this->success = $success;
        $this->value = $value;
        $this->exceptions = $exceptions;
    }
}

function retry($retries, callable $fn)
{
    $exceptions = [];
    beginning:
    try {
        return new Result(
            true,
            $fn(),
            $exceptions
        );
    } catch (\Exception $e) {
        $exceptions[] = $e;
        if (!$retries) {
            return new Result(
                false,
                null,
                $exceptions
            );
        }
        $retries--;
        goto beginning;
    }
}
