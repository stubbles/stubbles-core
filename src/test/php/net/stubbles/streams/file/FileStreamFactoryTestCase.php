<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\file;
use org\bovigo\vfs\vfsStream;
/**
 * Test for net\stubbles\streams\file\FileStreamFactory.
 *
 * @group  streams
 * @group  streams_file
 */
class FileStreamFactoryTestCase extends \PHPUnit_Framework_TestCase
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
        $constructor = $this->fileStreamFactory->getClass()->getConstructor();
        $this->assertTrue($constructor->hasAnnotation('Inject'));
        $this->assertTrue($constructor->getAnnotation('Inject')->isOptional());
        $this->assertTrue($constructor->hasAnnotation('Named'));
        $this->assertEquals('net.stubbles.filemode',
                            $constructor->getAnnotation('Named')->getName()
        );
    }

    /**
     * @test
     */
    public function createInputStreamWithOptions()
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(vfsStream::url('home/in.txt'),
                                                                       array('filemode' => 'rb')
                           );
        $this->assertEquals('foo', $fileInputStream->readLine());
    }

    /**
     * @test
     */
    public function createInputStreamWithoutOptions()
    {
        $fileInputStream = $this->fileStreamFactory->createInputStream(vfsStream::url('home/in.txt'));
        $this->assertEquals('foo', $fileInputStream->readLine());
    }

    /**
     * @test
     */
    public function createOutputStreamWithFilemodeOption()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl, array('filemode' => 'wb'));
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * @test
     */
    public function createOutputStreamWithFilemodeOptionAndDirectoryOptionSetToTrue()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl, array('filemode'             => 'wb',
                                                                                               'createDirIfNotExists' => true
                                                                                         )
                                                      );
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IOException
     */
    public function createOutputStreamWithDirectoryOptionNotSetThrowsExceptionIfDirectoryDoesNotExist()
    {
        $this->assertFalse(file_exists($this->fileUrl2));
        $this->fileStreamFactory->createOutputStream($this->fileUrl2);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IOException
     */
    public function createOutputStreamWithDirectoryOptionSetToFalseThrowsExceptionIfDirectoryDoesNotExist()
    {
        $this->assertFalse(file_exists($this->fileUrl2));
        $this->fileStreamFactory->createOutputStream($this->fileUrl2, array('createDirIfNotExists' => false));
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithDefaultPermissions()
    {
        $this->assertFalse(file_exists($this->fileUrl2));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl2, array('createDirIfNotExists' => true));
        $this->assertTrue(file_exists($this->fileUrl2));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl2));
        $this->assertEquals(0700, $this->root->getChild('test')->getPermissions());
    }

    /**
     * @test
     */
    public function createOutputStreamWithDirectoryOptionSetToTrueCreatesDirectoryWithOptionsPermissions()
    {
        $this->assertFalse(file_exists($this->fileUrl2));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl2, array('createDirIfNotExists' => true,
                                                                                                'dirPermissions'       => 0666
                                                                                          )
                                                      );
        $this->assertTrue(file_exists($this->fileUrl2));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl2));
        $this->assertEquals(0666, $this->root->getChild('test')->getPermissions());
    }

    /**
     * @test
     */
    public function createOutputStreamWithDelayedOption()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl, array('delayed' => true));
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertTrue(file_exists($this->fileUrl));
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }

    /**
     * @test
     */
    public function createOutputStreamWithoutOptions()
    {
        $this->assertFalse(file_exists($this->fileUrl));
        $fileOutputStream = $this->fileStreamFactory->createOutputStream($this->fileUrl);
        $this->assertTrue(file_exists($this->fileUrl));
        $fileOutputStream->write('foo');
        $this->assertEquals('foo', file_get_contents($this->fileUrl));
    }
}
?>