<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2018/7/14
 * Time: 15:12
 */

namespace Tests;

use Ming\Component\OptimisticLock\Locker;
use PHPUnit\Framework\TestCase;

class LockerTest extends TestCase
{
    public function testCaseLock()
    {
        $lock = new Locker('test');

        $this->assertSame('test', $lock->getName());
        $this->assertStringStartsWith('lock', $lock->getValue());

        $lock->setExpire(12)->setTimeout(13)->setWaitIntervalUs(14);
        $this->assertSame(12, $lock->getExpire());
        $this->assertSame(13, $lock->getTimeout());
        $this->assertSame(14, $lock->getWaitIntervalUs());

    }
}