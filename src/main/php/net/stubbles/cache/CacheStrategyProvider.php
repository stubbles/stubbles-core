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
/**
 * Provider for cache strategies.
 *
 * @ImplementedBy(net\stubbles\cache\DefaultCacheStrategyProvider.class)
 */
interface CacheStrategyProvider extends InjectionProvider
{
    // intentionally empty
}
?>