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
 * Tests for stubbles\predicate\ContainsAnyOf.
 *
 * @group  predicate
 * @since  5.3.0
 */
class ContainsAnyOfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * returns tuples which evaluate to true
     *
     * @return  array
     */
    public function tuplesEvaluatingToTrue()
    {
        return [[[true], true],
                [[false], false],
                [[5], 5],
                [[5], 55],
                [[5], 25],
                [[5], 'foo5'],
                [[5], 'fo5o'],
                [['foo', 'bar'], 'foobar'],
                [['foo', 'bar'], 'foo']
        ];
    }

    /**
     * @param  array  $contained
     * @param  mixed   $value
     * @test
     * @dataProvider  tuplesEvaluatingToTrue
     */
    public function evaluatesToTrue(array $contained, $value)
    {
        $contains = new ContainsAnyOf($contained);
        $this->assertTrue($contains($value));
    }

    /**
     * returns tuples which evaluate to false
     *
     * @return  array
     */
    public function tuplesEvaluatingToFalse()
    {
        return [[[true], false],
                [[false], true],
                [[false], new \stdClass()],
                [[false], null],
                [[5], 'foo'],
                [[5], 6],
                [[true], 5],
                [[false], 0],
                [[true], 'foo'],
                [['foo', 'baz'], 'bar']
        ];
    }

    /**
     * @param  array  $contained
     * @param  mixed   $value
     * @test
     * @dataProvider  tuplesEvaluatingToFalse
     */
    public function evaluatesToFalse(array $contained, $value)
    {
        $contains = new ContainsAnyOf($contained);
        $this->assertFalse($contains($value));
    }
}
