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
 * Tests for net\stubbles\cache\DefaultCacheStrategy.
 *
 * @group  cache
 */
class DefaultCacheStrategyTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  DefaultCacheStrategy
     */
    protected $defaultCacheStrategy;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->defaultCacheStrategy = new DefaultCacheStrategy();
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function annotationsPresentForSetTimeToLiveMethod()
    {
        $class = $this->defaultCacheStrategy->getClass();
        $setTimeToLiveMethod = $class->getMethod('setTimeToLive');
        $this->assertTrue($setTimeToLiveMethod->hasAnnotation('Inject'));
        $this->assertTrue($setTimeToLiveMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setTimeToLiveMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.util.cache.timeToLive',
                            $setTimeToLiveMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function annotationsPresentForSetMaxCacheSizeMethod()
    {
        $class = $this->defaultCacheStrategy->getClass();
        $setMaxCacheSizeMethod = $class->getMethod('setMaxCacheSize');
        $this->assertTrue($setMaxCacheSizeMethod->hasAnnotation('Inject'));
        $this->assertTrue($setMaxCacheSizeMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setMaxCacheSizeMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.util.cache.maxSize',
                            $setMaxCacheSizeMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function annotationsPresentForSetGcProbabilityMethod()
    {
        $class = $this->defaultCacheStrategy->getClass();
        $setGcProbabilityMethod = $class->getMethod('setGcProbability');
        $this->assertTrue($setGcProbabilityMethod->hasAnnotation('Inject'));
        $this->assertTrue($setGcProbabilityMethod->getAnnotation('Inject')->isOptional());
        $this->assertTrue($setGcProbabilityMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.util.cache.gcProbability',
                            $setGcProbabilityMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function isDefaultImplementation()
    {
        $refClass = new ReflectionClass('net\\stubbles\\cache\\CacheStrategy');
        $this->assertTrue($refClass->hasAnnotation('ImplementedBy'));
        $this->assertEquals($this->defaultCacheStrategy->getClassName(),
                            $refClass->getAnnotation('ImplementedBy')
                                     ->getDefaultImplementation()
                                     ->getName()
        );
    }

    /**
     * @test
     */
    public function isCachableIfValueFitsIntoCacheSpace()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $mockContainer->expects($this->once())
                      ->method('getUsedSpace')
                      ->will($this->returnValue(0));
        $mockContainer->expects($this->once())
                      ->method('getSize')
                      ->will($this->returnValue(1));
        $this->assertTrue($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'a'));
    }

    /**
     * @test
     */
    public function isCachableIfValueDoesExactlyFitIntoCacheSpace()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $mockContainer->expects($this->once())
                      ->method('getUsedSpace')
                      ->will($this->returnValue(0));
        $mockContainer->expects($this->once())
                      ->method('getSize')
                      ->will($this->returnValue(0));
        $this->assertTrue($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'ab'));
    }

    /**
     * @test
     */
    public function isCachableWhenCacheIsFullIfValueReplacesExistingValue()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $mockContainer->expects($this->once())
                      ->method('getUsedSpace')
                      ->will($this->returnValue(2));
        $mockContainer->expects($this->once())
                      ->method('getSize')
                      ->will($this->returnValue(2));
        $this->assertTrue($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'ab'));
    }

    /**
     * @test
     */
    public function isCachableWhenCacheSizeIsNotLimited()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(-1)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $this->assertTrue($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'ab'));
    }

    /**
     * @test
     */
    public function isNotCachableIfValueExceedsCacheSpace()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(1)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $mockContainer->expects($this->once())
                      ->method('getUsedSpace')
                      ->will($this->returnValue(0));
        $mockContainer->expects($this->once())
                      ->method('getSize')
                      ->will($this->returnValue(0));
        $this->assertFalse($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'ab'));
    }

    /**
     * @test
     */
    public function isNotCachableIfValueDoesNotFitIntoRemainingCacheSpace()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $mockContainer->expects($this->once())
                      ->method('getUsedSpace')
                      ->will($this->returnValue(1));
        $mockContainer->expects($this->once())
                      ->method('getSize')
                      ->will($this->returnValue(0));
        $this->assertFalse($this->defaultCacheStrategy->isCachable($mockContainer, 'a', 'ab'));
    }

    /**
     * @test
     */
    public function isExpiredReturnsFalseIfTimeToLiveNotReached()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $mockContainer->expects($this->once())
                      ->method('getLifeTime')
                      ->will($this->returnValue(9));
        $this->assertFalse($this->defaultCacheStrategy->isExpired($mockContainer, 'a'));
    }

    /**
     * @test
     */
    public function isExpiredReturnsFalseIfTimeToLiveExactlyReached()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $mockContainer->expects($this->once())
                      ->method('getLifeTime')
                      ->will($this->returnValue(10));
        $this->assertFalse($this->defaultCacheStrategy->isExpired($mockContainer, 'a'));
    }

    /**
     * @test
     */
    public function isExpiredReturnsTrueIfTimeToLiveReached()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $mockContainer->expects($this->once())
                      ->method('getLifeTime')
                      ->will($this->returnValue(11));
        $this->assertTrue($this->defaultCacheStrategy->isExpired($mockContainer, 'a'));
    }

    /**
     * @test
     */
    public function neverRunsGcWhenProbabilityIsZero()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(0);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $this->assertFalse($this->defaultCacheStrategy->shouldRunGc($mockContainer));
    }

    /**
     * @test
     */
    public function alswaysRunsGcWhenProbabilityIs100()
    {
        $this->defaultCacheStrategy->setTimeToLive(10)
                                   ->setMaxCacheSize(2)
                                   ->setGcProbability(100);
        $mockContainer = $this->getMock('net\\stubbles\\cache\\CacheContainer');
        $this->assertTrue($this->defaultCacheStrategy->shouldRunGc($mockContainer));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     * @since              1.1.0
     */
    public function setTimeToLiveWithNegativeValueThrowsInvalidArgumentException()
    {
        $this->defaultCacheStrategy->setTimeToLive(-1);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     * @since              1.1.0
     */
    public function setGcProbabilityWithValueLowerThan0ThrowsInvalidArgumentException()
    {
        $this->defaultCacheStrategy->setGcProbability(-1);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     * @since              1.1.0
     */
    public function setGcProbabilityWithValueGreaterThan100ThrowsInvalidArgumentException()
    {
        $this->defaultCacheStrategy->setGcProbability(101);
    }
}
?>