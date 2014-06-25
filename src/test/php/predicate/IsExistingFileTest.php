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
 * Tests for stubbles\predicate\IsExistingFile.
 *
 * @group  filesystem
 * @group  predicate
 * @since  4.0.0
 */
class IsExistingFileTest extends \PHPUnit_Framework_TestCase
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
        vfsStream::newFile('bar.txt')
                 ->at($root->getChild('basic'));
    }

    /**
     * @test
     */
    public function evaluatesToFalseForNull()
    {
        $isExistingFile = new IsExistingFile();
        $this->assertFalse($isExistingFile(null));
    }

    /**
     * @test
     */
    public function evaluatesToFalseForEmptyString()
    {
        $isExistingFile = new IsExistingFile();
        $this->assertFalse($isExistingFile(''));
    }

    /**
     * @test
     */
    public function evaluatesToTrueIfRelativePathExists()
    {
        $isExistingFile = new IsExistingFile(vfsStream::url('root/basic'));
        $this->assertTrue($isExistingFile('../foo.txt'));
    }

    /**
     * @test
     */
    public function evaluatesToFalseIfFileDoesNotExistRelatively()
    {
        $isExistingFile = new IsExistingFile(vfsStream::url('root/basic'));
        $this->assertFalse($isExistingFile('foo.txt'));
    }

    /**
     * @test
     */
    public function evaluatesToFalseIfFileDoesNotExistGlobally()
    {
        $isExistingFile = new IsExistingFile();
        $this->assertFalse($isExistingFile(__DIR__ . '/doesNotExist.txt'));
    }

    /**
     * @test
     */
    public function evaluatesToTrueIfFileDoesExistRelatively()
    {
        $isExistingFile = new IsExistingFile(vfsStream::url('root/basic'));
        $this->assertTrue($isExistingFile('bar.txt'));
    }

    /**
     * @test
     */
    public function evaluatesToTrueIfFileDoesExistGlobally()
    {
        $isExistingFile = new IsExistingFile();
        $this->assertTrue($isExistingFile(__FILE__));
    }

    /**
     * @test
     */
    public function evaluatesToFalseIfIsRelativeDir()
    {
        $isExistingFile = new IsExistingFile(vfsStream::url('root'));
        $this->assertFalse($isExistingFile('basic'));
    }

    /**
     * @test
     */
    public function evaluatesToFalseIfIsGlobalDir()
    {
        $isExistingFile = new IsExistingFile();
        $this->assertFalse($isExistingFile(__DIR__));
    }
}
