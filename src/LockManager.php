<?php

namespace Ming\Component\OptimisticLock;

use Psr\Log\LoggerInterface;

class LockManager
{
    /**
     * @var \Redis
     */
    private $redis;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $namespace;

    public function __construct(\Redis $redis, LoggerInterface $logger, string $namespace = 'lock:')
    {
        $this->redis     = $redis;
        $this->logger    = $logger;
        $this->namespace = $namespace;
    }

    /**
     * lock
     *
     * @author chenmingming
     *
     * @param LockInterface $lock
     *
     * @return void
     * @throws LockException
     */
    public function lock(LockInterface $lock)
    {
        $key       = $this->namespace . $lock->getName();
        $timeoutAt = time() + $lock->getTimeout();

        $this->logger->debug("try to request lock {$lock->getName()}. timeout at " . $timeoutAt);

        while (true) {
            $result = $this->redis->set($key, $lock->getValue(), ['nx', 'ex' => $lock->getExpire()]);
            if ($result === true) {
                $this->logger->debug("request lock {$lock->getName()}[{$lock->getValue()}] success.");

                return;
            }
            if ($lock->getTimeout() <= 0 || $timeoutAt < microtime(true)) break;

            usleep($lock->getWaitIntervalUs());
        }
        $this->logger->debug("request lock {$lock->getName()} timeout, wait {$lock->getTimeout()} seconds.");

        throw LockException::timeoutException($lock);
    }

    /**
     * isLocked
     *
     * @author chenmingming
     *
     * @param LockInterface $lock
     *
     * @return bool
     */
    public function isLocked(LockInterface $lock)
    {
        try {
            $this->lock($lock);

            return true;
        } catch (LockException $e) {
            return false;
        }
    }

    /**
     * release
     *
     * @author chenmingming
     *
     * @param LockInterface $lock
     *
     * @throws LockException
     */
    public function release(LockInterface $lock)
    {
        $key = $this->namespace . $lock->getName();
        $script
             = "if redis.call('get', KEYS[1]) == ARGV[1] then return redis.call('del', KEYS[1]) else return 0 end";

        $rs = $this->redis->eval($script, [$key, $lock->getValue()], 1);
        if ($rs === 1) {
            $this->logger->debug("delete lock {$lock->getName()}[{$lock->getValue()}] success");
        } elseif ($rs === 0) {
            $this->logger->debug(
                "delete lock {$lock->getName()}[{$lock->getValue()}] failed. this lock's value may has changed."
            );
        } else {
            throw LockException::noSupportLuaScript($lock);
        }
    }

}