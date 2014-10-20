<?php

namespace igorw;

function retry($retries, callable $fn)
{
    do{
        try {
            return $fn();
        } catch (\Exception $e) { }
    }while($retries--)
    throw $e;
}
