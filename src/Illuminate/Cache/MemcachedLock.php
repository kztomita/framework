<?php

namespace Illuminate\Cache;

class MemcachedLock extends Lock
{
    /**
     * The Memcached instance.
     *
     * @var \Memcached
     */
    protected $memcached;

    /**
     * Create a new lock instance.
     *
     * @param  \Memcached  $memcached
     * @param  string  $name
     * @param  int  $seconds
     * @return void
     */
    public function __construct($memcached, $name, $seconds)
    {
        parent::__construct($name, $seconds);

        $this->memcached = $memcached;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        return $this->memcached->add(
            $this->name, $this->value(), $this->seconds
        );
    }

    /**
     * Release the lock.
     *
     * @return void
     */
    public function release()
    {
        if ($this->isOwnedByCurrentProcess()) {
            $this->memcached->delete($this->name);
        }
    }

    /**
     * Returns the value written into the driver for this lock.
     *
     * @return mixed
     */
    protected function getValue()
    {
        return $this->memcached->get($this->name);
    }
}
