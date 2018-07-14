<?php

namespace Ming\Component\OptimisticLock;

class LockException extends \Exception
{
    const TIMEOUT = 1001;
    const LUA_SCRIPT_NO_SUPPORT = 1002;

    private $lock;

    public function __construct($message, int $code, LockInterface $lock)
    {
        $this->lock = $lock;
        parent::__construct($message, $code);
    }

    /**
     * timeoutException
     *
     * @param LockInterface $lock
     *
     * @return LockException
     */
    static public function timeoutException(LockInterface $lock)
    {
        return new self("get lock timeout", self::TIMEOUT, $lock);
    }

    /**
     * NoSupportLuaScript
     * @param LockInterface $lock
     *
     * @return LockException
     */
    static public function noSupportLuaScript(LockInterface $lock)
    {
        return new self("this redis server does not support lua script", self::LUA_SCRIPT_NO_SUPPORT, $lock);
    }

    /**
     * getLock
     *
     * @author chenmingming
     * @return LockInterface
     */
    public function getLock()
    {
        return $this->lock;
    }
}