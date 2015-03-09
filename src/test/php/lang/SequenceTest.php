<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * The contents of this file draw heavily from XP Framework
 * https://github.com/xp-forge/sequence
 *
 * Copyright (c) 2001-2014, XP-Framework Team
 * All rights reserved.
 * https://github.com/xp-framework/xp-framework/blob/master/core/src/main/php/LICENCE
 *
 * @package  stubbles
 */
namespace stubbles\lang;
use stubbles\streams\memory\MemoryOutputStream;
/**
 * Tests for stubbles\lang\Sequence.
 *
 * @since  5.2.0
 */
class SequenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     * @type  array
     */
    private static $generators = [];

    /**
     * Defines generator fixtures. Since their definition involves new syntax
     * unparseable with previous PHP versions, wrap in eval() statements.
     *
     * @beforeClass
     */
    public static function defineGenerators()
    {
        if (class_exists('Generator', false) && count(self::$generators) === 0) {
            self::$generators= [
              [eval('return function() { yield 1; yield 2; yield 3; };'), 'closure'],
              [eval('$f= function() { yield 1; yield 2; yield 3; }; return $f();'), 'generator']
            ];
        }
    }
    /**
     * @param   array    $expected
     * @param   Sequence $sequence
     * @param   string   $message   optional
     */
    private function assertSequence($expected, Sequence $sequence, $message= '!=')
    {
        $this->assertEquals($expected, $sequence->values(), $message);
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     */
    public function sequenceOfInvalidElementsThrowsIllegalArgumentException()
    {
        Sequence::of(new \stdClass());
    }

    /**
     * Returns valid arguments for the `of()` method: Arrays, iterables,
     * iterators and generators (the latter only if available).
     *
     * @return  array
     */
    public function validData() {
        return array_merge(
                self::$generators,
                [[[1, 2, 3], 'array'],
                 [new \ArrayIterator([1, 2, 3]), 'iterator'],
                 [Sequence::of([1, 2, 3]), 'self']
                ]
        );
    }

    /**
     * @param  array   $input
     * @param  string  $name
     * @test
     * @dataProvider  validData
     */
    public function dataReturnsElementsAsArray($input, $name)
    {
        $this->assertSequence([1, 2, 3], Sequence::of($input), $name);
    }

    /**
     * @test
     */
    public function filterRemovesElements()
    {
        $this->assertSequence([2, 4], Sequence::of([1, 2, 3, 4])->filter(function($e) { return 0 === $e % 2; }));
    }

    /**
     * @test
     */
    public function filterWithNativeFunction()
    {
        $this->assertSequence(['Hello', 'World'], Sequence::of(['Hello', 1337, 'World'])->filter('is_string'));
    }

    /**
     * @test
     */
    public function map()
    {
        $this->assertSequence([2, 4, 6, 8], Sequence::of([1, 2, 3, 4])->map(function($e) { return $e * 2; }));
    }

    /**
     * @test
     */
    public function mapWithNativeFunction()
    {
        $this->assertSequence([1.0, 2.0, 3.0], Sequence::of([1.9, 2.5, 3.1])->map('floor'));
    }

    /**
     * @test
     * @since  5.3.0
     */
    public function mapKeys()
    {
        $this->assertEquals(
                [0 => 1, 2 => 2, 4 => 3, 6 => 4],
                Sequence::of([1, 2, 3, 4])->mapKeys(function($e) { return $e * 2; })->data()
        );
    }

    /**
     * @return  array
     */
    public function countData()
    {
        return [
            [0, []],
            [1, [1]],
            [4, [1, 2, 3, 4]]
        ];
    }

    /**
     * @param  int    $expectedLength
     * @param  array  $elements
     * @test
     * @dataProvider  countData
     */
    public function sequenceIsCountable($expectedLength, $elements)
    {
        $this->assertEquals($expectedLength, count(Sequence::of($elements)));
    }

    /**
     * @return  array
     */
    public function sumData()
    {
        return [
            [0, []],
            [1, [1]],
            [10, [1, 2, 3, 4]]
        ];
    }

    /**
     * @param  int    $expectedResult
     * @param  array  $elements
     * @test
     * @dataProvider  sumData
     */
    public function sum($expectedResult, $elements)
    {
        $this->assertEquals($expectedResult, Sequence::of($elements)->reduce()->toSum());
    }

    /**
     * @return  array
     */
    public function minData()
    {
        return [
            [null, []],
            [1, [1]],
            [2, [10, 2, 7]]
        ];
    }

    /**
     * @param  int    $expectedResult
     * @param  array  $elements
     * @test
     * @dataProvider  minData
     */
    public function min($expectedResult, $elements)
    {
        $this->assertEquals($expectedResult, Sequence::of($elements)->reduce()->toMin());
    }

    /**
     * @return  array
     */
    public function maxData()
    {
        return [
            [null, []],
            [1, [1]],
            [10, [2, 10, 7]]
        ];
    }

    /**
     * @param  int    $expectedResult
     * @param  array  $elements
     * @test
     * @dataProvider  maxData
     */
    public function max($expectedResult, $elements)
    {
        $this->assertEquals($expectedResult, Sequence::of($elements)->reduce()->toMax());
    }

    /**
     * @test
     */
    public function reduceReturnsNullForEmptyInputWhenNoIdentityGiven()
    {
        $this->assertNull(Sequence::of([])->reduce(function($a, $b) { $this->fail('Should not be called'); }));
    }

    /**
     * @test
     */
    public function reduceReturnsIdentityForEmptyInput()
    {
        $this->assertEquals(
                -1,
                Sequence::of([])->reduce(function($a, $b) { $this->fail('Should not be called'); }, -1)
        );
    }

    /**
     * @test
     */
    public function reduceUsedForSumming()
    {
        $this->assertEquals(
                10,
                Sequence::of([1, 2, 3, 4])->reduce(function($a, $b) { return $a + $b; })
        );
    }

    /**
     * @test
     */
    public function reduceUsedForMaxWithNativeMaxFunction()
    {
        $this->assertEquals(10, Sequence::of([7, 1, 10, 3])->reduce('max'));
    }

    /**
     * @test
     */
    public function reduceUsedForConcatenation()
    {
        $this->assertEquals(
                'Hello World',
                Sequence::of(['Hello', ' ', 'World'])->reduce(function($a, $b) { return $a . $b; })
        );
    }

    /**
     * @test
     */
    public function collectUsedForAveraging()
    {
        $result = Sequence::of([1, 2, 3, 4])
                          ->collect()
                          ->with(
                                function() { return ['total' => 0, 'sum' => 0]; },
                                function(&$result, $element) { $result['total']++; $result['sum'] += $element; }
        );
        $this->assertEquals(2.5, $result['sum'] / $result['total']);
    }

    /**
     * @test
     */
    public function collectUsedForJoining()
    {
        $result = Sequence::of(['a', 'b', 'c'])
                          ->collect()
                          ->with(
                                function() { return ''; },
                                function(&$result, $arg) { $result .= ', '.$arg; },
                                function($result) { return substr($result, 2); }
        );
        $this->assertEquals('a, b, c', $result);
    }

    /**
     * @test
     */
    public function firstReturnsNullForEmptyInput()
    {
        $this->assertNull(Sequence::of([])->first());
    }

    /**
     * @test
     */
    public function firstReturnsFirstArrayElement()
    {
        $this->assertEquals(1, Sequence::of([1, 2, 3])->first());
    }

    /**
     * @test
     */
    public function eachOnEmptyInput()
    {
        $this->assertEquals(0, Sequence::of([])->each(function() { $this->fail('Should not be called'); }));
    }

    /**
     * @test
     */
    public function eachAppliesGivenCallableForAllElements()
    {
        $collect = [];
        $this->assertEquals(4, Sequence::of(['a', 'b', 'c', 'd'])->each(function($e) use(&$collect) { $collect[] = $e; }));
        $this->assertEquals(['a', 'b', 'c', 'd'], $collect);
    }

    /**
     * @test
     */
    public function eachStopsWhenCallableReturnsFalse()
    {
        $collect = [];
        $this->assertEquals(2, Sequence::of(['a', 'b', 'c', 'd'])->each(function($e) use(&$collect) { $collect[] = $e; if ('b' === $e) { return false; }}));
        $this->assertEquals(['a', 'b'], $collect);
    }

    /**
     * @test
     */
    public function eachWritingToStream()
    {
        $out = new MemoryOutputStream();
        Sequence::of([1, 2, 3, 4])->each([$out, 'write']);
        $this->assertEquals('1234', $out);
    }

    /**
     * @test
     */
    public function peekWithVarExport()
    {
        ob_start();
        Sequence::of([1, 2, 3, 4])->peek('var_export')->reduce()->toSum();
        $bytes = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('1234', $bytes);
    }

    /**
     * @test
     */
    public function peekWithVarExportAndKeys()
    {
        ob_start();
        Sequence::of(['a', 'b', 'c', 'd'])->peek('var_export', 'var_export')->reduce()->toSum();
        $bytes = ob_get_contents();
        ob_end_clean();
        $this->assertEquals("'a'0'b'1'c'2'd'3", $bytes);
    }

    /**
     * @test
     */
    public function limitStopsAtNthArrayElement()
    {
        $this->assertSequence([1, 2], Sequence::of([1, 2, 3])->limit(2));
    }

    /**
     * @test
     */
    public function limitStopsAtNthInfiniteElement()
    {
        $this->assertSequence([1, 2], Sequence::infinite(1, function($i) { return ++$i; })->limit(2));
    }

    /**
     * @test
     */
    public function limitStopsAtNthGeneratorElement()
    {
        $this->assertSequence([1, 2], Sequence::generate(1, function($i) { return $i + 1; }, function($i) { return $i < 10; })->limit(2));
    }

    /**
     * @test
     */
    public function skipIgnoresNumberOfArrayElements()
    {
        $this->assertSequence([6], Sequence::of([4, 5, 6])->skip(2));
    }

    /**
     * @test
     */
    public function skipIgnoresNumberOfInfiniteElements()
    {
        $this->assertSequence([3, 4, 5], Sequence::infinite(1, function($i) { return ++$i; })->skip(2)->limit(3));
    }

    /**
     * @test
     */
    public function skipIgnoresNumberOfGeneratorElements()
    {
        $this->assertSequence([3, 4, 5], Sequence::generate(1, function($i) { return $i + 1; }, function($i) { return $i < 10; })->skip(2)->limit(3));
    }

    /**
     * @test
     */
    public function appendCreatesNewCombinedSequence()
    {
        $this->assertSequence([1, 2, 3, 4], Sequence::of([1, 2])->append(Sequence::of([3, 4])));
    }

   #[@test, @values([
   #  [[1, 2, 3], [1, 2, 2, 3, 1, 3]],
   #  [[new String("a"), new String("b")], [new String("a"), new String("a"), new String("b")]]
   #])]
   public function distinct($result, $input) {
     $this->assertSequence($result, Sequence::of($input)->distinct());
   }

    /**
     * @test
     */
    public function isUseableInsideForeach()
    {
        $result = [];
        foreach (Sequence::of(['foo' => 1, 'bar' => 2, 'baz' => 3]) as $key => $element) {
          $result[$key] = $element;
        }
        $this->assertEquals(['foo' => 1, 'bar' => 2, 'baz' => 3], $result);
    }

    /**
     * @test
     */
    public function dataReturnsCompleteDataAsArray()
    {
        $this->assertEquals(
                ['foo' => 'bar', 'baz' => 303],
                Sequence::of(new \ArrayIterator(['foo' => 'bar', 'baz' => 303]))->data()
        );
    }

    /**
     * @return  array
     * @since   5.3.2
     */
    public function xmlIgnoreMethods()
    {
        return [
                ['first'],
                ['count'],
                ['values'],
                ['getIterator']
        ];
    }

    /**
     * @param  string  $method  method to test for presence of annotation
     * @test
     * @since  5.3.2
     * @dataProvider  xmlIgnoreMethods
     */
    public function methodsAnnotatedWithXmlIgnore($method)
    {
        $this->assertTrue(
                reflect\annotationsOf('stubbles\lang\Sequence', $method)
                        ->contain('XmlIgnore')
        );
    }
}
