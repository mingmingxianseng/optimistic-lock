<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2018/7/14
 * Time: 15:25
 */

namespace Tests;

use Ming\Component\OptimisticLock\Locker;
use Ming\Component\OptimisticLock\LockException;
use PHPUnit\Framework\TestCase;

class LockerExceptionTest extends TestCase
{
    public function testCase()
    {
        $lock = new Locker('111');
        $e    = LockException::timeoutException($lock);

        $this->assertInstanceOf(LockException::class, $e);
        $this->assertSame(LockException::TIMEOUT, $e->getCode());

        $this->assertSame($lock, $e->getLock());

        $e = LockException::noSupportLuaScript($lock);

        $this->assertSame(LockException::LUA_SCRIPT_NO_SUPPORT, $e->getCode());
    }
}