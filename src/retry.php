<?php

namespace igorw;

class FailingTooHardException extends \Exception {}

/**
 * Try to execute some operation with retry limit
 *
 * @param  int|float  $retries maximum number of tries
 * @param  callable   $fn      operation to be executed
 * @throws \igorw\FailingTooHardException If maximum amount of $retries is exceeded
 */
function retry($retries, callable $fn)
{
    beginning:
    try {
        return $fn();
    } catch (\Exception $e) {
        if (!$retries--) {
            throw new FailingTooHardException('', 0, $e);
        }
        goto beginning;
    }
}
