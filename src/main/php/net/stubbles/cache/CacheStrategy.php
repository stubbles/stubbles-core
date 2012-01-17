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
 * Interface for caching strategies.
 *
 * @ImplementedBy(net\stubbles\cache\DefaultCacheStrategy.class)
 */
interface CacheStrategy extends Object
{
    /**
     * checks whether an item is cacheable or not
     *
     * @param   CacheContainer  $container  the container to cache the data in
     * @param   string          $key        the key to cache the data under
     * @param   string          $data       data to cache
     * @return  bool
     */
    public function isCachable(CacheContainer $container, $key, $data);

    /**
     * checks whether a cached item is expired
     *
     * @param   CacheContainer  $container  the container that contains the cached data
     * @param   string          $key        the key where the data is cached under
     * @return  bool
     */
    public function isExpired(CacheContainer $container, $key);

    /**
     * checks whether the garbage collection should be run
     *
     * @param   CacheContainer $container
     * @return  bool
     */
    public function shouldRunGc(CacheContainer $container);
}
?>