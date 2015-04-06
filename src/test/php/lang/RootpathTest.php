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
use org\bovigo\vfs\vfsStream;
/**
 * Tests for stubbles\lang\Rootpath.
 *
 * @since  4.0.0
 * @group  lang
 * @group  lang_core
 */
class RootpathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function constructWithoutArgumentCalculatesRootpathAutomatically()
    {
        assertEquals(
                realpath(__DIR__ . '/../../../../'),
                (string) new Rootpath()
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function constructWithNonExistingPathThrowsIllegalArgumentException()
    {
        new Rootpath(__DIR__ . '/doesNotExist');
    }

    /**
     * @test
     */
    public function constructWithExistingPath()
    {
        assertEquals(
                __DIR__,
                (string) new Rootpath(__DIR__)
        );
    }

    /**
     * @test
     */
    public function constructWithExistingPathTurnsDotsIntoRealpath()
    {
        assertEquals(
                dirname(__DIR__),
                (string) new Rootpath(__DIR__ . '/..')
        );
    }

    /**
     * @test
     */
    public function constructWithVfsStreamUriDoesNotApplyRealpath()
    {
        $root = vfsStream::setup()->url();
        assertEquals(
                $root,
                (string) new Rootpath($root)
        );
    }

    /**
     * @test
     */
    public function castFromInstanceReturnsInstance()
    {
        $rootpath = new Rootpath();
        assertSame(
                $rootpath,
                Rootpath::castFrom($rootpath)
        );
    }

     /**
     * @test
     */
    public function castFromWithoutArgumentCalculatesRootpathAutomatically()
    {
        assertEquals(
                realpath(__DIR__ . '/../../../../'),
                (string) Rootpath::castFrom(null)
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function castFromWithNonExistingPathThrowsIllegalArgumentException()
    {
        Rootpath::castFrom(__DIR__ . '/doesNotExist');
    }

    /**
     * @test
     */
    public function castFromWithExistingPath()
    {
        assertEquals(
                __DIR__,
                (string) Rootpath::castFrom(__DIR__)
        );
    }

    /**
     * @test
     */
    public function toCreatesPath()
    {
        assertEquals(
                __FILE__,
                (string) Rootpath::castFrom(null)->to('src', 'test', 'php', 'lang', 'RootpathTest.php')
        );
    }

    /**
     * @test
     */
    public function doesNotContainNonExistingPath()
    {
        assertFalse(
                Rootpath::castFrom(null)->contains(__DIR__ . '/doesNotExist')
        );
    }

    /**
     * @test
     */
    public function doesNotContainPathOutsideRoot()
    {
        assertFalse(
                Rootpath::castFrom(__DIR__)->contains(dirname(__DIR__))
        );
    }

    /**
     * @test
     */
    public function containsPathInsideRoot()
    {
        assertTrue(
                Rootpath::castFrom(__DIR__)->contains(__FILE__)
        );
    }

    /**
     * @test
     */
    public function listOfSourcePathesIsEmptyIfNoAutoloaderPresent()
    {
        assertEquals(
                [],
                Rootpath::castFrom(__DIR__)->sourcePathes()
        );
    }

    /**
     * @test
     */
    public function listOfSourcePathesWorksWithPsr0Only()
    {
        $rootpath = Rootpath::castFrom(Rootpath::castFrom(null)->to('src', 'test', 'resources', 'rootpath', 'psr0'));
        assertEquals(
                [$rootpath->to('vendor/mikey179/vfsStream/src/main/php'),
                 $rootpath->to('vendor/symfony/yaml')
                ],
                $rootpath->sourcePathes()
        );
    }

    /**
     * @test
     */
    public function listOfSourcePathesWorksWithPsr4Only()
    {
        $rootpath = Rootpath::castFrom(Rootpath::castFrom(null)->to('src', 'test', 'resources', 'rootpath', 'psr4'));
        assertEquals(
                [$rootpath->to('vendor/stubbles/core-dev/src/main/php'),
                 $rootpath->to('src/main/php')
                ],
                $rootpath->sourcePathes()
        );
    }

    /**
     * @test
     */
    public function listOfSourcePathesContainsPsr0AndPsr4()
    {
        $rootpath = Rootpath::castFrom(Rootpath::castFrom(null)->to('src', 'test', 'resources', 'rootpath', 'all'));
        assertEquals(
                [$rootpath->to('vendor/mikey179/vfsStream/src/main/php'),
                 $rootpath->to('vendor/symfony/yaml'),
                 $rootpath->to('vendor/stubbles/core-dev/src/main/php'),
                 $rootpath->to('src/main/php')
                ],
                $rootpath->sourcePathes()
        );
    }
}
