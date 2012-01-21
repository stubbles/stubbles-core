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
class CacheTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  Cache
     */
    protected $cache;
    /**
     * mocked cache strategy
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStrategy;
    /**
     * mocked cache storage
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStorage;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockCacheStrategy = $this->getMock('net\\stubbles\\cache\\CacheStrategy');
        $this->mockCacheStorage  = $this->getMock('net\\stubbles\\cache\\CacheStorage');
        $this->cache             = new Cache($this->mockCacheStrategy, $this->mockCacheStorage);
    }

    /**
     * @test
     */
    public function putReturnsAmountOfStoredData()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isCachable')
                                ->will($this->returnValue(true));
        $this->mockCacheStorage->expects($this->once())
                               ->method('put')
                               ->will($this->returnValue(3));
        $this->assertEquals(3, $this->cache->put('foo', 'bar'));
    }

    /**
     * @test
     */
    public function putReturnsFalseIfValueIsNotCachable()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isCachable')
                                ->will($this->returnValue(false));
        $this->mockCacheStorage->expects($this->never())
                               ->method('put');
        $this->assertFalse($this->cache->put('baz', 'bar'));
    }

    /**
     * @test
     */
    public function hasReturnsFalseIfValueIsExpired()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->mockCacheStorage->expects($this->never())
                               ->method('has');
        $this->assertFalse($this->cache->has('baz'));
    }

    /**
     * @test
     */
    public function hasReturnsValueFromStorageCheckIfValueIsNotExpired()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->mockCacheStorage->expects($this->once())
                               ->method('has')
                               ->will($this->returnValue(true));
        $this->assertTrue($this->cache->has('baz'));
    }

    /**
     * @test
     */
    public function getReturnsNullIfValueIsExpired()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->mockCacheStorage->expects($this->never())
                               ->method('get');
        $this->assertNull($this->cache->get('baz'));
    }

    /**
     * @test
     */
    public function getReturnsValueFromStorageIfValueIsNotExpired()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->mockCacheStorage->expects($this->once())
                               ->method('get')
                               ->will($this->returnValue('a value'));
        $this->assertEquals('a value', $this->cache->get('baz'));
    }

    /**
     * @test
     */
    public function getKeysReturnsAllKeysForNonExpiredValues()
    {
        $this->mockCacheStorage->expects($this->once())
                               ->method('getKeys')
                               ->will($this->returnValue(array('foo', 'bar', 'baz')));
        $this->mockCacheStrategy->expects($this->exactly(3))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, true, false));
        $this->assertEquals(array('foo', 'baz'),
                            $this->cache->getKeys()
        );
    }

    /**
     * @test
     */
    public function garbageCollectionIsNotRunIfStrategyDenies()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('shouldRunGc')
                                ->will($this->returnValue(false));
        $this->mockCacheStorage->expects($this->never())
                               ->method('getKeys');
        $this->assertSame($this->cache, $this->cache->gc());
    }

    /**
     * @test
     */
    public function garbageCollectionRemovesAllExpiredValues()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('shouldRunGc')
                                ->will($this->returnValue(true));
        $this->mockCacheStorage->expects($this->once())
                               ->method('getKeys')
                               ->will($this->returnValue(array('foo', 'bar', 'baz')));
        $this->mockCacheStrategy->expects($this->exactly(3))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, true, false));
        $this->mockCacheStorage->expects($this->once())
                               ->method('remove')
                               ->with($this->equalTo('bar'));
        $this->assertSame($this->cache, $this->cache->gc());
    }
}
?>