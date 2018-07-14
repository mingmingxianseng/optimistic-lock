<?php
namespace Ming\Component\OptimisticLock;

class Locker implements LockInterface
{
    /**
     * @var string
     */
    protected $name;
    /**
     * @var string
     */
    protected $value;

    /**
     * @var int
     */
    protected $expire = 10;
    /**
     * @var int
     */
    protected $waitIntervalUs = 10;
    /**
     * 超时时间
     *
     * @var integer
     */
    protected $timeout = 0;

    public function __construct(string $name, ?string $value = null)
    {
        $this->name = $name;
        if ($value === null) {
            $value = uniqid('lock-');
        }
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setExpire(int $expire): Locker
    {
        $this->expire = $expire;

        return $this;
    }

    public function getExpire(): int
    {
        return $this->expire;
    }

    /**
     * @return int
     */
    public function getWaitIntervalUs(): int
    {
        return $this->waitIntervalUs;
    }

    /**
     * @param int $waitIntervalUs
     *
     * @return Locker
     */
    public function setWaitIntervalUs(int $waitIntervalUs): Locker
    {
        $this->waitIntervalUs = $waitIntervalUs;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     *
     * @return Locker
     */
    public function setTimeout(int $timeout): Locker
    {
        $this->timeout = $timeout;

        return $this;
    }
}