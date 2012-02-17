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
 * Tests for net\stubbles\lang\types\datespan\Month.
 *
 * @group  lang
 * @group  lang_types
 * @group  lang_types_datespan
 */
class MonthTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function stringRepresentationContainsYearAndMonth()
    {
        $month = new Month(2007, 4);
        $this->assertEquals('2007-04', $month->asString());
    }

    /**
     * @test
     */
    public function usesCurrentYearIfNotGiven()
    {
        $month = new Month(null, 4);
        $this->assertEquals(date('Y') . '-04', $month->asString());
    }

    /**
     * @test
     */
    public function usesCurrentMonthIfNotGiven()
    {
        $month = new Month(2007);
        $this->assertEquals('2007-' . date('m'), $month->asString());
    }

    /**
     * @test
     */
    public function usesCurrentYearAndMonthIfNotGiven()
    {
        $month = new Month();
        $this->assertEquals(date('Y') . '-' . date('m'), $month->asString());
    }

    /**
     * data provider for getDateSpansWithDayIntervalReturnsAllDaysInMonth()
     *
     * @return  array
     */
    public function getDayMonth()
    {
        return array(array(2007, 4, 30),
                     array(2007, 3, 31),
                     array(2007, 2, 28),
                     array(2008, 2, 29)
        );
    }

    /**
     * @param  int  $year      year to get days for
     * @param  int  $month     month to get days for
     * @param  int  $dayCount  amount of days in this month
     * @test
     * @dataProvider  getDayMonth
     */
    public function getDateSpansWithDayIntervalReturnsAllDaysInMonth($year, $month, $dayCount)
    {
        $month = new Month($year, $month);
        $days  = $month->getDateSpans();
        $this->assertEquals($dayCount, count($days));
        $expectedDay = 1;
        foreach ($days as $day) {
            /* @var $day Day */
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Day', $day);
            $this->assertEquals($expectedDay, $day->getStartDate()->getDay());
            $expectedDay++;
        }
    }

    /**
     * @test
     */
    public function getDateSpansWithWeeksIntervalReturnsAllWeeks()
    {
        $month = new Month(2007, 4);
        $weeks = $month->getDateSpans(DatespanInterval::$WEEK);
        $this->assertGreaterThan(0, count($weeks));
        foreach ($weeks as $week) {
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Week', $week);
        }
    }

    /**
     * @test
     */
    public function getDateSpansWithMonthIntervalReturnsListWithSelf()
    {
        $month = new Month();
        $dateSpans = $month->getDateSpans(DatespanInterval::$MONTH);
        $this->assertEquals(1, count($dateSpans));
        $this->assertSame($dateSpans[0], $month);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function yearIntervalThrowsIllegalArgumentException()
    {
        $month = new Month();
        $month->getDateSpans(DatespanInterval::$YEAR);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function customIntervalThrowsIllegalArgumentException()
    {
        $month = new Month();
        $month->getDateSpans(DatespanInterval::$CUSTOM);
    }

    /**
     * @test
     */
    public function monthInNextYearIsInFuture()
    {
        $month = new Month(date('Y') + 1, 7);
        $this->assertTrue($month->isInFuture());
    }

    /**
     * @test
     */
    public function monthInLastYearIsNotInFuture()
    {
        $month = new Month(date('Y') - 1, 7);
        $this->assertFalse($month->isInFuture());
    }

    /**
     * @test
     */
    public function currentMonthIsNotInFuture()
    {
        $month = new Month(date('Y'), date('m'));
        $this->assertFalse($month->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateFromPreviousMonth()
    {
        $month = new Month(2007, 4);
        $this->assertFalse($month->containsDate(new Date('2007-03-31')));
    }

    /**
     * @test
     */
    public function doesContainAllDatesForThisMonth()
    {
        $month = new Month(2007, 4);
        for ($day = 1; $day < 31; $day++) {
            $this->assertTrue($month->containsDate(new Date('2007-04-' . $day)));
        }
    }

    /**
     * @test
     */
    public function doesNotContainDateFromLaterMonth()
    {
        $month = new Month(2007, 4);
        $this->assertFalse($month->containsDate(new Date('2007-05-01')));
    }

    /**
     * @param  int  $year      year to get days for
     * @param  int  $month     month to get days for
     * @param  int  $dayCount  amount of days in this month
     * @test
     * @dataProvider  getDayMonth
     */
    public function getAmountOfDaysInMonth($year, $month, $dayCount)
    {
        $month = new Month($year, $month);
        $this->assertEquals($dayCount, $month->getAmountOfDays());
    }
}
?>