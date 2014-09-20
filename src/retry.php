<?php

namespace igorw;

class FailingTooHardException extends \Exception {}

function retry($retries, callable $fn, callable $onError = null)
{
    beginning:
    try {
        return $fn();
    } catch (\Exception $e) {
        if ($onError) {
            $onError($e);
        }
        if (!$retries) {
            throw new FailingTooHardException('', 0, $e);
        }
        $retries--;
        goto beginning;
    }
}
