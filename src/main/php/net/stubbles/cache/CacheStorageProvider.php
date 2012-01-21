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
 * Provider for cache storages.
 *
 * @ImplementedBy(net\stubbles\cache\FileCacheStorageProvider.class)
 */
interface CacheStorageProvider extends InjectionProvider
{
    // intentionally empty
}
?>