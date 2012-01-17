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
     * mocked cache strategy
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCacheStrategy;
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
        $this->root = vfsStream::setup('cache');
        $this->mockCacheStrategy = $this->getMock('net\\stubbles\\cache\\CacheStrategy');
        $this->cacheProvider     = new CacheProvider($this->mockCacheStrategy, vfsStream::url('cache'));
    }

    /**
     * @test
     */
    public function annotationPresentOnConstructor()
    {
        $refConstructor = $this->cacheProvider->getClass()->getConstructor();
        $this->assertTrue($refConstructor->hasAnnotation('Inject'));

        $refParams = $refConstructor->getParameters();
        $this->assertTrue($refParams[1]->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.cache.path',
                            $refParams[1]->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function annotationPresentOnSetFileModeMethod()
    {
        $refMethod = $this->cacheProvider->getClass()->getMethod('setFileMode');
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
    public function isDefaultProviderForCacheContainer()
    {
        $refClass = new ReflectionClass('net\\stubbles\\cache\\CacheContainer');
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
    public function fileMode()
    {
        $this->assertSame($this->cacheProvider, $this->cacheProvider->setFileMode(0660));
    }

    /**
     * @test
     */
    public function namedCacheContainerIsAlwaysSameInstance()
    {
        $this->mockCacheStrategy->expects($this->any())
                                ->method('shouldRunGc')
                                ->will($this->returnValue(false));
        $namedCacheContainer = $this->cacheProvider->get('websites');
        $this->assertInstanceOf('net\\stubbles\\cache\\FileCacheContainer',
                                $namedCacheContainer
        );
        $this->assertTrue($this->root->hasChild('websites'));
        $this->assertSame($namedCacheContainer, $this->cacheProvider->get('websites'));
    }


    /**
     * @test
     */
    public function unNamedCacheContainerIsAlwaysSameInstance()
    {
        $this->mockCacheStrategy->expects($this->any())
                                ->method('shouldRunGc')
                                ->will($this->returnValue(false));
        $unnamedCacheContainer = $this->cacheProvider->get();
        $this->assertInstanceOf('net\\stubbles\\cache\\FileCacheContainer',
                                $unnamedCacheContainer
        );
        $this->assertFalse($this->root->hasChild(CacheProvider::DEFAULT_NAME));
        $this->assertSame($unnamedCacheContainer, $this->cacheProvider->get());
        $this->assertSame($unnamedCacheContainer, $this->cacheProvider->get(CacheProvider::DEFAULT_NAME));
    }
}
?>