<?php

namespace igorw;

class RetryTest extends \PHPUnit_Framework_TestCase
{
    public function testRetryWithoutFailing()
    {
        $i = 0;
        $value = retry(1, function () use (&$i) {
            $i++;

            return 5;
        });
        $this->assertSame(1, $i);
        $this->assertSame(5, $value);
    }

    public function testRetryFailingOnce()
    {
        $i = 0;
        $failed = false;
        $value = retry(1, function () use (&$i, &$failed) {
            $i++;
            if (!$failed) {
                $failed = true;
                throw new \RuntimeException('roflcopter');
            }

            return 5;
        });
        $this->assertSame(2, $i);
        $this->assertSame(5, $value);
    }

    public function testRetryFailingTooHard()
    {
        $e = null;
        $i = 0;
        try {
            retry(1, function () use (&$i) {
                $i++;
                throw new \RuntimeException('rofl');
            });
        } catch (\Exception $e) {
        }

        $this->assertInstanceof('igorw\FailingTooHardException', $e);
        $this->assertInstanceof('RuntimeException', $e->getPrevious());
        $this->assertSame('rofl', $e->getPrevious()->getMessage());
        $this->assertSame(2, $i);
    }

    public function testRetryManyTimes()
    {
        $e = null;
        $i = 0;
        try {
            retry(1000, function () use (&$i, &$failed) {
                $i++;
                throw new \RuntimeException('dogecoin');
            });
        } catch (\Exception $e) {
        }

        $this->assertInstanceof('igorw\FailingTooHardException', $e);
        $this->assertInstanceof('RuntimeException', $e->getPrevious());
        $this->assertSame('dogecoin', $e->getPrevious()->getMessage());
        $this->assertSame(1001, $i);
    }
}
