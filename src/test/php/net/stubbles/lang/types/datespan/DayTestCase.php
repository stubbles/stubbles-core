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
 * Tests for net\stubbles\lang\types\datespan\Day.
 *
 * @group  lang
 * @group  lang_types
 * @group  lang_types_datespan
 */
class DayTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function startDateSetToMidNight()
    {
        $day = new Day('2007-04-04');
        $this->assertEquals('2007-04-04 00:00:00' . $day->getStart()->getOffset(),
                            $day->getStart()->asString()
        );
    }

    /**
     * @test
     */
    public function endDateSetToOneSecondBeforeMidNight()
    {
        $day = new Day('2007-04-04');
        $this->assertEquals('2007-04-04 23:59:59' . $day->getEnd()->getOffset(),
                            $day->getEnd()->asString()
        );
    }

    /**
     * @test
     */
    public function amountOfDaysIsAlwaysOne()
    {
        $day = new Day('2007-04-04');
        $this->assertEquals(1, $day->getAmountOfDays());
    }

    /**
     * @test
     */
    public function getDaysReturnsListWithSelf()
    {
        $day       = new Day('2007-05-14');
        $dateSpans = $day->getDays();
        $this->assertEquals(1, count($dateSpans));
        $this->assertSame($dateSpans[0], $day);
    }

    /**
     * @test
     */
    public function tomorrowIsNotToday()
    {
        $day = new Day('tomorrow');
        $this->assertFalse($day->isToday());
    }

    /**
     * @test
     */
    public function yesterdayIsNotToday()
    {
        $day = new Day('yesterday');
        $this->assertFalse($day->isToday());
    }

    /**
     * @test
     */
    public function nowIsToday()
    {
        $day = new Day('now');
        $this->assertTrue($day->isToday());
    }

    /**
     * @test
     */
    public function tomorrowIsFuture()
    {
        $day = new Day('tomorrow');
        $this->assertTrue($day->isInFuture());
    }

    /**
     * @test
     */
    public function yesterdayIsNotFuture()
    {
        $day = new Day('yesterday');
        $this->assertFalse($day->isInFuture());
    }

    /**
     * @test
     */
    public function todayIsNotFuture()
    {
        $day = new Day('now');
        $this->assertFalse($day->isInFuture());
        $day = new Day();
        $this->assertFalse($day->isInFuture());
    }

    /**
     * @test
     */
    public function doesNotContainTheDayBefore()
    {
        $day = new Day('2007-04-04');
        $this->assertFalse($day->containsDate(new Date('2007-04-03')));
    }

    /**
     * @test
     */
    public function doesContainTheExactDay()
    {
        $day = new Day('2007-04-04');
        $this->assertTrue($day->containsDate(new Date('2007-04-04')));
    }

    /**
     * @test
     */
    public function doesNotContainTheDayAfter()
    {
        $day = new Day('2007-04-04');
        $this->assertFalse($day->containsDate(new Date('2007-04-05')));
    }

    /**
     * @test
     */
    public function stringRepresentationOfDayContainsNameOfDayAndDate()
    {
        $day = new Day('2007-04-04');
        $this->assertEquals('2007-04-04', $day->asString());
    }

    /**
     * @test
     */
    public function properStringConversion()
    {
        $day = new Day('2007-04-04');
        $this->assertEquals('2007-04-04', (string) $day);
    }

    /**
     * @test
     */
    public function asIntReturnsRepresentationOfDayWithinMonth()
    {
        $day = new Day('2007-05-14');
        $this->assertEquals(14, $day->asInt());
    }

    /**
     * @test
     */
    public function formatReturnsOtherStringRepresentation()
    {
        $day = new Day('2007-05-14');
        $this->assertEquals('Monday, 14.05.2007', $day->format('l, d.m.Y'));
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function tomorrowCreatesInstanceForTomorrow()
    {
        $this->assertEquals(
                date('Y-m-d', strtotime('tomorrow')),
                Day::tomorrow()->asString()
        );
    }

    /**
     * @test
     * @since  3.5.1
     */
    public function yesterdayCreatesInstanceForYesterday()
    {
        $this->assertEquals(
                date('Y-m-d', strtotime('yesterday')),
                Day::yesterday()->asString()
        );
    }
}
