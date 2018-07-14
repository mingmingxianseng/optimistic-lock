<?php
/**
 * Created by PhpStorm.
 * User: chenmingming
 * Date: 2018/7/14
 * Time: 15:27
 */

namespace Tests;

use Ming\Component\OptimisticLock\Locker;
use Ming\Component\OptimisticLock\LockException;
use Ming\Component\OptimisticLock\LockManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\NullLogger;
use SebastianBergmann\CodeCoverage\Report\PHP;

class LockFactoryTest extends TestCase
{
    public function testCase()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1');
        $manager = new LockManager($redis, new EchoLogger(), 'test:');
        $name    = time();
        $lock    = new Locker($name, 'lock.value');

        $manager->lock($lock);

        $this->assertSame('lock.value', $redis->get('test:' . $name));

        $this->assertFalse($manager->isLocked($lock));
        $manager->release($lock);

        $this->assertFalse($redis->get('test:' . $name));
        $start = microtime(true);

        $lock->setTimeout(2)->setExpire(1);

        $this->assertTrue($manager->isLocked($lock));

        $manager->lock($lock);

        $end = microtime(true);
        echo 'wait:' . ($end - $start) . 's' . PHP_EOL;

        $this->assertTrue($end - $start > 0.5);
        $redis->set('test:' . $name, uniqid());

        $manager->release($lock);
    }

    public function testInvalidRedis()
    {
        $redis = new NoLuaRedis();

        $logger  = new DemoLogger();
        $manager = new LockManager($redis, $logger);

        try {
            $manager->release(new Locker(time(), '123123'));
        } catch (LockException $e) {
            $this->assertSame(
                LockException::LUA_SCRIPT_NO_SUPPORT, $e->getCode()
            );
        }

    }
}

class NoLuaRedis extends \Redis
{
    public function eval($key)
    {
        return false;
    }

    public function getLastError()
    {
        return '';
    }

}

class EchoLogger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        echo "{$level}:{$message}" . PHP_EOL;
    }
}

class DemoLogger extends AbstractLogger
{
    private $message;

    public function log($level, $message, array $context = array())
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

}