<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\file;
use org\bovigo\vfs\vfsStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function stubbles\lang\reflect\annotationsOfConstructor;
/**
 * Test for stubbles\streams\file\FileStreamFactory.
 *
 * @group  streams
 * @group  streams_file
 */
class FileStreamFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  FileStreamFactory
     */
    protected $fileStreamFactory;
    /**
     * a file url used in the tests
     *
     * @type  string
     */
    protected $fileUrl;
    /**
     * root directory
     *
     * @type  vfsStreamDirectory
     */
    protected $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root = vfsStream::setup('home');
        vfsStream::newFile('in.txt')->at($this->root)->withContent('foo');
        $this->fileUrl           = vfsStream::url('home/out.txt');
        $this->fileUrl2          = vfsStream::url('home/test/out.txt');
        $this->fileStreamFactory = new FileStreamFactory();
    }

    /**
     * @test
     */
    public function annotationsPresent()
    {
        $annotations = annotationsOfConstructor($this->fileStreamFactory);
        assertTrue($annotations->contain('Named'));
        assert(
                $annotations->named('Named')[0]->getName(),
                equals('stubbles.filemode')
        );
        assertTrue($annotations->contain('Property'));
        assert(
                $annotations->named('Property')[0]->getName(),
                equals('stubbles.filemode')
        );
    }

    /**
     * @test
     */
    public function createInputStreamWithOptions()
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(vfsStream::url('home/in.txt'),
                                                                       ['filemode' => 'rb']
                           );
        assert($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function createInputStreamWithoutOptions()
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(
                vfsStream::url('home/in.txt')
        );
        assert($fileInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function createOutputStreamWithFilemodeOption()
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl,
                ['filemode' => 'wb']
        );
        assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        assert(file_get_contents($this->fileUrl), equals('foo'));
    }

    /**
     * @test
     */
    public function createOutputStreamWithFilemodeOptionAndDirectoryOptionSetToTrue()
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl,
                ['filemode'             => 'wb',
                 'createDirIfNotExists' => true
                ]
        );
        assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        assert(file_get_contents($this->fileUrl), equals('foo'));
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function createOutputStreamWithDirectoryOptionNotSetThrowsExceptionIfDirectoryDoesNotExist()
    {
        assertFalse(file_exists($this->fileUrl2));
        $this->fileStreamFactory->createOutputStream($this->fileUrl2);
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IOException
     */
    public function createOutputStreamWithDirectoryOptionSetToFalseThrowsExceptionIfDirectoryDoesNotExist()
    {
        $this->fileStreamFactory->createOutputStream(
                $this->fileUrl2,
                ['createDirIfNotExists' => false]
        );
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithDefaultPermissions()
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl2,
                ['createDirIfNotExists' => true]
        );
        assertTrue(file_exists($this->fileUrl2));
        $fileOutputStream->write('foo');
        assert(file_get_contents($this->fileUrl2), equals('foo'));
        assert($this->root->getChild('test')->getPermissions(), equals(0700));
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithOptionsPermissions()
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl2,
                ['createDirIfNotExists' => true,
                 'dirPermissions'       => 0666
                ]
        );
        assertTrue(file_exists($this->fileUrl2));
        $fileOutputStream->write('foo');
        assert(file_get_contents($this->fileUrl2), equals('foo'));
        assert($this->root->getChild('test')->getPermissions(), equals(0666));
    }

    /**
     * @test
     */
    public function createOutputStreamWithDelayedOption()
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl,
                ['delayed' => true]
        );
        assertFalse(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        assertTrue(file_exists($this->fileUrl));
        assert(file_get_contents($this->fileUrl), equals('foo'));
    }

    /**
     * @test
     */
    public function createOutputStreamWithoutOptions()
    {
        $fileOutputStream = $this->fileStreamFactory->createOutputStream(
                $this->fileUrl
        );
        assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        assert(file_get_contents($this->fileUrl), equals('foo'));
    }
}
