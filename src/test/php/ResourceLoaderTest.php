<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles;
use bovigo\callmap\NewInstance;
use stubbles\streams\InputStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertEmptyArray;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\contains;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function stubbles\reflect\annotationsOf;
/**
 * Tests for stubbles\ResourceLoader.
 *
 * @since  1.6.0
 * @group  app
 */
class ResourceLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\ResourceLoader
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
        assertTrue(annotationsOf($this->resourceLoader)->contain('Singleton'));
    }

    /**
     * @test
     * @expectedException  DomainException
     * @since  4.0.0
     */
    public function openNonExistingResourceThrowsDomainException()
    {
        $this->resourceLoader->open('lang/doesNotExist.ini');
    }

    /**
     * @test
     * @expectedException  DomainException
     * @since  4.0.0
     */
    public function loadNonExistingResourceThrowsDomainException()
    {
        $this->resourceLoader->load('lang/doesNotExist.ini');
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function openLocalResourceReturnsInputStream()
    {
        assert(
                $this->resourceLoader->open('lang/stubbles.ini'),
                isInstanceOf(InputStream::class)
        );
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function openLocalResourceWithOtherUsesOther()
    {
        $myClass = NewInstance::classname(InputStream::class);
        assert(
                $this->resourceLoader->open('lang/stubbles.ini', $myClass),
                isInstanceOf($myClass)
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  7.0.0
     */
    public function openLocalResourceWithNonExistingClassThrowsInvalidArgumentException()
    {
        $this->resourceLoader->open('lang/stubbles.ini', 'DoesNotExist');
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadLocalResourceWithoutLoaderReturnsContent()
    {
        assert(
                $this->resourceLoader->load('lang/stubbles.ini'),
                equals("[foo]\nbar=\"baz\"\n")
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadLocalResourceWithLoaderReturnsLoaderResult()
    {
        assert(
                $this->resourceLoader->load(
                        'lang/stubbles.ini',
                        function($resource)
                        {
                            $rootpath = new Rootpath();
                            assert(
                                    $rootpath->to('src', 'main', 'resources', 'lang', 'stubbles.ini'),
                                    equals($resource)
                            );
                            return 'foo';
                        }
                ),
                equals('foo')
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function openResourceWithCompletePathInRootReturnsInputStream()
    {
        assert(
                $this->resourceLoader->open(__FILE__),
                isInstanceOf(InputStream::class)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadResourceWithCompletePathInRootWithoutLoaderReturnsContent()
    {
        assert(
                $this->resourceLoader->load(__FILE__),
                contains('loadResourceWithCompletePathInRootReturnsContent()')
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function loadLocalWithCompletePathWithLoaderReturnsLoaderResult()
    {
        $rootpath = new Rootpath();
        assert(
                $this->resourceLoader->load(
                        $rootpath->to('src', 'main', 'resources', 'lang', 'stubbles.ini'),
                        function($resource) use($rootpath)
                        {
                            assert(
                                    $rootpath->to('src', 'main', 'resources', 'lang', 'stubbles.ini'),
                                    equals($resource)
                            );
                            return 'foo';
                        }
                ),
                equals('foo')
        );
    }

    /**
     * @test
     * @expectedException  DomainException
     * @since  4.0.0
     */
    public function openResourceWithCompletePathOutsideRootThrowsDomainException()
    {
        $this->resourceLoader->open(tempnam(sys_get_temp_dir(), 'test.txt'));
    }

    /**
     * @test
     * @expectedException  DomainException
     * @since  4.0.0
     */
    public function loadResourceWithCompletePathOutsideRootThrowsDomainException()
    {
        $this->resourceLoader->load(tempnam(sys_get_temp_dir(), 'test.txt'));
    }

    /**
     * @test
     * @expectedException  OutOfBoundsException
     * @since  4.0.0
     */
    public function openResourceWithCompleteRealpathOutsideRootThrowsOutOfBoundsException()
    {
        $resourceLoader = new ResourceLoader(__DIR__ . '/environments');
        $resourceLoader->open(__DIR__ . '/environments/../ResourceLoaderTest.php');
    }

    /**
     * @test
     * @expectedException  OutOfBoundsException
     * @since  4.0.0
     */
    public function loadResourceWithCompleteRealpathOutsideRootThrowsOutOfBoundsException()
    {
        $resourceLoader = new ResourceLoader(__DIR__ . '/environments');
        $resourceLoader->load(__DIR__ . '/environments/../ResourceLoaderTest.php');
    }

    /**
     * @test
     */
    public function returnsListOfAllResourceUrisForExistingFile()
    {
        assert(
                $this->resourceLoader->availableResourceUris('lang/stubbles.ini'),
                equals([
                        (new Rootpath()) . DIRECTORY_SEPARATOR
                        . 'src' . DIRECTORY_SEPARATOR
                        . 'main' . DIRECTORY_SEPARATOR
                        . 'resources' . DIRECTORY_SEPARATOR
                        . 'lang' . DIRECTORY_SEPARATOR . 'stubbles.ini'
                ])
        );
    }

    /**
     * @test
     */
    public function returnsEmptyListOfAllResourceUrisForNonExistingFile()
    {
        assertEmptyArray(
                $this->resourceLoader->availableResourceUris('doesnot.exist')
        );
    }
}
