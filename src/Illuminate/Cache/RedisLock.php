<?php

namespace Illuminate\Cache;

class RedisLock extends Lock
{
    /**
     * The Redis factory implementation.
     *
     * @var \Illuminate\Redis\Connections\Connection
     */
    protected $redis;

    /**
     * Create a new lock instance.
     *
     * @param  \Illuminate\Redis\Connections\Connection  $redis
     * @param  string  $name
     * @param  int  $seconds
     * @return void
     */
    public function __construct($redis, $name, $seconds)
    {
        parent::__construct($name, $seconds);

        $this->redis = $redis;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        $result = $this->redis->setnx($this->name, $this->value());

        if ($result === 1 && $this->seconds > 0) {
            $this->redis->expire($this->name, $this->seconds);
        }

        return $result === 1;
    }

    /**
     * Release the lock.
     *
     * @return void
     */
    public function release()
    {
        if ($this->isOwnedByCurrentProcess()) {
            $this->redis->del($this->name);
        }
    }

    /**
     * Returns the value written into the driver for this lock.
     *
     * @return string
     */
    protected function getValue()
    {
        return $this->redis->get($this->name);
    }
}
