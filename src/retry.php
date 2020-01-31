<?php

namespace igorw;

class FailingTooHardException extends \Exception {}

function retry($retries, callable $fn, callable $onError = null)
{
    do
    {
        try {
            return $fn();
        } catch (\Exception $e) {}
        if ($onError) {
            $onError($e);
        }
    }
    while ($retries--);
    throw new FailingTooHardException('', 0, $e);
}

