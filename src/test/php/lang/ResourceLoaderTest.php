<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang;
/**
 * Tests for stubbles\lang\ResourceLoader.
 *
 * @since  1.6.0
 * @group  lang
 * @group  lang_core
 */
class ResourceLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  ResourceLoader
     */
    private $resourceLoader;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->resourceLoader = new ResourceLoader();
    }

    /**
     * @test
     */
    public function isAnnotatedAsSingleton()
    {
        $this->assertTrue(reflect($this->resourceLoader)->hasAnnotation('Singleton'));
    }

    /**
     * @test
     * @since  3.1.2
     * @deprecated  since 4.0.0, will be removed with 5.0.0
     */
    public function returnsProjectResourceUriForExistingFile()
    {
        $this->assertEquals(ResourceLoader::getRootPath() . DIRECTORY_SEPARATOR
                                    . 'src' . DIRECTORY_SEPARATOR
                                    . 'main' . DIRECTORY_SEPARATOR
                                    . 'resources' . DIRECTORY_SEPARATOR
                                    . 'lang' . DIRECTORY_SEPARATOR . 'stubbles.ini',
                            $this->resourceLoader->getProjectResourceUri('lang/stubbles.ini')
        );
    }

    /**
     * @test
     * @since  3.1.2
     * @deprecated  since 4.0.0, will be removed with 5.0.0
     */
    public function returnsProjectResourceUriForNonExistingFile()
    {
        $this->assertEquals(ResourceLoader::getRootPath() . DIRECTORY_SEPARATOR
                                    . 'src' . DIRECTORY_SEPARATOR
                                    . 'main' . DIRECTORY_SEPARATOR
                                    . 'resources' . DIRECTORY_SEPARATOR
                                    . 'lang' . DIRECTORY_SEPARATOR . 'doesNotExist.ini',
                            $this->resourceLoader->getProjectResourceUri('lang/doesNotExist.ini')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\FileNotFoundException
     * @since  4.0.0
     */
    public function openNonExistingResourceThrowsFileNotFoundException()
    {
        $this->resourceLoader->open('lang/doesNotExist.ini');
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\FileNotFoundException
     * @since  4.0.0
     */
    public function loadNonExistingResourceThrowsFileNotFoundException()
    {
        $this->resourceLoader->load('lang/doesNotExist.ini');
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function openLocalResourceReturnsInputStream()
    {
        $this->assertInstanceOf(
                'stubbles\streams\InputStream',
                $this->resourceLoader->open('lang/stubbles.ini')
        );

    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadLocalResourceReturnsContent()
    {
        $this->assertEquals(
                "[foo]\nbar=\"baz\"\n",
                $this->resourceLoader->load('lang/stubbles.ini')
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function openResourceWithCompletePathInRootReturnsInputStream()
    {
        $this->assertInstanceOf(
                'stubbles\streams\InputStream',
                $this->resourceLoader->open(__FILE__)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadResourceWithCompletePathInRootReturnsContent()
    {
        $this->assertContains(
                'loadResourceWithCompletePathInRootReturnsContent()',
                $this->resourceLoader->load(__FILE__)
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\FileNotFoundException
     * @since  4.0.0
     */
    public function openResourceWithCompletePathOutsideRootThrowsFileNotFoundException()
    {
        $this->resourceLoader->open(tempnam(sys_get_temp_dir(), 'test.txt'));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\FileNotFoundException
     * @since  4.0.0
     */
    public function loadResourceWithCompletePathOutsideRootThrowsFileNotFoundExceptionException()
    {
        $this->resourceLoader->load(tempnam(sys_get_temp_dir(), 'test.txt'));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     * @since  4.0.0
     */
    public function openResourceWithCompleteRealpathOutsideRootThrowsIllegalArgumentException()
    {
        $resourceLoader = new ResourceLoader(__DIR__ . '/exception');
        $resourceLoader->open(__DIR__ . '/exception/../ResourceLoaderTest.php');
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     * @since  4.0.0
     */
    public function loadResourceWithCompleteRealpathOutsideRootThrowsIllegalArgumentException()
    {
        $resourceLoader = new ResourceLoader(__DIR__ . '/exception');
        $resourceLoader->load(__DIR__ . '/exception/../ResourceLoaderTest.php');
    }

    /**
     * @test
     */
    public function returnsListOfAllResourceUrisForExistingFile()
    {
        $this->assertEquals([ResourceLoader::getRootPath() . DIRECTORY_SEPARATOR
                                    . 'src' . DIRECTORY_SEPARATOR
                                    . 'main' . DIRECTORY_SEPARATOR
                                    . 'resources' . DIRECTORY_SEPARATOR
                                    . 'lang' . DIRECTORY_SEPARATOR . 'stubbles.ini'
                            ],
                            $this->resourceLoader->listResourceUris('lang/stubbles.ini')
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListOfAllResourceUrisForNonExistingFile()
    {
        $this->assertEquals([],
                            $this->resourceLoader->listResourceUris('doesnot.exist')
        );
    }

    /**
     * @test
     * @deprecated  since 4.0.0, will be removed with 5.0.0
     */
    public function returnsRootPathBothStaticAndNonStatic()
    {
        $this->assertEquals(ResourceLoader::getRootPath(),
                            $this->resourceLoader->getRoot()
        );
    }
}
