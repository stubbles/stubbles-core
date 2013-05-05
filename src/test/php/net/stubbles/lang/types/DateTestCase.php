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
 * Helper class for the test.
 */
class DateHandleDeliverer extends Date
{
    /**
     * returns handle
     *
     * @return  DateTime
     */
    public static function deliverHandle(Date $date)
    {
        return $date->dateTime;
    }
}
/**
 * Tests for net\stubbles\lang\types\Date.
 *
 * @group  lang
 * @group  lang_types
 */
class DateTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * origin time zone for restoring in tearDown()
     *
     * @type  string
     */
    protected $originTimeZone;
    /**
     * current date/time as timestamp
     *
     * @type  int
     */
    protected $timestamp;
    /**
     * instance to test
     *
     * @type  Date
     */
    protected $date;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->originTimeZone = date_default_timezone_get();
        date_default_timezone_set('GMT');
        $this->timestamp = time();
        $this->date      = new Date($this->timestamp);
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        date_default_timezone_set($this->originTimeZone);
    }

    /**
     * helper assertion for the test
     *
     * @param  string    $expected  expected date as string
     * @param  net\stubbles\lang\types\Date  $date      date to check for equality to expected date
     */
    protected function assertDateEquals($expected, Date $date)
    {
        $this->assertEquals(date_format(date_create($expected), 'U'),
                            date_format($date->getHandle(), 'U'),
                            'Expected ' . $expected . ' but got ' . $date->format('c')
        );
    }

    /**
     * construction should work with time zone as part of a well-formed time string
     *
     * @test
     */
    public function constructorParseWithoutTz()
    {
        $this->assertTrue(new Date('2007-01-01 01:00:00 Europe/Berlin') instanceof Date);
    }

    /**
     * construction should work with a unix timestamp
     *
     * @test
     */
    public function constructorUnixtimestampWithoutTz()
    {
        $this->assertDateEquals('2007-08-23T12:35:47+00:00', new Date(1187872547));
    }

    /**
     * construction should work with a unix timestamp and a specified time zone
     *
     * @test
     */
    public function constructorUnixtimestampWithTz()
    {
        $this->assertDateEquals('2007-08-23T12:35:47+00:00', new Date(1187872547, new TimeZone('Europe/Berlin')));
    }

    /**
     * time zone info should be parsed correct
     *
     * @test
     */
    public function constructorParseTz()
    {
        $date = new Date('2007-01-01 01:00:00 Europe/Berlin');
        $this->assertEquals('Europe/Berlin', $date->getTimeZone()->getName());
        $this->assertDateEquals('2007-01-01T00:00:00+00:00', $date);

        $date = new Date('2007-01-01 01:00:00 Europe/Berlin', new TimeZone('Europe/Athens'));
        $this->assertEquals('Europe/Berlin', $date->getTimeZone()->getName());
        $this->assertDateEquals('2007-01-01T00:00:00+00:00', $date);

        $date= new Date('2007-01-01 01:00:00', new TimeZone('Europe/Athens'));
        $this->assertEquals('Europe/Athens', $date->getTimeZone()->getName());
        $this->assertDateEquals('2006-12-31T23:00:00+00:00', $date);
    }

    /**
     * a timezone should not be reported erroneously if it actually could not be
     * parsed out of a string.
     *
     * @test
     */
    public function noDiscreteTimeZone()
    {
        $date = new Date('2007-11-04 14:32:00+1000');
        $this->assertEquals('+1000', $date->getOffset());
        $this->assertEquals(36000, $date->getOffsetInSeconds());
    }

    /**
     * correct time zone should be recognized
     *
     * @test
     */
    public function constructorParseNoTz()
    {
        $date= new Date('2007-01-01 01:00:00', new TimeZone('Europe/Athens'));
        $this->assertEquals('Europe/Athens', $date->getTimeZone()->getName());

        $date= new Date('2007-01-01 01:00:00');
        $this->assertEquals('GMT', $date->getTimeZone()->getName());
    }

    /**
     * date handling should work as expected
     *
     * @test
     */
    public function dateHandling()
    {
        $this->assertEquals($this->timestamp, $this->date->getTimestamp());
        $this->assertEquals(date('r', $this->timestamp), $this->date->format('r'));
        $this->assertTrue($this->date->isAfter(new Date('yesterday')));
        $this->assertTrue($this->date->isBefore(new Date('tomorrow')));
    }

    /**
     * dates before unix epoch should be handled
     *
     * @test
     */
    public function preUnixEpoch()
    {
        $this->assertDateEquals('1969-12-31T00:00:00+00:00', new Date('31.12.1969 00:00 GMT'));
    }

    /**
     * dates before the year 1582 are 11 days off, but we do not support this
     *
     * Actually, PHP does not support this and we did not want to build a
     * workaround ourself.
     *
     * Quoting Wikipedia:
     * The last day of the Julian calendar was Thursday October 4, 1582 and this
     * was followed by the first day of the Gregorian calendar, Friday October
     * 15, 1582 (the cycle of weekdays was not affected).
     *
     * @test
     * @see   http://en.wikipedia.org/wiki/Gregorian_calendar
     */
    public function pre1582()
    {
        //$this->assertDateEquals('1499-12-21T00:00:00+00:00', new Date('01.01.1500 00:00 GMT'));
        $this->assertDateEquals('1500-01-01T00:00:00+00:00', new Date('01.01.1500 00:00 GMT'));
    }

    /**
     * dates before the year 1752 are 11 days off, but we do not support this
     *
     * Actually, PHP does not support this and we did not want to build a
     * workaround ourself.
     *
     * Quoting Wikipedia:
     * The Kingdom of Great Britain and thereby the rest of the British Empire
     * (including the eastern part of what is now the United States) adopted the
     * Gregorian calendar in 1752 under the provisions of the Calendar Act 1750;
     * by which time it was necessary to correct by eleven days (Wednesday,
     * September 2, 1752 being followed by  Thursday, September 14, 1752) to
     * account for February 29, 1700 (Julian).
     *
     * @test
     * @see   http://en.wikipedia.org/wiki/Gregorian_calendar
     */
    public function calendarAct1750()
    {
        //$this->assertDateEquals('1753-01-01T00:00:00+00:00', new Date('01.01.1753 00:00 GMT'));
        //$this->assertDateEquals('1751-12-21T00:00:00+00:00', new Date('01.01.1752 00:00 GMT'));
        $this->assertDateEquals('1753-01-01T00:00:00+00:00', new Date('01.01.1753 00:00 GMT'));
        $this->assertDateEquals('1752-01-01T00:00:00+00:00', new Date('01.01.1752 00:00 GMT'));
    }

    /**
     * setting of correct hours when date was given troughthe AM/PM format
     *
     * @test
     */
    public function anteAndPostMeridiem()
    {
        $date = new Date('May 28 1980 1:00AM');
        $this->assertEquals(1, $date->getHours(), '1:00AM != 1h');
        $date = new Date('May 28 1980 12:00AM');
        $this->assertEquals(0, $date->getHours(), '12:00AM != 0h');
        $date = new Date('May 28 1980 1:00PM');
        $this->assertEquals(13, $date->getHours(), '1:00PM != 13h');
        $date = new Date('May 28 1980 12:00PM');
        $this->assertEquals(12, $date->getHours(), '12:00PM != 12h');
    }

    /**
     * setting of correct hours when date was given troughthe AM/PM format
     *
     * @test
     */
    public function anteAndPostMeridiemInMidage()
    {
        $date = new Date('May 28 1580 1:00AM');
        $this->assertEquals(1, $date->getHours(), '1:00AM != 1h');
        $date = new Date('May 28 1580 12:00AM');
        $this->assertEquals(0, $date->getHours(), '12:00AM != 0h');
        $date = new Date('May 28 1580 1:00PM');
        $this->assertEquals(13, $date->getHours(), '1:00PM != 13h');
        $date = new Date('May 28 1580 12:00PM');
        $this->assertEquals(12, $date->getHours(), '12:00PM != 12h');
    }

    /**
     * date parsing in different formats in pre 1970 epoch.
     *
     * @test
     */
    public function pre1970()
    {
        $this->assertDateEquals('1969-02-01T00:00:00+00:00', new Date('01.02.1969'));
        $this->assertDateEquals('1969-02-01T00:00:00+00:00', new Date('1969-02-01'));
        $this->assertDateEquals('1969-02-01T00:00:00+00:00', new Date('1969-02-01 12:00AM'));
    }

    /**
     * serialize()/unserialize() should preserve all data
     *
     * @test
     */
    public function serialization()
    {
        $original = new Date('2007-07-18T09:42:08 Europe/Athens');
        $copy     = unserialize(serialize($original));
        $this->assertDateEquals($original->format('c'), $copy);
    }

    /**
     * time zone should be preserved during serialize()/unserialize()
     *
     * @test
     */
    public function timeZoneSerialization()
    {
        date_default_timezone_set('Europe/Athens');
        $date = new Date('2007-11-20 21:45:33 Europe/Berlin');
        $this->assertEquals('Europe/Berlin', $date->getTimeZone()->getName());
        $this->assertEquals('+0100', $date->getOffset());

        $copy = unserialize(serialize($date));
        $this->assertEquals('+0100', $copy->getOffset());
    }

    /**
     * timezone functionality
     *
     * @test
     */
    public function handlingOfTimezone()
    {
        $date = new Date('2007-07-18T09:42:08 Europe/Athens');
        $this->assertEquals('Europe/Athens', $date->getTimeZone()->getName());
        $this->assertEquals(3 * 3600, $date->getTimeZone()->getOffsetInSeconds($date));
    }

    /**
     * representation of string is working deterministicly
     *
     * @test
     */
    public function testTimestamp()
    {
        date_default_timezone_set('Europe/Berlin');
        $d1 = new Date('1980-05-28 06:30:00 Europe/Berlin');
        $d2 = new Date(328336200);

        $this->assertEquals($d1, $d2);
        $this->assertEquals($d2, new Date($d2->format('Y-m-d H:i:se')));
    }

    /**
     * dates created with a timestamp are in correct timezone ifa timezone has been passed
     *
     * @test
     */
    public function timestampWithTZ()
    {
        $date = new Date(328336200, new TimeZone('Australia/Sydney'));
        $this->assertEquals('Australia/Sydney', $date->getTimeZone()->getName());
    }

    /**
     * string formatting preserves same timezone after serialization
     *
     * @test
     */
    public function stringOutputPreserved()
    {
        $date = unserialize(serialize(new Date('2007-11-10 20:15+0100')));
        $this->assertEquals('2007-11-10 20:15:00+0100', $date->format('Y-m-d H:i:sO'));
        $this->assertEquals('2007-11-10 19:15:00+0000', $date->format('Y-m-d H:i:sO', new TimeZone()));
    }

    /**
     * now() constructs date with current time
     *
     * @test
     */
    public function nowConstructsCurrentDate()
    {
        $date = Date::now();
        $this->assertInstanceOf('net\\stubbles\\lang\\types\\Date', $date);
        $this->assertLessThanOrEqual(time(), $date->getTimestamp());
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug267
     */
    public function nowConstructsCurrentDateInUtcTimeZone()
    {
        $this->assertEquals('UTC',
                            Date::now()->getTimeZone()->getName()
        );
    }

    /**
     * @test
     * @since  1.7.0
     * @group  bug267
     */
    public function nowConstructsCurrentDateWithTimeZone()
    {
        $this->assertEquals('Europe/London',
                            Date::now(new TimeZone('Europe/London'))->getTimeZone()->getName()
        );
    }

    /**
     * single date and time parts should be returned
     *
     * @test
     */
    public function partsReturned()
    {
        // 2007-08-23T12:35:47+00:00
        $date = new Date(1187872547);
        $this->assertEquals(47, $date->getSeconds());
        $this->assertEquals(35, $date->getMinutes());
        $this->assertEquals(12, $date->getHours());
        $this->assertEquals(23, $date->getDay());
        $this->assertEquals(8, $date->getMonth());
        $this->assertEquals(2007, $date->getYear());
    }

    /**
     * same dates should be equal
     *
     * @test
     */
    public function sameDatesShouldBeEqual()
    {
        $date = new Date('31.12.1969 00:00 GMT');
        $this->assertFalse($date->equals('foo'));
        $this->assertTrue($date->equals(new Date('1969-12-31T00:00:00+00:00')));
        $this->assertFalse($date->equals(new Date('1969-12-01T00:00:00+00:00')));
    }

    /**
     * handle must be cloned as well
     *
     * @test
     */
    public function cloneClonesHandleAsWell()
    {
        $date       = new Date('31.12.1969 00:00 GMT');
        $clonedDate = clone $date;
        $this->assertNotSame(DateHandleDeliverer::deliverHandle($date), DateHandleDeliverer::deliverHandle($clonedDate));
    }

    /**
     * failing constructoon throws a illegal argument exception
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function failingConstructionThrowsIllegalArgumentException()
    {
        new Date(null);
    }

    /**
     * failing constructoon throws a illegal argument exception
     *
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidDateStringhrowsIllegalArgumentException()
    {
        new Date('invalid');
    }

    /**
     * ensure a readable string representation is created
     *
     * @test
     */
    public function toStringConvertsDateTimePropertyIntoReadableDateRepresentation()
    {
        $date = new Date('31.12.1969 00:00 GMT');
        $this->assertEquals('1969-12-31 00:00:00+0000',
                            (string) $date
        );
    }

    /**
     * @test
     */
    public function asStringReturnsStringValue()
    {
        $date = new Date('2012-01-21 21:00:00');
        $this->assertEquals('2012-01-21 21:00:00' . $date->getOffset(),
                            $date->asString()
        );
    }

    /**
     * creates reflection instance for class under test
     *
     * @return  \net\stubbles\lang\reflect\ReflectionClass
     */
    private function getDateReflectionClass()
    {
        return \net\stubbles\lang\reflect('net\stubbles\lang\types\Date');
    }

    /**
     * @test
     */
    public function classIsAnnotatedWithXmlTag()
    {
        $this->assertTrue($this->getDateReflectionClass()->hasAnnotation('XmlTag'));
    }

    /**
     * @test
     */
    public function getHandleIsAnnotatedWithXmlIgnore()
    {
        $this->assertTrue($this->getDateReflectionClass()
                               ->getMethod('getHandle')
                               ->hasAnnotation('XmlIgnore')
        );
    }

    /**
     * returns list of methods which should be annotated with XmlIgnore
     *
     * @return  array
     */
    public function getXmlIgnoredMethods()
    {
        return array(array('getHandle'),
                     array('change'),
                     array('getTimestamp'),
                     array('getSeconds'),
                     array('getMinutes'),
                     array('getHours'),
                     array('getDay'),
                     array('getMonth'),
                     array('getYear'),
                     array('getOffset'),
                     array('getOffsetInSeconds'),
                     array('getTimeZone')
        );
    }
    /**
     * @test
     * @dataProvider  getXmlIgnoredMethods
     */
    public function methodIsAnnotatedWithXmlIgnore($method)
    {
        $this->assertTrue($this->getDateReflectionClass()
                               ->getMethod($method)
                               ->hasAnnotation('XmlIgnore')
        );
    }

    /**
     * @test
     */
    public function asStringIsAnnotatedWithXmlAttribute()
    {
        $this->assertTrue($this->getDateReflectionClass()
                               ->getMethod('asString')
                               ->hasAnnotation('XmlAttribute')
        );
    }
}
?>