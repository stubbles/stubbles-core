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
use bovigo\callmap\NewInstance;
/**
 * Test for stubbles\streams\PrefixedStreamFactory.
 *
 * @group  streams
 */
class PrefixedStreamFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\PrefixedStreamFactorys
     */
    protected $prefixedStreamFactory;
    /**
     * mocked stream factory
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $streamFactory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->streamFactory = NewInstance::of('stubbles\streams\StreamFactory');
        $this->prefixedStreamFactory = new PrefixedStreamFactory(
                $this->streamFactory,
                'prefix/'
        );
    }

    /**
     * @test
     */
    public function inputStreamGetsPrefix()
    {
        $inputStream = NewInstance::of('stubbles\streams\InputStream');
        $this->streamFactory->mapCalls(
                ['createInputStream' => $inputStream]
        );
        assertSame(
                $inputStream,
                $this->prefixedStreamFactory->createInputStream(
                        'foo',
                        ['bar' => 'baz']
                )
        );
        assertEquals(
                ['prefix/foo', ['bar' => 'baz']],
                $this->streamFactory->argumentsReceivedFor('createInputStream')
        );
    }

    /**
     * @test
     */
    public function outputStreamGetsPrefix()
    {
        $outputStream = NewInstance::of('stubbles\streams\OutputStream');
        $this->streamFactory->mapCalls(
                ['createOutputStream' => $outputStream]
        );
        assertSame(
                $outputStream,
                $this->prefixedStreamFactory->createOutputStream(
                        'foo',
                        ['bar' => 'baz']
                )
        );
        assertEquals(
                ['prefix/foo', ['bar' => 'baz']],
                $this->streamFactory->argumentsReceivedFor('createOutputStream')
        );
    }
}
