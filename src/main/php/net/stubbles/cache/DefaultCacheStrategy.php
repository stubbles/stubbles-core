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
use net\stubbles\lang\exception\IllegalArgumentException;
/**
 * Default caching strategy.
 */
class DefaultCacheStrategy extends BaseObject implements CacheStrategy
{
    /**
     * time to live for single cached data
     *
     * @type  int
     */
    protected $timeToLive    = 86400;
    /**
     * maximum size of cache
     *
     * To allow an infinite size set this to -1.
     *
     * @type  string
     */
    protected $maxSize       = -1;
    /**
     * probability of a garbage collection run
     *
     * Should be a value between 0 and 100 where 0 means never and 100 means always.
     *
     * @type  int
     */
    protected $gcProbability = 10;

    /**
     * sets the time to live for cache entries in seconds
     *
     * @param   int  $timeToLive
     * @return  DefaultCacheStrategy
     * @throws  IllegalArgumentException
     * @since   1.1.0
     * @Inject(optional=true)
     * @Named('net.stubbles.cache.timeToLive')
     */
    public function setTimeToLive($timeToLive)
    {
        settype($timeToLive, 'integer');
        if (0 > $timeToLive) {
            throw new IllegalArgumentException('timeToLive should not be negative');
        }

        $this->timeToLive = $timeToLive;
        return $this;
    }

    /**
     * sets the maximum cache size in bytes
     *
     * Setting the size to -1 means unlimited.
     *
     * @param   int  $maxSize
     * @return  DefaultCacheStrategy
     * @since   1.1.0
     * @Inject(optional=true)
     * @Named('net.stubbles.cache.maxSize')
     */
    public function setMaxCacheSize($maxSize)
    {
        $this->maxSize = (int) $maxSize;
        return $this;
    }

    /**
     * sets the probability of a garbage collection run
     *
     * @param   int  $gcProbability  probability that a garbage collection is run, between 0 and 100
     * @return  DefaultCacheStrategy
     * @throws  IllegalArgumentException
     * @since   1.1.0
     * @Inject(optional=true)
     * @Named('net.stubbles.cache.gcProbability')
     */
    public function setGcProbability($gcProbability)
    {
        settype($gcProbability, 'integer');
        if (0 > $gcProbability || 100 < $gcProbability) {
            throw new IllegalArgumentException('gcProbability must be between 0 and 100');
        }

        $this->gcProbability = $gcProbability;
        return $this;
    }

    /**
     * checks whether an item is cacheable or not
     *
     * @param   CacheStorage  $storage  the container to cache the data in
     * @param   string        $key      the key to cache the data under
     * @param   string        $data     data to cache
     * @return  bool
     */
    public function isCachable(CacheStorage $storage, $key, $data)
    {
        if (-1 == $this->maxSize) {
            return true;
        }

        if (($storage->getUsedSpace() + strlen($data) - $storage->getSize($key)) > $this->maxSize) {
            return false;
        }

        return true;
    }

    /**
     * checks whether a cached item is expired
     *
     * @param   CacheStorage  $storage  the container that contains the cached data
     * @param   string        $key      the key where the data is cached under
     * @return  bool
     */
    public function isExpired(CacheStorage $storage, $key)
    {
        return ($storage->getLifeTime($key) > $this->timeToLive);
    }

    /**
     * checks whether the garbage collection should be run
     *
     * @param   CacheStorage  $storage
     * @return  bool
     */
    public function shouldRunGc(CacheStorage $storage)
    {
        if (rand(1, 100) < $this->gcProbability) {
            return true;
        }

        return false;
    }
}
?>