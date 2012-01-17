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
/**
 * Helper implementation for the test.
 */
class TestAbstractCacheContainer extends AbstractCacheContainer
{
    public function setStrategy(CacheStrategy $cacheStrategy)
    {
        $this->strategy = $cacheStrategy;
    }

    public $data = array();

    protected function doPut($key, $data)
    {
        $this->data[$key] = array('data' => $data, 'time' => time());
        return strlen($data);
    }

    protected function doHas($key)
    {
        return isset($this->data[$key]['data']);
    }

    protected function doGet($key)
    {
        if ($this->doHas($key) == true) {
            return $this->data[$key]['data'];
        }

        return null;
    }

    public function getLifeTime($key)
    {
        if ($this->doHas($key) == true) {
            return (time() - $this->data[$key]['time']);
        }

        return 0;
    }

    public function getStoreTime($key)
    {
        if ($this->doHas($key) == true) {
            return $this->data[$key]['time'];
        }

        return 0;
    }

    protected function doGetSize($key)
    {
        return strlen($this->data[$key]['data']);
    }

    public function getUsedSpace()
    {
        $size = 0;
        foreach ($this->data as $data) {
            $size += strlen($data['data']);
        }

        return $size;
    }

    public function getKeys()
    {
        return array_keys($this->data);
    }

    protected function doGc()
    {
        foreach ($this->data as $key => $data) {
            if ($this->strategy->isExpired($this, $key) == true) {
                unset($this->data[$key]);
            }
        }
    }
}
/**
 * Tests for net\stubbles\cache\AbstractCacheContainer.
 *
 * @group  cache
 */
class AbstractCacheContainerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  TestAbstractCacheContainer
     */
    protected $cacheContainer;
    /**
     * a mocked cache strategy
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStrategy;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockCacheStrategy = $this->getMock('net\\stubbles\\cache\\CacheStrategy');
        $this->cacheContainer    = new TestAbstractCacheContainer();
        $this->cacheContainer->setStrategy($this->mockCacheStrategy);
    }

    /**
     * @test
     */
    public function returnsAmountOfStoredData()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isCachable')
                                ->will($this->returnValue(true));
        $this->assertEquals(3, $this->cacheContainer->put('foo', 'bar'));
    }

    /**
     * @test
     */
    public function returnsFalseIfValueCanNotBeStored()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isCachable')
                                ->will($this->returnValue(false));
        $this->assertEquals(false, $this->cacheContainer->put('baz', 'bar'));
    }

    /**
     * @test
     */
    public function hasReturnsTrueForNonExpiredValue()
    {
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertTrue($this->cacheContainer->has('foo'));
    }

    /**
     * @test
     */
    public function hasReturnsFalseForExpiredValue()
    {
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->assertFalse($this->cacheContainer->has('foo'));
    }

    /**
     * @test
     */
    public function getReturnsNonExpiredValue()
    {
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertEquals('bar', $this->cacheContainer->get('foo'));
    }

    /**
     * @test
     */
    public function getReturnsNullForExpiredValue()
    {
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->assertNull($this->cacheContainer->get('foo'));
    }

    /**
     * @test
     */
    public function getSizeReturnsSizeOfStoredNonExpiredValue()
    {
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertEquals(3, $this->cacheContainer->getSize('foo'));
    }

    /**
     * @test
     */
    public function getSizeReturnsNullForStoredButExpiredValue()
    {
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->assertEquals(0, $this->cacheContainer->getSize('foo'));
    }

    /**
     * @test
     */
    public function lastGcRunIsInitiallyNull()
    {
        $this->assertNull($this->cacheContainer->lastGcRun());
    }

    /**
     * @test
     */
    public function gcDoesNotRunIfStrategyDenies()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('shouldRunGc')
                                ->will($this->returnValue(false));
        $this->assertNull($this->cacheContainer->gc()->lastGcRun());
    }

    /**
     * @test
     */
    public function gcDoesRunIfStrategyAllows()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('shouldRunGc')
                                ->will($this->returnValue(true));
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->cacheContainer->data = array('foo' => array('data' => 'bar', 'time' => 10));
        $this->assertNotNull($this->cacheContainer->gc()->lastGcRun());
    }
}
?>