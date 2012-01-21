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
 * Tests for net\stubbles\cache\FileCacheStorage.
 *
 * @group  cache
 */
class FileCacheStorageTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FileCacheStorage
     */
    protected $fileCacheStorage;
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
        $this->fileCacheStorage = new FileCacheStorage(vfsStream::url('cache/id'));
        $this->cacheDirectory   = $root->getChild('id');
        vfsStream::newDirectory('ignore_on_calculations_of_size_and_keys')
                 ->at($this->cacheDirectory);
    }

    /**
     * @test
     */
    public function createsDirectoryWithDefaultFileModeIfNotExists()
    {
        $root = vfsStream::setup('cache');
        $fileCacheStorage = new FileCacheStorage(vfsStream::url('cache/id'));
        $this->assertTrue($root->hasChild('id'));
        $this->assertEquals(0700, $root->getChild('id')->getPermissions());
    }

    /**
     * @test
     */
    public function createsDirectoryWithGivenFileModeIfNotExists()
    {
        $root = vfsStream::setup('cache');
        $fileCacheStorage = new FileCacheStorage(vfsStream::url('cache/id'), 0755);
        $this->assertTrue($root->hasChild('id'));
        $this->assertEquals(0755, $root->getChild('id')->getPermissions());
    }

    /**
     * @test
     */
    public function putStoresDataInDirectory()
    {
        $this->assertEquals(3, $this->fileCacheStorage->put('foo', 'bar'));
        $this->assertTrue($this->cacheDirectory->hasChild('foo.cache'));
        $this->assertEquals('bar',
                            $this->cacheDirectory->getChild('foo.cache')
                                                 ->getContent()
        );
    }

    /**
     * @test
     */
    public function failureToStoreDataInDirectoryReturnsFalse()
    {
        $root = vfsStream::setup('cache');
        $fileCacheStorage = new FileCacheStorage(vfsStream::url('cache/id'), 0000);
        $this->assertFalse($fileCacheStorage->put('foo', 'bar'));
    }

    /**
     * @test
     */
    public function hasReturnsTrueIfCacheFileExists()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->assertTrue($this->fileCacheStorage->has('foo'));
    }

    /**
     * @test
     */
    public function hasReturnsFalseIfCacheDoesNotExist()
    {
        $this->assertFalse($this->fileCacheStorage->has('foo'));
    }

    /**
     * @test
     */
    public function getReturnsValueIfCacheFileExists()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->assertEquals('bar', $this->fileCacheStorage->get('foo'));
    }

    /**
     * @test
     */
    public function getReturnsNullIfCacheFileDoesNotExist()
    {
        $this->assertNull($this->fileCacheStorage->get('foo'));
    }

    /**
     * @test
     */
    public function removeDeletesCacheFile()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->fileCacheStorage->remove('foo');
        $this->assertFalse($this->cacheDirectory->hasChild('foo.cache'));
    }

    /**
     * @test
     */
    public function lifetimeForNonCachedKeyIsZero()
    {
        $this->assertEquals(0, $this->fileCacheStorage->getLifeTime('doesNotExist'));
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
        $this->assertGreaterThanOrEqual(500, $this->fileCacheStorage->getLifeTime('foo'));
    }

    /**
     * @test
     */
    public function getSizeReturnsSizeIfCacheFileExists()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->assertEquals(3, $this->fileCacheStorage->getSize('foo'));
    }

    /**
     * @test
     */
    public function getSizeReturnsZeroIfCacheFileDoesNotExist()
    {
        $this->assertEquals(0, $this->fileCacheStorage->getSize('foo'));
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
        $this->assertEquals(6, $this->fileCacheStorage->getUsedSpace());
    }

    /**
     * @test
     */
    public function cachesUsedSpaceDoesNotConsiderDirectDirectoryAccess()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('bar.cache')
                                                 ->withContent('bar')
        );
        $this->assertEquals(6, $this->fileCacheStorage->getUsedSpace());
        $this->cacheDirectory->addChild(vfsStream::newFile('baz.cache')
                                                 ->withContent('bar')
        );
        $this->assertEquals(6, $this->fileCacheStorage->getUsedSpace());
    }

    /**
     * @test
     */
    public function cachesUsedSpaceConsidersAddedEntries()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('bar.cache')
                                                 ->withContent('bar')
        );
        $this->assertEquals(6, $this->fileCacheStorage->getUsedSpace());
        $this->fileCacheStorage->put('other', 'other');
        $this->assertEquals(11,
                            $this->fileCacheStorage->getUsedSpace()
        );
    }

    /**
     * @test
     */
    public function getKeysReturnsKeysOfAllEntries()
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
        $this->assertEquals(array('foo' => 'foo',
                                  'bar' => 'bar',
                                  'baz' => 'baz'
                            ),
                            $this->fileCacheStorage->getKeys()
        );
    }

    /**
     * @test
     */
    public function cachesKeyDataInternallyDoesNotConsiderDirectDirectoryAccess()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('bar.cache')
                                                 ->withContent('bar')
        );
        $this->assertEquals(array('foo' => 'foo',
                                  'bar' => 'bar'
                            ),
                            $this->fileCacheStorage->getKeys()
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('baz.cache')
                                                 ->withContent('baz')
        );
        $this->assertEquals(array('foo' => 'foo',
                                  'bar' => 'bar'
                            ),
                            $this->fileCacheStorage->getKeys()
        );
    }

    /**
     * @test
     */
    public function cachesKeyDataConsidersAddedEntries()
    {
        $this->cacheDirectory->addChild(vfsStream::newFile('foo.cache')
                                                 ->withContent('bar')
        );
        $this->cacheDirectory->addChild(vfsStream::newFile('bar.cache')
                                                 ->withContent('bar')
        );
        $this->assertEquals(array('foo' => 'foo',
                                  'bar' => 'bar'
                            ),
                            $this->fileCacheStorage->getKeys()
        );
        $this->fileCacheStorage->put('other', 'other');
        $this->assertEquals(array('foo'   => 'foo',
                                  'bar'   => 'bar',
                                  'other' => 'other'
                            ),
                            $this->fileCacheStorage->getKeys()
        );
    }
}
?>