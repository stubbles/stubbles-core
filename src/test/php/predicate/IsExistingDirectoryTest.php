<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\predicate;
use org\bovigo\vfs\vfsStream;
/**
 * Tests for stubbles\predicate\IsExistingDirectory.
 *
 * @group  filesystem
 * @group  predicate
 * @since  4.0.0
 */
class IsExistingDirectoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * set up test environment
     */
    public function setUp()
    {
        $root = vfsStream::setup();
        vfsStream::newDirectory('basic')
                 ->at($root);
        vfsStream::newFile('foo.txt')
                 ->at($root);
        vfsStream::newDirectory('other')
                 ->at($root);
        vfsStream::newDirectory('bar')
                 ->at($root->getChild('basic'));
    }

    /**
     * @test
     */
    public function evaluatesToFalseForNull()
    {
        $isExistingDirectory = new IsExistingDirectory();
        $this->assertFalse($isExistingDirectory(null));
    }

    /**
     * @test
     */
    public function evaluatesToFalseForEmptyString()
    {
        $isExistingDirectory = new IsExistingDirectory();
        $this->assertFalse($isExistingDirectory(''));
    }

    /**
     * @test
     */
    public function evaluatesToTrueForRelativePath()
    {
        $isExistingDirectory = new IsExistingDirectory(vfsStream::url('root/basic'));
        $this->assertTrue($isExistingDirectory('../other'));
    }

    /**
     * @test
     */
    public function evaluatesToFalseIfDirDoesNotExistRelatively()
    {
        $isExistingDirectory = new IsExistingDirectory(vfsStream::url('root/basic'));
        $this->assertFalse($isExistingDirectory('other'));
    }

    /**
     * @test
     */
    public function evaluatesToFalseIfDirDoesNotExistGlobally()
    {
        $isExistingDirectory = new IsExistingDirectory();
        $this->assertFalse($isExistingDirectory(__DIR__ . '/../doesNotExist'));
    }

    /**
     * @test
     */
    public function evaluatesToTrueIfDirDoesExistRelatively()
    {
        $isExistingDirectory = new IsExistingDirectory(vfsStream::url('root/basic'));
        $this->assertTrue($isExistingDirectory('bar'));
    }

    /**
     * @test
     */
    public function evaluatesToTrueIfDirDoesExistGlobally()
    {
        $isExistingDirectory = new IsExistingDirectory();
        $this->assertTrue($isExistingDirectory(__DIR__));
    }

    /**
     * @test
     */
    public function evaluatesToFalseIfIsRelativeFile()
    {
        $isExistingDirectory = new IsExistingDirectory(vfsStream::url('root'));
        $this->assertFalse($isExistingDirectory('foo.txt'));
    }

    /**
     * @test
     */
    public function evaluatesToFalseIfIsGlobalFile()
    {
        $isExistingDirectory = new IsExistingDirectory();
        $this->assertFalse($isExistingDirectory(__FILE__));
    }
}
