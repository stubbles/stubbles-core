<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\cache;
use net\stubbles\lang\BaseObject;
/**
 * Abstract base class for cache containers.
 */
abstract class AbstractCacheContainer extends BaseObject implements CacheContainer
{
    /**
     * the strategy used for decisions about caching
     *
     * @type  CacheStrategy
     */
    protected $strategy;
    /**
     * unix timestamp when last run of garbage collection happened
     *
     * @type  int
     */
    private $lastGcRun = null;

    /**
     * puts date into the cache
     *
     * Returns amount of cached bytes or false if caching failed.
     *
     * @param   string    $key   key under which the data should be stored
     * @param   string    $data  data that should be cached
     * @return  int|bool
     */
    public function put($key, $data)
    {
        if (!$this->strategy->isCachable($this, $key, $data)) {
            return false;
        }

        return $this->doPut($key, $data);
    }

    /**
     * puts date into the cache
     *
     * Returns amount of cached bytes or false if caching failed.
     *
     * @param   string    $key   key under which the data should be stored
     * @param   string    $data  data that should be cached
     * @return  int|bool
     */
    protected abstract function doPut($key, $data);

    /**
     * checks whether data is cached under the given key
     *
     * @param   string  $key
     * @return  bool
     */
    public function has($key)
    {
        if ($this->strategy->isExpired($this, $key)) {
            return false;
        }

        return $this->doHas($key);
    }

    /**
     * checks whether data is cached under the given key
     *
     * @param   string  $key
     * @return  bool
     */
    protected abstract function doHas($key);

    /**
     * checks whether cache data is expired
     *
     * @param   string  $key   key under which the data is stored
     * @return  bool
     */
    public function isExpired($key)
    {
        return $this->strategy->isExpired($this, $key);
    }

    /**
     * fetches data from the cache
     *
     * Returns null if no data is cached under the given key.
     *
     * @param   string  $key
     * @return  string
     */
    public function get($key)
    {
        if ($this->strategy->isExpired($this, $key)) {
            return null;
        }

        return $this->doGet($key);
    }

    /**
     * fetches data from the cache
     *
     * Returns null if no data is cached under the given key.
     *
     * @param   string  $key
     * @return  string
     */
    protected abstract function doGet($key);

    /**
     * returns the allocated space of the data associated with $key in bytes
     *
     * @param   string  $key
     * @return  int
     */
    public function getSize($key)
    {
        if ($this->has($key)) {
            return $this->doGetSize($key);
        }

        return 0;
    }

    /**
     * returns the allocated space of the data associated with $key in bytes
     *
     * @param   string  $key
     * @return  int
     */
    protected abstract function doGetSize($key);

    /**
     * returns the unix timestamp of the last run of the garbage collection
     *
     * @return  int
     */
    public function lastGcRun()
    {
        return $this->lastGcRun;
    }

    /**
     * runs the garbage collection
     *
     * @return  stubCacheContainer
     */
    public function gc()
    {
        if ($this->strategy->shouldRunGc($this)) {
            $this->doGc();
            $this->lastGcRun = time();
        }

        return $this;
    }

    /**
     * runs the garbage collection
     */
    protected abstract function doGc();
}
?>