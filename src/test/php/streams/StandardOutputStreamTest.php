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
 * Test for stubbles\streams\StandardOutputStream.
 *
 * @group  streams
 * @since  5.4.0
 */
class StandardOutputStreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function writesToStandardOutputBuffer()
    {
        $out = new StandardOutputStream();
        ob_start();
        $out->write('foo');
        assertEquals('foo', ob_get_contents());
        ob_end_clean();
    }
}
