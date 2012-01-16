<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\types;
/**
 * Tests for net\stubbles\lang\types\TimeZone.
 *
 * @group  lang
 * @group  lang_types
 */
class TimeZoneTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  TimeZone
     */
    protected $timeZone;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->timeZone = new TimeZone('Europe/Berlin');
    }

    /**
     * name of the time zone should be returned
     *
     * @test
     */
    public function name()
    {
        $this->assertEquals('Europe/Berlin', $this->timeZone->getName());
    }

    /**
     * offset in summer time should be 2 hours
     *
     * @test
     */
    public function offsetDST()
    {
        $this->assertEquals('+0200', $this->timeZone->getOffset(new Date('2007-08-21')));
        $this->assertEquals(7200, $this->timeZone->getOffsetInSeconds(new Date('2007-08-21')));
    }

    /**
     * offset in non-summer time should be 1 hour
     *
     * @test
     */
    public function offsetNoDST()
    {
        $this->assertEquals('+0100', $this->timeZone->getOffset(new Date('2007-01-21')));
        $this->assertEquals(3600, $this->timeZone->getOffsetInSeconds(new Date('2007-01-21')));
    }

    /**
     * offset in seconds for current date is 3600 seconds or 7200 seconds, depending
     * whether we are in dst or not
     *
     * @test
     */
    public function offsetForCurrentDateIs3600SecondsOr7200SecondsDependingWhetherInDstOrNot()
    {
        $offset = $this->timeZone->getOffsetInSeconds();
        $this->assertTrue((3600 === $offset || 7200 === $offset));
    }

    /**
     * offset for some time zones is just an half hour more
     *
     * @test
     */
    public function offsetWithHalfHourDST()
    {
        $timeZone = new TimeZone('Australia/Adelaide');
        $this->assertEquals('+1030', $timeZone->getOffset(new Date('2007-01-21')));
    }

    /**
     * offset for some time zones is just an half hour more
     *
     * @test
     */
    public function offsetWithHalfHourNoDST()
    {
        $timeZone = new TimeZone('Australia/Adelaide');
        $this->assertEquals('+0930', $timeZone->getOffset(new Date('2007-08-21')));
    }

    /**
     * a date should be translatable into a date of our current time zone
     *
     * @test
     */
    public function translate()
    {
        $date = new Date('2007-01-01 00:00 Australia/Sydney');
        $this->assertEquals(new Date('2006-12-31 14:00:00 Europe/Berlin'), $this->timeZone->translate($date));
    }

    /**
     * @test
     */
    public function timeZonesHavingDstShouldBeMarkedAsSuch()
    {
        $this->assertTrue($this->timeZone->hasDst());
    }

    /**
     * @test
     */
    public function timeZonesAreEqualsIfTheyRepresentTheSameTimeZoneString()
    {
        $this->assertTrue($this->timeZone->equals($this->timeZone));
        $this->assertTrue($this->timeZone->equals(new TimeZone('Europe/Berlin')));
        $this->assertFalse($this->timeZone->equals(new TimeZone('Australia/Adelaide')));
        $this->assertFalse($this->timeZone->equals(new \stdClass()));
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidTimeZoneValueThrowsIllegalArgumentExceptionOnConstruction()
    {
        $timeZone = new TimeZone(500);
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function nonExistingTimeZoneValueThrowsIllegalArgumentExceptionOnConstruction()
    {
        $timeZone = new TimeZone('Europe/Karlsruhe');
    }

    /**
     * @test
     */
    public function toStringConversionCreatesReadableRepresentation()
    {
        $this->assertEquals("net\\stubbles\\lang\\types\\TimeZone {\n    timeZone(string): Europe/Berlin\n}\n",
                            (string) $this->timeZone
        );
    }
}
?>