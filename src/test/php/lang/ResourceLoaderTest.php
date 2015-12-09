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
use stubbles\streams\InputStream;
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
        assertTrue(
                reflect\annotationsOf($this->resourceLoader)->contain('Singleton')
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
        assertInstanceOf(
                InputStream::class,
                $this->resourceLoader->open('lang/stubbles.ini')
        );

    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadLocalResourceWithoutLoaderReturnsContent()
    {
        assertEquals(
                "[foo]\nbar=\"baz\"\n",
                $this->resourceLoader->load('lang/stubbles.ini')
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadLocalResourceWithLoaderReturnsLoaderResult()
    {
        assertEquals(
                'foo',
                $this->resourceLoader->load(
                        'lang/stubbles.ini',
                        function($resource)
                        {
                            $rootpath = new Rootpath();
                            assertEquals(
                                    $resource,
                                    $rootpath->to('src', 'main', 'resources', 'lang', 'stubbles.ini')
                            );
                            return 'foo';
                        }
                )
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function openResourceWithCompletePathInRootReturnsInputStream()
    {
        assertInstanceOf(
                InputStream::class,
                $this->resourceLoader->open(__FILE__)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadResourceWithCompletePathInRootWithoutLoaderReturnsContent()
    {
        assertContains(
                'loadResourceWithCompletePathInRootReturnsContent()',
                $this->resourceLoader->load(__FILE__)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadLocalWithCompletePathWithLoaderReturnsLoaderResult()
    {
        $rootpath = new Rootpath();
        assertEquals(
                'foo',
                $this->resourceLoader->load(
                        $rootpath->to('src', 'main', 'resources', 'lang', 'stubbles.ini'),
                        function($resource) use($rootpath)
                        {
                            assertEquals(
                                    $resource,
                                    $rootpath->to('src', 'main', 'resources', 'lang', 'stubbles.ini')
                            );
                            return 'foo';
                        }
                )
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
     * @expectedException  InvalidArgumentException
     * @since  4.0.0
     */
    public function openResourceWithCompleteRealpathOutsideRootThrowsIllegalArgumentException()
    {
        $resourceLoader = new ResourceLoader(__DIR__ . '/exception');
        $resourceLoader->open(__DIR__ . '/exception/../ResourceLoaderTest.php');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
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
        assertEquals(
                [(new Rootpath()) . DIRECTORY_SEPARATOR
                 . 'src' . DIRECTORY_SEPARATOR
                 . 'main' . DIRECTORY_SEPARATOR
                 . 'resources' . DIRECTORY_SEPARATOR
                 . 'lang' . DIRECTORY_SEPARATOR . 'stubbles.ini'
                ],
                $this->resourceLoader->availableResourceUris('lang/stubbles.ini')
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListOfAllResourceUrisForNonExistingFile()
    {
        assertEquals(
                [],
                $this->resourceLoader->availableResourceUris('doesnot.exist')
        );
    }
}
