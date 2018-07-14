<?php
namespace Ming\Component\OptimisticLock;

interface LockInterface
{
    /**
     * 获取锁名称
     *
     * @return string
     */
    public function getName(): string;

    /**
     * getValue 获取锁的值
     *
     * @author chenmingming
     * @return string
     */
    public function getValue(): string;

    /**
     * 获取锁的有效期 单位秒
     *
     * @return int
     */
    public function getExpire(): int;

    /**
     * getTimeout 获取超时时间
     *
     * @return int
     */
    public function getTimeout(): int;

    /**
     * getWaitIntervalUs 获取等待微秒数
     *
     * @return int
     */
    public function getWaitIntervalUs(): int;

}