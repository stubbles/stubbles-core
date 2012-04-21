<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\types\datespan;
use net\stubbles\lang\types\Date;
/**
 * Tests for net\stubbles\lang\types\datespan\Week.
 *
 * @group  lang
 * @group  lang_types
 * @group  lang_types_datespan
 */
class WeekTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function amountOfDaysIsAlwaysSeven()
    {
        $week = new Week('2007-05-14');
        $this->assertEquals(7, $week->getAmountOfDays());
    }

    /**
     * @test
     */
    public function getDaysReturnsAllSevenDays()
    {
        $week = new Week('2007-05-14');
        $days = $week->getDays();
        $this->assertEquals(7, count($days));
        $expectedDay = 14;
        foreach ($days as $day) {
            /* @var $day Day */
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Day', $day);
            $this->assertEquals($expectedDay, $day->asInt());
            $expectedDay++;
        }
    }

    /**
     * @test
     */
    public function weekWhichStartsAfterTodayIsInFuture()
    {
        $week = new Week('tomorrow');
        $this->assertTrue($week->isInFuture());
    }

    /**
     * @test
     */
    public function weekWhichStartsBeforeTodayIsNotInFuture()
    {
        $week = new Week('yesterday');
        $this->assertFalse($week->isInFuture());
    }

    /**
     * @test
     */
    public function weekWhichStartsTodayIsNotInFuture()
    {
        $week = new Week('now');
        $this->assertFalse($week->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDatesBeforeBeginnOfWeek()
    {
        $week = new Week('2009-01-05');
        $this->assertFalse($week->containsDate(new Date('2009-01-04')));
    }

    /**
     * @test
     */
    public function containsAllDaysOfThisWeek()
    {
        $week = new Week('2009-01-05');
        $this->assertTrue($week->containsDate(new Date('2009-01-05')));
        $this->assertTrue($week->containsDate(new Date('2009-01-06')));
        $this->assertTrue($week->containsDate(new Date('2009-01-07')));
        $this->assertTrue($week->containsDate(new Date('2009-01-08')));
        $this->assertTrue($week->containsDate(new Date('2009-01-09')));
        $this->assertTrue($week->containsDate(new Date('2009-01-10')));
        $this->assertTrue($week->containsDate(new Date('2009-01-11')));
    }

    /**
     * @test
     */
    public function doesNotContainDatesAfterEndOfWeek()
    {
        $week = new Week('2009-01-05');
        $this->assertFalse($week->containsDate(new Date('2009-01-12')));
    }

    /**
     * @test
     */
    public function stringRepresentationOfWeekContainsNumberOfWeek()
    {
        $week = new Week('2007-04-02');
        $this->assertEquals('14', $week->asString());
    }

    /**
     * @test
     */
    public function properStringConversion()
    {
        $week = new Week('2007-04-02');
        $this->assertEquals('14', (string) $week);
    }
}
?>