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
use org\bovigo\vfs\vfsStream;
/**
 * Tests for net\stubbles\cache\FileCacheContainer.
 *
 * @group  cache
 */
class FileCacheContainerTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FileCacheContainer
     */
    protected $cacheContainer;
    /**
     * a mocked cache strategy
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStrategy;
    /**
     * the path to the cache files
     *
     * @type  vfsStreamDirectory
     */
    protected $cacheDirectory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $root = vfsStream::setup('cache');
        $this->mockCacheStrategy = $this->getMock('net\\stubbles\\cache\\CacheStrategy');
        $this->cacheContainer    = new FileCacheContainer($this->mockCacheStrategy,
                                                          vfsStream::url('cache/id')
                                   );
        $this->cacheDirectory    = $root->getChild('id');
        vfsStream::newDirectory('ignore_on_calculations_of_size_and_keys')
                 ->at($this->cacheDirectory);
    }

    /**
     * @test
     */
    public function putStoresDataInDirectory()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isCachable')
                                ->will($this->returnValue(true));
        $this->assertEquals(3, $this->cacheContainer->put('foo', 'bar'));
        $this->assertTrue($this->cacheDirectory->hasChild('foo.cache'));
        $this->assertEquals('bar',
                            $this->cacheDirectory->getChild('foo.cache')
                                                 ->getContent()
        );
    }

    /**
     * @test
     */
    public function putDoesNotWriteIntoDirectoryIfNotCachable()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isCachable')
                                ->will($this->returnValue(false));
        $this->assertFalse($this->cacheContainer->put('baz', 'bar'));
        $this->assertFalse($this->cacheDirectory->hasChild('baz.cache'));
    }

    /**
     * @test
     */
    public function hasReturnsTrueIfCacheFileExistsAndIsNotExpired()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertTrue($this->cacheContainer->has('foo'));
    }

    /**
     * @test
     */
    public function hasReturnsFalseIfCacheFileExistsAndIsExpired()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->assertFalse($this->cacheContainer->has('foo'));
    }

    /**
     * @test
     */
    public function hasReturnsFalseIfCacheFileDoesNotExist()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertFalse($this->cacheContainer->has('foo'));
    }

    /**
     * @test
     */
    public function getReturnsValueIfCacheFileExistsAndIsNotExpired()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertEquals('bar', $this->cacheContainer->get('foo'));
    }

    /**
     * @test
     */
    public function getReturnsNullIfCacheFileExistsAndIsExpired()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->assertNull($this->cacheContainer->get('foo'));
    }

    /**
     * @test
     */
    public function getReturnsNullIfCacheFileDoesNotExist()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertNull($this->cacheContainer->get('foo'));
    }

    /**
     * @test
     */
    public function getSizeReturnsSizeIfCacheFileExistsAndIsNotExpired()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertEquals(3, $this->cacheContainer->getSize('foo'));
    }

    /**
     * @test
     */
    public function getSizeReturnsZeroIfCacheFileExistsAndIsExpired()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(true));
        $this->assertEquals(0, $this->cacheContainer->getSize('foo'));
    }

    /**
     * @test
     */
    public function getSizeReturnsZeroIfCacheFileDoesNotExist()
    {
        $this->mockCacheStrategy->expects($this->once())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertEquals(0, $this->cacheContainer->getSize('foo'));
    }

    /**
     * @test
     */
    public function getUsedSpaceReturnsSumOfAllEntrySizes()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('bar.cache')
                                                 ->withContent('bar')
        );
        $this->mockCacheStrategy->expects($this->any())
                                ->method('isCachable')
                                ->will($this->returnValue(true));
        $this->assertEquals(6, $this->cacheContainer->getUsedSpace());
    }

    /**
     * @test
     */
    public function getKeysReturnsKeysOfAllNonExpiredEntries()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('bar.cache')
                                                 ->withContent('bar')
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('baz.cache')
                                                 ->withContent('bar')
        );
        $this->mockCacheStrategy->expects($this->exactly(3))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, true, false));
        $this->assertEquals(array('foo' => 'foo', 'baz' => 'baz'), $this->cacheContainer->getKeys());
    }

    /**
     * @test
     */
    public function keysAreCached()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('baz.cache')
                                                 ->withContent('bar')
        );
        $this->mockCacheStrategy->expects($this->any())
                                ->method('isExpired')
                                ->will($this->returnValue(false));
        $this->assertEquals(array('foo' => 'foo', 'baz' => 'baz'), $this->cacheContainer->getKeys());
        $this->cacheDirectory->addChild(vfsStream::newFile('other.cache')
                                                 ->withContent('more')
        );
        $this->assertEquals(array('foo' => 'foo', 'baz' => 'baz'), $this->cacheContainer->getKeys());
    }

    /**
     * @test
     */
    public function gcRemovesFilesForExpiredEntries()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('bar.cache')
                                                 ->withContent('baz')
        );
        $this->mockCacheStrategy->expects($this->any())
                                ->method('shouldRunGc')
                                ->will($this->returnValue(true));
        $this->mockCacheStrategy->expects($this->exactly(2))
                                ->method('isExpired')
                                ->will($this->onConsecutiveCalls(false, true));
        $this->assertSame($this->cacheContainer, $this->cacheContainer->gc());
        $this->assertTrue($this->cacheDirectory->hasChild('foo.cache'));
        $this->assertFalse($this->cacheDirectory->hasChild('bar.cache'));

    }

    /**
     * @test
     */
    public function keyContainingDirectorySeperatorAndExistingCacheFileShouldWork()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('barfoo.cache')
                                                 ->withContent('bar')
        );
        $this->assertTrue($this->cacheContainer->has('bar' . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertEquals('bar', $this->cacheContainer->get('bar' . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertEquals(array('barfoo' => 'barfoo'), $this->cacheContainer->getKeys());
        $this->assertEquals(3, $this->cacheContainer->getSize('bar' . DIRECTORY_SEPARATOR . 'foo'));
        $this->assertEquals(3, $this->cacheContainer->getUsedSpace());
    }

    /**
     * @test
     */
    public function lifetimeForNonCachedKeyIsZero()
    {
        $this->assertEquals(0, $this->cacheContainer->getLifeTime('doesNotExist'));
    }

    /**
     * @test
     */
    public function lifetimeForCachedKeyIsTimeSinceCreationInSeconds()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
                                                 ->lastModified(time() - 500)
        );
        $this->assertGreaterThanOrEqual(500, $this->cacheContainer->getLifeTime('foo'));
    }

    /**
     * @test
     */
    public function storetimeForNonCachedKeyIsZero()
    {
        $this->assertEquals(0, $this->cacheContainer->getStoreTime('doesNotExist'));
    }

    /**
     * @test
     */
    public function storetimeForCachedKeyIsCreationTime()
    {
        $stored = time() - 500;
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
                                                 ->lastModified($stored)
        );
        $this->assertEquals($stored, $this->cacheContainer->getStoreTime('foo'));
    }
}
?>