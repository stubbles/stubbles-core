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
use org\bovigo\vfs\vfsStream;
/**
 * Test for net\stubbles\cache\FileCacheStorageProvider.
 *
 * @group  cache
 */
class FileCacheStorageProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FileCacheStorageProvider
     */
    protected $fileCacheStorageProvider;
    /**
     * access to cache directory
     *
     * @type  vfsStreamDirectory
     */
    protected $cacheDirectory;

    /**
     * set up the test environment
     */
    public function setUp()
    {
        $this->root                     = vfsStream::setup('cache');
        $this->fileCacheStorageProvider = new FileCacheStorageProvider(vfsStream::url('cache'));
    }

    /**
     * @test
     */
    public function annotationPresentOnConstructor()
    {
        $refConstructor = $this->fileCacheStorageProvider->getClass()->getConstructor();
        $this->assertTrue($refConstructor->hasAnnotation('Inject'));
        $this->assertTrue($refConstructor->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.cache.path',
                            $refConstructor->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationPresentOnSetFileModeMethod()
    {
        $refMethod = $this->fileCacheStorageProvider->getClass()->getMethod('setFileMode');
        $this->assertTrue($refMethod->hasAnnotation('Inject'));
        $this->assertTrue($refMethod->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.util.cache.filemode',
                            $refMethod->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function isDefaultProviderForFileCacheStorage()
    {
        $refClass = new ReflectionClass('net\\stubbles\\cache\\FileCacheStorage');
        $this->assertTrue($refClass->hasAnnotation('ProvidedBy'));
        $this->assertEquals($this->fileCacheStorageProvider->getClassName(),
                            $refClass->getAnnotation('ProvidedBy')
                                     ->getProviderClass()
                                     ->getName()
        );
    }

    /**
     * @test
     * @since  2.0.0
     */
    public function isDefaultImplementationForCacheStorageProvider()
    {
        $refClass = new ReflectionClass('net\\stubbles\\cache\\CacheStorageProvider');
        $this->assertTrue($refClass->hasAnnotation('ImplementedBy'));
        $this->assertEquals($this->fileCacheStorageProvider->getClassName(),
                            $refClass->getAnnotation('ImplementedBy')
                                     ->getDefaultImplementation()
                                     ->getName()
        );
    }

    /**
     * @test
     */
    public function namedCacheContainerIsAlwaysSameInstance()
    {
        $namedCacheContainer = $this->fileCacheStorageProvider->get('websites');
        $this->assertInstanceOf('net\\stubbles\\cache\\FileCacheStorage',
                                $namedCacheContainer
        );
        $this->assertSame($namedCacheContainer, $this->fileCacheStorageProvider->get('websites'));
    }

    /**
     * @test
     */
    public function defaultFileModeIsUsedForDirectoryToCreate()
    {
        $namedCacheContainer = $this->fileCacheStorageProvider->get('websites');
        $this->assertInstanceOf('net\\stubbles\\cache\\FileCacheStorage',
                                $namedCacheContainer
        );
        $this->assertTrue($this->root->hasChild('websites'));
        $this->assertEquals(0700, $this->root->getChild('websites')->getPermissions());
    }

    /**
     * @test
     */
    public function fileModeIsUsedForDirectoryToCreate()
    {
        $namedCacheContainer = $this->fileCacheStorageProvider->setFileMode(0660)
                                                              ->get('websites');
        $this->assertInstanceOf('net\\stubbles\\cache\\FileCacheStorage',
                                $namedCacheContainer
        );
        $this->assertTrue($this->root->hasChild('websites'));
        $this->assertEquals(0660, $this->root->getChild('websites')->getPermissions());
    }

    /**
     * @test
     */
    public function unNamedCacheContainerIsAlwaysSameInstance()
    {
        $unnamedCacheContainer = $this->fileCacheStorageProvider->get();
        $this->assertInstanceOf('net\\stubbles\\cache\\FileCacheStorage',
                                $unnamedCacheContainer
        );
        $this->assertFalse($this->root->hasChild(FileCacheStorageProvider::DEFAULT_NAME));
        $this->assertSame($this->fileCacheStorageProvider->get(),
                          $this->fileCacheStorageProvider->get(FileCacheStorageProvider::DEFAULT_NAME)
        );
    }
}
?>