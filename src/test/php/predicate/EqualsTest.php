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
/**
 * Tests for stubbles\predicate\Equals.
 *
 * @group  predicate
 * @since  4.0.0
 */
class EqualsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function constructionWithObjectThrowsIllegalArgumentException()
    {
        new Equals(new \stdClass());
    }

    /**
     * @return  array
     */
    public function tuplesEvaluatingToTrue()
    {
        return [[true, true],
                [false, false],
                [5, 5],
                [null, null],
                ['foo', 'foo']
        ];
    }

    /**
     * @param  scalar  $expected
     * @param  mixed   $value
     * @test
     * @dataProvider  tuplesEvaluatingToTrue
     */
    public function evaluatesToTrue($expected, $value)
    {
        $equals = new Equals($expected);
        $this->assertTrue($equals($value));
    }

    /**
     * @return  array
     */
    public function tuplesEvaluatingToFalse()
    {
        return [[true, false],
                [false, true],
                [false, new \stdClass()],
                [false, null],
                [5, 'foo'],
                [5, 6],
                [true, 5],
                [false, 0],
                [true, 'foo'],
                ['foo', 'bar'],
                [5, new \stdClass()],
                ['foo', new \stdClass()]
        ];
    }

    /**
     * @param  scalar  $expected
     * @param  mixed   $value
     * @test
     * @dataProvider  tuplesEvaluatingToFalse
     */
    public function evaluatesToFalse($expected, $value)
    {
        $equals = new Equals($expected);
        $this->assertFalse($equals($value));
    }
}
