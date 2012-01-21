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
use net\stubbles\ioc\InjectionProvider;
use net\stubbles\lang\BaseObject;
/**
 * Provider for caches.
 */
class CacheProvider extends BaseObject implements InjectionProvider
{
    /**
     * provider for cache strategy
     *
     * @type  CacheStrategyProvider
     */
    private $strategyProvider;
    /**
     * provider for cache storage
     *
     * @type  CacheStorageProvider
     */
    private $storageProvider;

    /**
     * constructor
     *
     * @param  CacheStrategyProvider  $strategyProvider
     * @param  CacheStorageProvider   $storageProvider
     * @Inject
     */
    public function __construct(CacheStrategyProvider $strategyProvider, CacheStorageProvider $storageProvider)
    {
        $this->strategyProvider = $strategyProvider;
        $this->storageProvider  = $storageProvider;
    }

    /**
     * returns the requested cache
     *
     * @param   string  $name  name of requested cache
     * @return  Cache
     */
    public function get($name = null)
    {
        $cache = new Cache($this->strategyProvider->get($name), $this->storageProvider->get($name));
        return $cache->gc();
    }
}
?>