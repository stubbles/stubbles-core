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
 * Tests for net\stubbles\lang\types\datespan\CustomDatespan.
 *
 * @group  lang
 * @group  lang_types
 * @group  lang_types_datespan
 */
class CustomDatespanTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function stringRepresentationOfDayContainsStartAndEndDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertEquals('04.04.2006 - 20.04.2006', $customDatespan->asString());
    }

    /**
     * @test
     */
    public function startDateCreatedFromStringInput()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $startDate      = $customDatespan->getStartDate();
        $this->assertInstanceOf('net\\stubbles\\lang\\types\\Date', $startDate);
        $this->assertEquals('2006-04-04 00:00:00' . $startDate->getOffset(),
                            $startDate->asString()
        );
    }

    /**
     * @test
     */
    public function endDateCreatedFromStringInput()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $endDate        = $customDatespan->getEndDate();
        $this->assertInstanceOf('net\\stubbles\\lang\\types\\Date', $endDate);
        $this->assertEquals('2006-04-20 23:59:59' . $endDate->getOffset(),
                            $endDate->asString()
        );
    }

    /**
     * @test
     */
    public function startDateIsSetToMidnight()
    {
        $customDatespan = new CustomDatespan(new Date('2006-04-04'), new Date('2006-04-20'));
        $startDate      = $customDatespan->getStartDate();
        $this->assertEquals('2006-04-04 00:00:00' . $startDate->getOffset(),
                            $startDate->asString()
        );
    }

    /**
     * @test
     */
    public function endDateIsSetToMidnight()
    {
        $customDatespan = new CustomDatespan(new Date('2006-04-04'), new Date('2006-04-20'));
        $endDate        = $customDatespan->getEndDate();
        $this->assertEquals('2006-04-20 23:59:59' . $endDate->getOffset(),
                            $endDate->asString()
        );
    }

    /**
     * @test
     */
    public function getDateSpansIntervalDay()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $days           = $customDatespan->getDateSpans();
        $this->assertEquals(14, count($days));
        foreach ($days as $day) {
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Day', $day);
        }
    }

    /**
     * @test
     */
    public function getDateSpansIntervalDayWithExplicitInterval()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $days           = $customDatespan->getDateSpans(DatespanInterval::$DAY);
        $this->assertEquals(14, count($days));
        foreach ($days as $day) {
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Day', $day);
        }
    }

    /**
     * test that the datespans are returned correctly
     *
     * @test
     */
    public function getDateSpansIntervalWeek()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $weeks          = $customDatespan->getDateSpans(DatespanInterval::$WEEK);
        $this->assertEquals(2, count($weeks));
        foreach ($weeks as $week) {
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Week', $week);
        }
    }

    /**
     * @test
     */
    public function getDateSpansIntervalMonth()
    {
        $customDatespan = new CustomDatespan('2008-01-01', '2008-12-31');
        $months          = $customDatespan->getDateSpans(DatespanInterval::$MONTH);
        $this->assertEquals(12, count($months));
        foreach ($months as $month) {
            $this->assertInstanceOf('net\\stubbles\\lang\\types\\datespan\\Month', $month);
        }
    }

    /**
     * @test
     */
    public function getDateSpansWithCustomIntervalReturnsListWithSelf()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $dateSpans = $customDatespan->getDateSpans(DatespanInterval::$CUSTOM);
        $this->assertEquals(1, count($dateSpans));
        $this->assertSame($dateSpans[0], $customDatespan);
    }

    /**
     * @test
     */
    public function isInFutureIfCurrentDateBeforeStartDate()
    {
        $customDatespan = new CustomDatespan('tomorrow', '+3 days');
        $this->assertTrue($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function isNotInFutureIfCurrentDateWithinSpan()
    {
        $customDatespan = new CustomDatespan('yesterday', '+3 days');
        $this->assertFalse($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function isNotInFutureIfCurrentDateAfterEndDate()
    {
        $customDatespan = new CustomDatespan('-3 days', 'yesterday');
        $this->assertFalse($customDatespan->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainDateBeforeStartDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertFalse($customDatespan->containsDate(new Date('2006-04-03')));
    }

    /**
     * @test
     */
    public function containsAllDatesInSpan()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        for ($day = 4; $day <= 20; $day++) {
            $this->assertTrue($customDatespan->containsDate(new Date('2006-04-' . $day)));
        }
    }

    /**
     * @test
     */
    public function doesNotContainDateAfterEndDate()
    {
        $customDatespan = new CustomDatespan('2006-04-04', '2006-04-20');
        $this->assertFalse($customDatespan->containsDate(new Date('2006-04-21')));
    }

    /**
     * @test
     */
    public function serializeAndUnserializeDoesNotDestroyStartAndEndDate()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $serialized     = serialize($customDatespan);
        $unserialized   = unserialize($serialized);
        $this->assertTrue($customDatespan->getStartDate()->equals($unserialized->getStartDate()));
        $this->assertTrue($customDatespan->getEndDate()->equals($unserialized->getEndDate()));
    }

    /**
     * @test
     */
    public function returnsAmountOfDaysInDatespan()
    {
        $customDatespan = new CustomDatespan('2007-05-14', '2007-05-27');
        $this->assertEquals(14, $customDatespan->getAmountOfDays());
    }
}
?>