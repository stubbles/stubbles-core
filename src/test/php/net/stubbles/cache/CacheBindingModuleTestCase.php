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
use net\stubbles\ioc\InjectionProvider;
use net\stubbles\lang\BaseObject;
/**
 * Helper class for the test.
 */
class DifferentCacheContainerProvider extends BaseObject implements InjectionProvider
{
    /**
     * cache container to return
     *
     * @type  CacheContainer
     */
    public static $cacheContainer;
    /**
     * returns the requested cache container
     *
     * @param   string  $name  optional  name of requested cache container
     * @return  CacheContainer
     */
    public function get($name = null)
    {
        return self::$cacheContainer;
    }
}
/**
 * Helper class to access protected properties.
 */
class DefaultCacheStrategyPropertyAccessor extends DefaultCacheStrategy
{
    /**
     * returns list of protected properties and their values
     *
     * @param   DefaultCacheStrategy  $defaultCacheStrategy
     * @return  array
     */
    public static function getProperties(DefaultCacheStrategy $defaultCacheStrategy)
    {
        return array('ttl'           => $defaultCacheStrategy->timeToLive,
                     'maxSize'       => $defaultCacheStrategy->maxSize,
                     'gcProbability' => $defaultCacheStrategy->gcProbability

               );
    }
}
/**
 * Test for net\stubbles\cache\CacheBindingModule.
 *
 * @group  cache
 */
class CacheBindingModuleTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  CacheBindingModule
     */
    protected $cacheBindingModule;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->cacheBindingModule = new CacheBindingModule(__DIR__);
    }

    /**
     * configures the bindings
     *
     * @return  Injector
     */
    private function configure()
    {
        $binder = new Binder();
        $this->cacheBindingModule->configure($binder);
        return $binder->getInjector();
    }

    /**
     * @test
     */
    public function cachePathBoundIfGiven()
    {
        $injector = $this->configure();
        $this->assertTrue($injector->hasConstant('net.stubbles.cache.path'));
        $this->assertEquals(__DIR__,
                            $injector->getConstant('net.stubbles.cache.path')
        );
    }

    /**
     * @test
     */
    public function cachePathNotBoundIfNotGiven()
    {
        $this->cacheBindingModule = new CacheBindingModule();
        $this->assertFalse($this->configure()
                                ->hasConstant('net.stubbles.cache.path')
        );
    }

    /**
     * @test
     */
    public function addsDefaultBindingForCacheStrategy()
    {
        $injector = $this->configure();
        $this->assertTrue($injector->hasBinding('net\\stubbles\\cache\\CacheStrategy'));
        $cacheStrategy = $injector->getInstance('net\\stubbles\\cache\\CacheStrategy');
        $this->assertInstanceOf('net\\stubbles\\cache\\DefaultCacheStrategy', $cacheStrategy);
        $this->assertEquals(array('ttl'           => 86400,
                                  'maxSize'       => -1,
                                  'gcProbability' => 10

                            ),
                            DefaultCacheStrategyPropertyAccessor::getProperties($cacheStrategy)
        );
    }

    /**
     * @test
     */
    public function addsDefaultBindingForCacheStrategyWithChangedSettings()
    {
        $this->assertSame($this->cacheBindingModule,
                          $this->cacheBindingModule->setDefaultStrategyValues(100, 100, 0)
        );
        $this->assertEquals(array('ttl'           => 100,
                                  'maxSize'       => 100,
                                  'gcProbability' => 0

                            ),
                            DefaultCacheStrategyPropertyAccessor::getProperties($this->configure()->getInstance('net\\stubbles\\cache\\CacheStrategy'))
        );
    }

    /**
     * @test
     */
    public function addsBindingWithCacheStrategySet()
    {
        $mockCacheStrategy = $this->getMock('net\\stubbles\\cache\\CacheStrategy');
        $this->assertSame($this->cacheBindingModule,
                          $this->cacheBindingModule->setCacheStrategy($mockCacheStrategy)
        );
        $injector = $this->configure();
        $this->assertTrue($injector->hasBinding('net\\stubbles\\cache\\CacheStrategy'));
        $this->assertSame($mockCacheStrategy,
                          $injector->getInstance('net\\stubbles\\cache\\CacheStrategy')
        );
    }

    /**
     * @test
     */
    public function bindsCacheContainerToCacheContainerProviderByDefault()
    {
        $injector = $this->configure();
        $this->assertTrue($injector->hasBinding('net\\stubbles\\cache\\CacheContainer'));
    }

    /**
     * @test
     */
    public function bindsCacheContainerToGivenCacheContainerProviderClass()
    {
        DifferentCacheContainerProvider::$cacheContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $this->cacheBindingModule = CacheBindingModule::create(__DIR__,
                                                               'net\\stubbles\\cache\\DifferentCacheContainerProvider'
                                    );
        $injector = $this->configure();
        $this->assertTrue($injector->hasBinding('net\\stubbles\\cache\\CacheContainer'));
        $this->assertSame(DifferentCacheContainerProvider::$cacheContainer,
                          $injector->getInstance('net\\stubbles\\cache\\CacheContainer')
        );

    }
}
?>