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
        $this->assertEquals('2007-04-04 00:00:00' . $day->getStartDate()->getOffset(),
                            $day->getStartDate()->asString()
        );
    }

    /**
     * @test
     */
    public function endDateSetToOneSecondBeforeMidNight()
    {
        $day = new Day('2007-04-04');
        $this->assertEquals('2007-04-04 23:59:59' . $day->getEndDate()->getOffset(),
                            $day->getEndDate()->asString()
        );
    }

    /**
     * @test
     */
    public function stringRepresentationOfDayContainsNameOfDayAndDate()
    {
        $day = new Day('2007-04-04');
        $this->assertEquals('Wednesday, 04.04.2007', $day->asString());
    }

    /**
     * @test
     */
    public function getDateSpansReturnsListWithSelf()
    {
        $day       = new Day('2007-05-14');
        $dateSpans = $day->getDateSpans();
        $this->assertEquals(1, count($dateSpans));
        $this->assertSame($dateSpans[0], $day);
    }

    /**
     * @test
     */
    public function getDateSpansWithDayIntervalReturnsListWithSelf()
    {
        $day       = new Day('2007-05-14');
        $dateSpans = $day->getDateSpans(DatespanInterval::$DAY);
        $this->assertEquals(1, count($dateSpans));
        $this->assertSame($dateSpans[0], $day);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function weekIntervalThrowsIllegalArgumentException()
    {
        $day = new Day('2007-05-14');
        $day->getDateSpans(DatespanInterval::$WEEK);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function monthIntervalThrowsIllegalArgumentException()
    {
        $day = new Day('2007-05-14');
        $day->getDateSpans(DatespanInterval::$MONTH);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function yearIntervalThrowsIllegalArgumentException()
    {
        $day = new Day('2007-05-14');
        $day->getDateSpans(DatespanInterval::$YEAR);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function customIntervalThrowsIllegalArgumentException()
    {
        $day = new Day('2007-05-14');
        $day->getDateSpans(DatespanInterval::$CUSTOM);
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
    public function amountOfDaysIsAlwaysOne()
    {
        $day = new Day('2007-04-04');
        $this->assertEquals(1, $day->getAmountOfDays());
    }
}
?>