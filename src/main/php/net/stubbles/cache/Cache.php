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
 *
 * @ProvidedBy(net\stubbles\cache\CacheProvider.class)
 */
class Cache extends BaseObject
{
    /**
     * the strategy used for decisions about caching
     *
     * @type  CacheStrategy
     */
    protected $strategy;
    /**
     * the directory to store the cache files in
     *
     * @type  CacheStorage
     */
    protected $storage;

    /**
     * constructor
     *
     * @param  CacheStrategy  $strategy
     * @param  CacheStorage   $storage
     */
    public function __construct(CacheStrategy $strategy, CacheStorage $storage)
    {
        $this->strategy = $strategy;
        $this->storage  = $storage;
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
    public function put($key, $data)
    {
        if (!$this->strategy->isCachable($this->storage, $key, $data)) {
            return false;
        }

        return $this->storage->put($key, $data);
    }

    /**
     * checks whether data is cached under the given key
     *
     * @param   string  $key
     * @return  bool
     */
    public function has($key)
    {
        if ($this->strategy->isExpired($this->storage, $key)) {
            return false;
        }

        return $this->storage->has($key);
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
        if ($this->strategy->isExpired($this->storage, $key)) {
            return null;
        }

        return $this->storage->get($key);
    }

    /**
     * returns a list of all keys that are stored in the cache
     *
     * @return  string[]
     */
    public function getKeys()
    {
        $keys = array();
        foreach ($this->storage->getKeys() as $key) {
            if (!$this->strategy->isExpired($this->storage, $key)) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * runs the garbage collection
     *
     * @return  Cache
     */
    public function gc()
    {
        if ($this->strategy->shouldRunGc($this->storage)) {
            foreach ($this->storage->getKeys() as $key) {
                if ($this->strategy->isExpired($this->storage, $key)) {
                    $this->storage->remove($key);
                }
            }
        }

        return $this;
    }
}
?>