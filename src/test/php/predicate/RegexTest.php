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
 * Tests for stubbles\predicate\Regex.
 *
 * @group  predicate
 * @since  4.0.0
 */
class RegexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return  array
     */
    public function validValues()
    {
        return [['/^([a-z]{3})$/', 'foo'],
                ['/^([a-z]{3})$/i', 'foo'],
                ['/^([a-z]{3})$/i', 'Bar']
        ];
    }

    /**
     * @param  string  $regex
     * @param  string  $value
     * @test
     * @dataProvider  validValues
     */
    public function validValueEvaluatesToTrue($regex, $value)
    {
        $regex = new Regex($regex);
        assertTrue($regex($value));
    }

    /**
     * @return  array
     */
    public function invalidValues()
    {
        return [['/^([a-z]{3})$/', 'Bar'],
                ['/^([a-z]{3})$/', 'baz0123'],
                ['/^([a-z]{3})$/', null],
                ['/^([a-z]{3})$/i', 'baz0123'],
                ['/^([a-z]{3})$/i', null]
        ];
    }

    /**
     * @param  string  $regex
     * @param  string  $value
     * @test
     * @dataProvider  invalidValues
     */
    public function invalidValueEvaluatesToFalse($regex, $value)
    {
        $regex = new Regex($regex);
        assertFalse($regex($value));
    }

    /**
     * @test
     * @expectedException  RuntimeException
     */
    public function invalidRegexThrowsRuntimeExceptionOnEvaluation()
    {
        $regex = new Regex('^([a-z]{3})$');
        $regex('foo');
    }
}
