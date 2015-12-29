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
use stubbles\streams\memory\MemoryInputStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\predicate\equals;
use function bovigo\callmap\verify;
/**
 * Helper class for the test to make abstract class instantiable.
 */
class TestAbstractDecoratedInputStream extends AbstractDecoratedInputStream
{
    // intentionally empty
}
/**
 * Test for stubbles\streams\AbstractDecoratedInputStream.
 *
 * @group  streams
 */
class AbstractDecoratedInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  \stubbles\streams\AbstractDecoratedInputStream
     */
    private $abstractDecoratedInputStream;
    /**
     * mocked input stream
     *
     * @type  \stubbles\streams\memory\MemoryInputStream
     */
    private $memory;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->memory = new MemoryInputStream("foo\n");
        $this->abstractDecoratedInputStream = new TestAbstractDecoratedInputStream($this->memory);
    }

    /**
     * @test
     */
    public function readCallsDecoratedStream()
    {
        assert($this->abstractDecoratedInputStream->read(), equals("foo\n"));
    }

    /**
     * @test
     */
    public function readLineCallsDecoratedStream()
    {
        assert($this->abstractDecoratedInputStream->readLine(), equals('foo'));
    }

    /**
     * @test
     */
    public function bytesLeftCallsDecoratedStream()
    {
        assert($this->abstractDecoratedInputStream->bytesLeft(), equals(4));
    }

    /**
     * @test
     */
    public function eofCallsDecoratedStream()
    {
        assertFalse($this->abstractDecoratedInputStream->eof());
    }

    /**
     * @test
     */
    public function closeCallsDecoratedStream()
    {
        $inputStream = NewInstance::of(InputStream::class);
        $abstractDecoratedInputStream = new TestAbstractDecoratedInputStream($inputStream);
        $abstractDecoratedInputStream->close();
        verify($inputStream, 'close')->wasCalledOnce();
    }
}
