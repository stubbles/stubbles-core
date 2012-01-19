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
use net\stubbles\ioc\Binder;
use net\stubbles\ioc\module\BindingModule;
use net\stubbles\lang\BaseObject;
/**
 * Binding module for cache containers.
 */
class CacheBindingModule extends BaseObject implements BindingModule
{
    /**
     * path to cache directory
     *
     * @var  string
     */
    protected $cachePath;
    /**
     * cache strategy to be used
     *
     * @var  CacheStrategy
     */
    protected $cacheStrategy;
    /**
     * provider class for creating the cache container instances
     *
     * @var  string
     */
    protected $cacheContainerProviderClass = 'net\\stubbles\\cache\\CacheProvider';
    /**
     * configure values for default cache strategy
     *
     * @var  array
     */
    protected $defaultStrategyValues       = array('ttl'           => 86400,
                                                   'maxSize'       => -1,
                                                   'gcProbability' => 10
                                             );

    /**
     * constructor
     *
     * Please note that the cache path is only optional if it is bound by
     * another module.
     *
     * @param  string  $cachePath                    path to cache directory
     * @param  string  $cacheContainerProviderClass  provider implementation which creates cache container instances
     */
    public function __construct($cachePath = null, $cacheContainerProviderClass = null)
    {
        if (null != $cachePath) {
            $this->cachePath = $cachePath;
        }

        if (null != $cacheContainerProviderClass) {
            $this->cacheContainerProviderClass = $cacheContainerProviderClass;
        }
    }

    /**
     * static constructor
     *
     * Please note that the cache path is only optional if it is bound by
     * another module.
     *
     * @param   string  $cachePath                    path to cache directory
     * @param   string  $cacheContainerProviderClass  provider implementation which creates cache container instances
     * @return  CacheBindingModule
     */
    public static function create($cachePath = null, $cacheContainerProviderClass = null)
    {
        return new self($cachePath, $cacheContainerProviderClass);
    }

    /**
     * sets cache strategy to be used
     *
     * @param   CacheStrategy       $cacheStrategy
     * @return  CacheBindingModule
     */
    public function setCacheStrategy(CacheStrategy $cacheStrategy)
    {
        $this->cacheStrategy = $cacheStrategy;
        return $this;
    }

    /**
     * sets config values for default cache strategy
     *
     * @param   int     $ttl            maximum time to live for cache entries
     * @param   int     $maxSize        maximum size of cache in bytes (-1 means indefinite)
     * @param   double  $gcProbability  probability of a garbage collection run between 0 and 1
     * @return  CacheBindingModule
     */
    public function setDefaultStrategyValues($ttl, $maxSize, $gcProbability)
    {
        $this->defaultStrategyValues['ttl']           = $ttl;
        $this->defaultStrategyValues['maxSize']       = $maxSize;
        $this->defaultStrategyValues['gcProbability'] = $gcProbability;
        return $this;
    }

    /**
     * configure the binder
     *
     * @param  Binder  $binder
     */
    public function configure(Binder $binder)
    {
        if (null != $this->cachePath) {
            $binder->bindConstant()
                   ->named('net.stubbles.cache.path')
                   ->to($this->cachePath);
        }

        $binder->bind('net\\stubbles\\cache\\CacheStrategy')
               ->toInstance($this->createStrategy());
        $binder->bind('net\\stubbles\\cache\\CacheContainer')
               ->toProviderClass($this->cacheContainerProviderClass);
    }

    /**
     * creates the cache strategy to be used
     *
     * @return  CacheStrategy
     */
    protected function createStrategy()
    {
        if (null !== $this->cacheStrategy) {
            return $this->cacheStrategy;
        }

        $this->cacheStrategy = new DefaultCacheStrategy();
        return $this->cacheStrategy->setTimeToLive($this->defaultStrategyValues['ttl'])
                                   ->setMaxCacheSize($this->defaultStrategyValues['maxSize'])
                                   ->setGcProbability($this->defaultStrategyValues['gcProbability']);
    }
}
?>