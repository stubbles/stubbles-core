<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\iterator;
/**
 * Tests for stubbles\lang\iterator\Generator.
 *
 * @group  lang
 * @group  lang_iterator
 * @group  sequence
 * @since  5.2.0
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function iterationStopsWhenValidatorReturnsFalse()
    {
        $generator = new Generator(
                2,
                function($value) { return $value + 2; },
                function($value) { return $value < 15; }
        );
        $result = [];
        foreach ($generator as $key => $value) {
            $result[$key] = $value;
        }

        assertEquals(
                [0 => 2, 1 => 4, 2 => 6, 3 => 8, 4 => 10, 5 => 12, 6 => 14],
                $result
        );
    }

    /**
     * @test
     */
    public function resultsAreReproducableWhenOperationIsReproducable()
    {
        $generator = new Generator(
                2,
                function($value) { return $value + 2; },
                function($value) { return $value < 15; }
        );
        $result1 = [];
        foreach ($generator as $key => $value) {
            $result1[$key] = $value;
        }

        $result2 = [];
        foreach ($generator as $key => $value) {
            $result2[$key] = $value;
        }

        assertEquals($result1, $result2);
    }

    /**
     * @test
     */
    public function infiniteGeneratorDoesStopOnlyWhenBreakOutOfLoop()
    {
        $i = 0;
        foreach (Generator::infinite(0, function($value) { return $value + 2; }) as $key => $value) {
            if (1000 > $key) {
                $i++;
                assertEquals($key * 2, $value);
            } else {
                break;
            }
        }

        assertEquals(1000, $i);
    }
}