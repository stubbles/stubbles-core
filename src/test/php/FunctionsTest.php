<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles;
use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
/**
 * Tests for stubbles\*().
 *
 * @since  3.1.0
 * @group  lang
 */
class FunctionsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @since  3.4.2
     */
    public function lastErrorMessageShouldBeNullByDefault()
    {
        assert(lastErrorMessage(), equals(Result::of(null)));
    }

    /**
     * @test
     * @since  3.4.2
     */
    public function lastErrorMessageShouldContainLastError()
    {
        @file_get_contents(__DIR__ . '/doesNotExist.txt');
        assert(
                lastErrorMessage()->value(),
                equals('file_get_contents(' . __DIR__ . '/doesNotExist.txt): failed to open stream: No such file or directory')
        );
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function typeOfOjectReturnsNameOfClass()
    {
        assert(typeOf($this), equals(__CLASS__));
    }

    /**
     * @test
     * @since  7.0.0
     */
    public function typeOfResourceReturnsResourceWithResourceType()
    {
        $fp = fopen(__FILE__, 'r');
        assert(typeOf($fp), equals('resource[stream]'));
        fclose($fp);
    }

    /**
     * @return  array
     */
    public function valueTypes()
    {
        return [
                [303, 'integer'],
                [3.03, 'double'],
                ['foo', 'string'],
                [[], 'array'],
                [true, 'boolean'],
                [null, 'NULL']
        ];
    }

    /**
     * @param  mixed   $value
     * @param  string  $expectedType
     * @test
     * @dataProvider  valueTypes
     * @since  7.0.0
     */
    public function typeOfOtherValuesReturnsNativeType($value, $expectedType)
    {
        assert(typeOf($value), equals($expectedType));
    }
}
