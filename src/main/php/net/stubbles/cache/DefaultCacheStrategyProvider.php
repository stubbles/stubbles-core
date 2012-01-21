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
 * Provider for file cache storages.
 */
class DefaultCacheStrategyProvider extends BaseObject implements CacheStrategyProvider
{
    /**
     * configure values for default cache strategy
     *
     * @type  array
     */
    private $strategyValues = array();

    /**
     * sets config values for cache strategy
     *
     * @param   int     $name           name of cache strategy
     * @param   int     $ttl            maximum time to live for cache entries
     * @param   int     $maxSize        maximum size of cache in bytes (-1 means indefinite)
     * @param   double  $gcProbability  probability of a garbage collection run between 0 and 1
     * @return  DefaultCacheStrategyProvider
     */
    public function setDefaultStrategyValues($name, $ttl = 86400, $maxSize = -1, $gcProbability = 10)
    {
        $this->strategyValues[$name] = array('ttl'           => $ttl,
                                             'maxSize'       => $maxSize,
                                             'gcProbability' => $gcProbability
                                       );
        return $this;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  mixed
     */
    public function get($name = null)
    {
        return $this->setStrategyValues(new DefaultCacheStrategy(), $name);
    }

    /**
     * fills given default strategy with values
     *
     * @param   DefaultCacheStrategy  $strategy
     * @param   string                $name
     * @return  DefaultCacheStrategy
     */
    private function setStrategyValues(DefaultCacheStrategy $strategy, $name)
    {
        if (isset($this->strategyValues[$name])) {
            return $strategy->setTimeToLive($this->strategyValues[$name]['ttl'])
                            ->setMaxCacheSize($this->strategyValues[$name]['maxSize'])
                            ->setGcProbability($this->strategyValues[$name]['gcProbability']);
        }

        return $strategy;
    }
}
?>