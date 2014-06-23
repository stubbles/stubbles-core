<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\filter;
/**
 * Test for stubbles\streams\filter\CallableStreamFilter.
 *
 * @group  streams
 * @group  streams_filter
 * @since  4.0.0
 */
class CallableStreamFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function usesGivenCallable()
    {
        $callableStreamFilter = new CallableStreamFilter(
                function($data)
                {
                    $this->assertEquals('foo', $data);
                    return false;
                });
        $this->assertFalse($callableStreamFilter->shouldFilter('foo'));
    }
}
