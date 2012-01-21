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
use net\stubbles\lang\Object;
/**
 * Interface for cache storages.
 */
interface CacheStorage extends Object
{
    /**
     * puts date into the cache
     *
     * Returns amount of cached bytes or false if caching failed.
     *
     * @param   string    $key   key under which the data should be stored
     * @param   string    $data  data that should be cached
     * @return  int|bool
     */
    public function put($key, $data);

    /**
     * checks whether data is cached under the given key
     *
     * @param   string  $key
     * @return  bool
     */
    public function has($key);

    /**
     * fetches data from the cache
     *
     * Returns null if no data is cached under the given key.
     *
     * @param   string  $key
     * @return  string
     */
    public function get($key);

    /**
     * removes data with that key from storage
     *
     * @param  string  $key
     */
    public function remove($key);

    /**
     * returns the time in seconds how long the data associated with $key is cached
     *
     * @param   string  $key
     * @return  int
     */
    public function getLifeTime($key);

    /**
     * returns the allocated space of the data associated with $key in bytes
     *
     * @param   string  $key
     * @return  int
     */
    public function getSize($key);

    /**
     * returns the amount of bytes the cache data requires
     *
     * @return  int
     */
    public function getUsedSpace();

    /**
     * returns a list of all keys that are stored in the cache
     *
     * @return  string[]
     */
    public function getKeys();
}
?>