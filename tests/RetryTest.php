<?php

namespace igorw;

class RetryTest extends \PHPUnit_Framework_TestCase
{
    function testRetryWithoutFailing()
    {
        $i = 0;
        $result = retry(1, function () use (&$i) {
            $i++;
            return 5;
        });

        $this->assertSame(1, $i);
        $this->assertEquals(new Result(true, 5, []), $result);
    }

    function testRetryFailingOnce()
    {
        $i = 0;
        $failed = false;
        $result = retry(1, function () use (&$i, &$failed) {
            $i++;
            if (!$failed) {
                $failed = true;
                throw new \RuntimeException('roflcopter');
            }
            return 5;
        });

        $this->assertSame(2, $i);
        $this->assertInstanceOf('igorw\Result', $result);
        $this->assertTrue($result->success);
        $this->assertSame(5, $result->value);
        $this->assertCount(1, $result->exceptions);
    }

    function testRetryFailingTooHard()
    {
        $i = 0;
        $result = retry(1, function () use (&$i) {
            $i++;
            throw new \RuntimeException('rofl');
        });

        $this->assertInstanceOf('igorw\Result', $result);
        $this->assertFalse($result->success);
        $this->assertNull($result->value);
        $this->assertCount(2, $result->exceptions);
        $this->assertInstanceof('RuntimeException', $result->exceptions[1]);
        $this->assertSame('rofl', $result->exceptions[1]->getMessage());
        $this->assertSame(2, $i);
    }

    function testRetryManyTimes()
    {
        $i = 0;
        $result = retry(10, function () use (&$i) {
            $i++;
            if ($i < 8) {
                throw new \RuntimeException('gödel escher doge');
            }
            return 5;
        });

        $this->assertSame(8, $i);
        $this->assertInstanceOf('igorw\Result', $result);
        $this->assertTrue($result->success);
        $this->assertSame(5, $result->value);
        $this->assertCount(7, $result->exceptions);
    }

    function testRetryManyTimesFailingTooHard()
    {
        $i = 0;
        $result = retry(1000, function () use (&$i) {
            $i++;
            throw new \RuntimeException('dogecoin');
        });

        $this->assertInstanceOf('igorw\Result', $result);
        $this->assertFalse($result->success);
        $this->assertNull($result->value);
        $this->assertCount(1001, $result->exceptions);
        $this->assertInstanceof('RuntimeException', $result->exceptions[1]);
        $this->assertSame('dogecoin', $result->exceptions[1]->getMessage());
        $this->assertSame(1001, $i);
    }
}
