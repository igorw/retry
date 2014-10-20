<?php

namespace igorw;

class retry extends \Exception {}

function retry($retries, callable $fn)
{
    do {
        try {
            return $fn();
        } catch (\Exception $e) {}
    } while ($retries--);
    throw new FailingTooHardException('', 0, $e);
}
