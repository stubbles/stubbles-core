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
 * Test for stubbles\streams\StandardInputStream.
 *
 * @group  streams
 * @since  5.4.0
 */
class StandardInputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @type  \stubbles\streams\StandardInputStream
     */
    private $standardInputStream;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->standardInputStream = new StandardInputStream();
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function seekAfterCloseThrowsLogicException()
    {
        $this->standardInputStream->close();
        $this->standardInputStream->seek(0);
    }

    /**
     * @test
     */
    public function canSeekToStartOfStream()
    {
        $this->standardInputStream->seek(0);
    }

    /**
     * @test
     */
    public function canSeekToAnyPosition()
    {
        $this->standardInputStream->seek(100);
    }

    /**
     * @test
     * @expectedException  LogicException
     */
    public function tellAfterCloseThrowsLogicException()
    {
        $this->standardInputStream->close();
        $this->standardInputStream->tell();
    }
}
