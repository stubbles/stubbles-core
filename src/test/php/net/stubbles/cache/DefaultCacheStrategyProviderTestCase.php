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
use net\stubbles\lang\reflect\ReflectionClass;
/**
 * Helper class for the test.
 */
class DefaultCacheStrategyAccessor extends DefaultCacheStrategy
{
    /**
     * returns time to live of given instance
     *
     * @param   DefaultCacheStrategy  $s
     * @return  int
     */
    public function getTtl(DefaultCacheStrategy $instance)
    {
        return $instance->timeToLive;
    }

    /**
     * returns max cache size of given instance
     *
     * @param   DefaultCacheStrategy  $s
     * @return  int
     */
    public function getMaxCacheSize(DefaultCacheStrategy $instance)
    {
        return $instance->maxSize;
    }

    /**
     * returns gc probability of given instance
     *
     * @param   DefaultCacheStrategy  $s
     * @return  int
     */
    public function getGcProbability(DefaultCacheStrategy $instance)
    {
        return $instance->gcProbability;
    }
}
/**
 * Test for net\stubbles\cache\DefaultCacheStrategyProvider.
 *
 * @group  cache
 */
class DefaultCacheStrategyProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  DefaultCacheStrategyProvider
     */
    protected $defaultCacheStrategyProvider;
    /**
     * accessor for strategy values
     *
     * @type  DefaultCacheStrategyAccessor
     */
    protected $accessor;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->defaultCacheStrategyProvider = new DefaultCacheStrategyProvider();
        $this->accessor                     = new DefaultCacheStrategyAccessor();
    }

    /**
     * @test
     */
    public function isDefaultImplementationForCacheStrategyProvider()
    {
        $refClass = new ReflectionClass('net\\stubbles\\cache\\CacheStrategyProvider');
        $this->assertTrue($refClass->hasAnnotation('ImplementedBy'));
        $this->assertEquals($this->defaultCacheStrategyProvider->getClassName(),
                            $refClass->getAnnotation('ImplementedBy')
                                     ->getDefaultImplementation()
                                     ->getName()
        );
    }

    /**
     * @test
     */
    public function createWithDefaultValues()
    {
        $defaultStrategy = $this->defaultCacheStrategyProvider->get();
        $this->assertEquals(86400, $this->accessor->getTtl($defaultStrategy));
        $this->assertEquals(-1, $this->accessor->getMaxCacheSize($defaultStrategy));
        $this->assertEquals(10, $this->accessor->getGcProbability($defaultStrategy));
    }

    /**
     * @test
     */
    public function createWithDefaultValuesIfNamedValuesNotSet()
    {
        $defaultStrategy = $this->defaultCacheStrategyProvider->get('notSet');
        $this->assertEquals(86400, $this->accessor->getTtl($defaultStrategy));
        $this->assertEquals(-1, $this->accessor->getMaxCacheSize($defaultStrategy));
        $this->assertEquals(10, $this->accessor->getGcProbability($defaultStrategy));
    }

    /**
     * @test
     */
    public function createWithTtlValuesSet()
    {
        $defaultStrategy = $this->defaultCacheStrategyProvider->setDefaultStrategyValues('foo', 500)
                                                              ->get('foo');
        $this->assertEquals(500, $this->accessor->getTtl($defaultStrategy));
        $this->assertEquals(-1, $this->accessor->getMaxCacheSize($defaultStrategy));
        $this->assertEquals(10, $this->accessor->getGcProbability($defaultStrategy));
    }

    /**
     * @test
     */
    public function createWithAllValuesButGcProbabilitySet()
    {
        $defaultStrategy = $this->defaultCacheStrategyProvider->setDefaultStrategyValues('foo', 500, 20)
                                                              ->get('foo');
        $this->assertEquals(500, $this->accessor->getTtl($defaultStrategy));
        $this->assertEquals(20, $this->accessor->getMaxCacheSize($defaultStrategy));
        $this->assertEquals(10, $this->accessor->getGcProbability($defaultStrategy));
    }

    /**
     * @test
     */
    public function createWithAllValuesSet()
    {
        $defaultStrategy = $this->defaultCacheStrategyProvider->setDefaultStrategyValues('foo', 500, 10, 0)
                                                              ->get('foo');
        $this->assertEquals(500, $this->accessor->getTtl($defaultStrategy));
        $this->assertEquals(10, $this->accessor->getMaxCacheSize($defaultStrategy));
        $this->assertEquals(0, $this->accessor->getGcProbability($defaultStrategy));
    }
}
?>