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
 * Test for net\stubbles\cache\CacheProvider.
 *
 * @group  cache
 */
class CacheProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  CacheProvider
     */
    protected $cacheProvider;
    /**
     * mocked cache strategy provider
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStrategyProvider;
    /**
     * mocked cache storage provider
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStorageProvider;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->mockCacheStrategyProvider = $this->getMock('net\\stubbles\\cache\\CacheStrategyProvider');
        $this->mockCacheStorageProvider  = $this->getMock('net\\stubbles\\cache\\CacheStorageProvider');
        $this->cacheProvider             = new CacheProvider($this->mockCacheStrategyProvider, $this->mockCacheStorageProvider);
    }

    /**
     * @test
     */
    public function annotationPresentOnConstructor()
    {
        $refConstructor = $this->cacheProvider->getClass()->getConstructor();
        $this->assertTrue($refConstructor->hasAnnotation('Inject'));
    }

    /**
     * @test
     */
    public function isDefaultProviderForCache()
    {
        $refClass = new ReflectionClass('net\\stubbles\\cache\\Cache');
        $this->assertTrue($refClass->hasAnnotation('ProvidedBy'));
        $this->assertEquals($this->cacheProvider->getClassName(),
                            $refClass->getAnnotation('ProvidedBy')
                                     ->getProviderClass()
                                     ->getName()
        );
    }

    /**
     * @test
     */
    public function createsCacheUsingBothProviders()
    {
        $this->mockCacheStrategyProvider->expects($this->any())
                                        ->method('get')
                                        ->with($this->equalTo(null))
                                        ->will($this->returnValue($this->getMock('net\\stubbles\\cache\\CacheStrategy')));
        $this->mockCacheStorageProvider->expects($this->any())
                                       ->method('get')
                                       ->with($this->equalTo(null))
                                       ->will($this->returnValue($this->getMock('net\\stubbles\\cache\\CacheStorage')));
        $this->assertInstanceOf('net\\stubbles\\cache\\Cache', $this->cacheProvider->get());
    }

    /**
     * @test
     */
    public function createsCacheUsingGivenName()
    {
        $this->mockCacheStrategyProvider->expects($this->any())
                                        ->method('get')
                                        ->with($this->equalTo('foo'))
                                        ->will($this->returnValue($this->getMock('net\\stubbles\\cache\\CacheStrategy')));
        $this->mockCacheStorageProvider->expects($this->any())
                                       ->method('get')
                                       ->with($this->equalTo('foo'))
                                       ->will($this->returnValue($this->getMock('net\\stubbles\\cache\\CacheStorage')));
        $this->assertInstanceOf('net\\stubbles\\cache\\Cache', $this->cacheProvider->get('foo'));
    }
}
?>