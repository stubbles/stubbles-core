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
class YearTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function stringRepresentationContainsYear()
    {
        $year = new Year(2007);
        $this->assertEquals('2007', $year->asString());
    }

    /**
     * @test
     */
    public function usesCurrentYearIfNotGiven()
    {
        $year = new Year();
        $this->assertEquals(date('Y'), $year->asString());
    }

    /**
     * data provider for getDateSpansWithDayIntervalReturnsAllDaysInYear()
     *
     * @return  array
     */
    public function getDayYear()
    {
        return array(array(2007, 365),
                     array(2008, 366)
        );
    }

    /**
     * @param  int  $year      year to get days for
     * @param  int  $dayCount  amount of days in this year
     * @test
     * @dataProvider  getDayYear
     */
    public function getDateSpansWithDayIntervalReturnsAllDaysInYear($year, $dayCount)
    {
        $year = new Year($year);
        $days = $year->getDateSpans();
        $this->assertEquals($dayCount, count($days));
        foreach ($days as $day) {
            /* @var $day Day */
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Day', $day);
        }
    }

    /**
     * @test
     */
    public function getDateSpansWithWeeksIntervalReturnsAllWeeks()
    {
        $year  = new Year(2007);
        $weeks = $year->getDateSpans(DatespanInterval::$WEEK);
        $this->assertGreaterThan(0, count($weeks));
        foreach ($weeks as $week) {
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Week', $week);
        }
    }

    /**
     * @test
     */
    public function getDateSpansWithMonthIntervalReturnsAllMonth()
    {
        $year   = new Year(2007);
        $months = $year->getDateSpans(DatespanInterval::$MONTH);
        $this->assertEquals(12, count($months));
        $expectedMonth = 1;
        foreach ($months as $month) {
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Month', $month);
            $this->assertEquals('2007-' . $this->prefixInt($expectedMonth), $month->asString());
            $expectedMonth++;
        }
    }

    /**
     * prefixes ints smaller than 10 with a zero
     *
     * @param   int     $int
     * @return  string
     */
    private function prefixInt($int)
    {
        if (10 <= $int) {
            return $int;
        }

        return 0 . $int;
    }

    /**
     * @test
     */
    public function getDateSpansWithYearIntervalReturnsListWithSelf()
    {
        $year = new Year();
        $dateSpans = $year->getDateSpans(DatespanInterval::$YEAR);
        $this->assertEquals(1, count($dateSpans));
        $this->assertSame($dateSpans[0], $year);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function customIntervalThrowsIllegalArgumentException()
    {
        $year = new Year();
        $year->getDateSpans(DatespanInterval::$CUSTOM);
    }

    /**
     * @test
     */
    public function nextYearIsInFuture()
    {
        $year = new Year(date('Y') + 1);
        $this->assertTrue($year->isInFuture());
    }

    /**
     * @test
     */
    public function lastYearIsNotInFuture()
    {
        $year = new Year(date('Y') - 1);
        $this->assertFalse($year->isInFuture());
    }

    /**
     * @test
     */
    public function currentYearIsNotInFuture()
    {
        $year = new Year();
        $this->assertFalse($year->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateFromPreviousYear()
    {
        $year = new Year(2007);
        $this->assertFalse($year->containsDate(new Date('2006-12-31')));
    }

    /**
     * @test
     */
    public function doesContainAllDatesForThisYear()
    {
        $year = new Year(2007);
        for ($month = 1; $month <= 12; $month++) {
            $days = Month::newInstance(2007, $month)->getAmountOfDays();
            for ($day = 1; $day <= $days; $day++) {
                $this->assertTrue($year->containsDate(new Date('2007-' . $month . '-' . $day)));
            }
        }
    }

    /**
     * @test
     */
    public function doesNotContainDateFromLaterYear()
    {
        $year = new Year(2007);
        $this->assertFalse($year->containsDate(new Date('2008-01-01')));
    }

    /**
     * @test
     */
    public function amountOfDaysIs366ForLeapYears()
    {
        $year = new Year(2008);
        $this->assertEquals(366, $year->getAmountOfDays());
    }

    /**
     * @test
     */
    public function amountOfDaysIs365ForNonLeapYears()
    {
        $year = new Year(2007);
        $this->assertEquals(365, $year->getAmountOfDays());
    }

    /**
     * @test
     */
    public function isLeapYearReturnsTrueForLeapYears()
    {
        $year = new Year(2008);
        $this->assertTrue($year->isLeapYear());
    }

    /**
     * @test
     */
    public function isLeapYearReturnsFalseForNonLeapYears()
    {
        $year = new Year(2007);
        $this->assertFalse($year->isLeapYear());
    }
}
?>