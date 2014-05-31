<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams;
/**
 * Test for stubbles\streams\PrefixedStreamFactory.
 *
 * @group  streams
 */
class PrefixedStreamFactoryTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  PrefixedStreamFactory
     */
    protected $prefixedStreamFactory;
    /**
     * mocked stream factory
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockStreamFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockStreamFactory     = $this->getMock('stubbles\streams\StreamFactory');
        $this->prefixedStreamFactory = new PrefixedStreamFactory($this->mockStreamFactory, 'prefix/');
    }

    /**
     * @test
     */
    public function inputStreamGetsPrefix()
    {
        $mockInputStream = $this->getMock('stubbles\streams\InputStream');
        $this->mockStreamFactory->expects($this->once())
                                ->method('createInputStream')
                                ->with($this->equalTo('prefix/foo'), $this->equalTo(array('bar' => 'baz')))
                                ->will($this->returnValue($mockInputStream));
        $this->assertSame($mockInputStream, $this->prefixedStreamFactory->createInputStream('foo', array('bar' => 'baz')));
    }

    /**
     * @test
     */
    public function outputStreamGetsPrefix()
    {
        $mockOutputStream = $this->getMock('stubbles\streams\OutputStream');
        $this->mockStreamFactory->expects($this->once())
                                ->method('createOutputStream')
                                ->with($this->equalTo('prefix/foo'), $this->equalTo(array('bar' => 'baz')))
                                ->will($this->returnValue($mockOutputStream));
        $this->assertSame($mockOutputStream, $this->prefixedStreamFactory->createOutputStream('foo', array('bar' => 'baz')));
    }
}
