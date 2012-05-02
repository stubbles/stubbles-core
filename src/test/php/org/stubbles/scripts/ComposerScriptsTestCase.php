<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  org\stubbles\scripts
 */
namespace org\stubbles\scripts;
use org\bovigo\vfs\vfsStream;
use org\stubbles\test\scripts\Event;
use org\stubbles\test\scripts\IOInterface;
/**
 * Tests for org\stubbles\scripts\ComposerScripts.
 *
 * @group   composer
 */
class ComposerScriptsTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * root directory
     *
     * @type  org\bovigo\vfs\vfsStreamDirectory
     */
    private $rootDir;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->rootDir  = vfsStream::setup();
        vfsStream::newDirectory('vendor/stubbles/core')
                 ->at($this->rootDir);
        ComposerScripts::setRootDir(vfsStream::url('root'));
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        ComposerScripts::setRootDir(null);
    }

    /**
     * @test
     */
    public function postInstallCreatesCacheFolderIfDoesNotExist()
    {
        $this->assertTrue(ComposerScripts::postInstall(new Event(new IOInterface())));
        $this->assertTrue($this->rootDir->hasChild('cache'));
    }

    /**
     * @test
     */
    public function postInstallClearsCacheFilesIfCacheDirExists()
    {
        $cacheDir = vfsStream::newDirectory('cache')->at($this->rootDir);
        vfsStream::newFile('foo.cache')->at($cacheDir);
        vfsStream::newFile('bar.txt')->at($cacheDir);
        $subCacheDir = vfsStream::newDirectory('subdir')->at($cacheDir);
        vfsStream::newFile('more.cache')->at($subCacheDir);
        vfsStream::newFile('other.txt')->at($subCacheDir);
        $this->assertTrue(ComposerScripts::postInstall(new Event(new IOInterface())));
        $this->assertFalse($cacheDir->hasChild('foo.cache'));
        $this->assertTrue($cacheDir->hasChild('bar.txt'));
        $this->assertTrue($cacheDir->hasChild('subdir'));
        $this->assertFalse($subCacheDir->hasChild('more.cache'));
        $this->assertTrue($subCacheDir->hasChild('other.txt'));
    }

    /**
     * @test
     */
    public function postInstallSignalsAmountOfClearedCacheFiles()
    {
        $cacheDir = vfsStream::newDirectory('cache')->at($this->rootDir);
        vfsStream::newFile('foo.cache')->at($cacheDir);
        vfsStream::newFile('bar.txt')->at($cacheDir);
        $subCacheDir = vfsStream::newDirectory('subdir')->at($cacheDir);
        vfsStream::newFile('more.cache')->at($subCacheDir);
        vfsStream::newFile('other.txt')->at($subCacheDir);
        $io = new IOInterface();
        $this->assertTrue(ComposerScripts::postInstall(new Event($io)));
        $output = $io->getOutput();
        $this->assertEquals('Cleared 2 files from cache', $output[0]);
    }

    /**
     * @test
     */
    public function postUpdateClearsCacheFilesIfCacheDirExists()
    {
        $cacheDir = vfsStream::newDirectory('cache')->at($this->rootDir);
        vfsStream::newFile('foo.cache')->at($cacheDir);
        vfsStream::newFile('bar.txt')->at($cacheDir);
        $subCacheDir = vfsStream::newDirectory('subdir')->at($cacheDir);
        vfsStream::newFile('more.cache')->at($subCacheDir);
        vfsStream::newFile('other.txt')->at($subCacheDir);
        $this->assertTrue(ComposerScripts::postUpdate(new Event(new IOInterface())));
        $this->assertFalse($cacheDir->hasChild('foo.cache'));
        $this->assertTrue($cacheDir->hasChild('bar.txt'));
        $this->assertTrue($cacheDir->hasChild('subdir'));
        $this->assertFalse($subCacheDir->hasChild('more.cache'));
        $this->assertTrue($subCacheDir->hasChild('other.txt'));
    }

    /**
     * @test
     */
    public function postUpdateSignalsAmountOfClearedCacheFiles()
    {
        $cacheDir = vfsStream::newDirectory('cache')->at($this->rootDir);
        vfsStream::newFile('foo.cache')->at($cacheDir);
        vfsStream::newFile('bar.txt')->at($cacheDir);
        $subCacheDir = vfsStream::newDirectory('subdir')->at($cacheDir);
        vfsStream::newFile('other.txt')->at($subCacheDir);
        $io = new IOInterface();
        $this->assertTrue(ComposerScripts::postUpdate(new Event($io)));
        $output = $io->getOutput();
        $this->assertEquals('Cleared 1 file from cache', $output[0]);
    }

}
?>