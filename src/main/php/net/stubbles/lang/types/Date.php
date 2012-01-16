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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
/**
 * Class for date/time handling.
 *
 * Shameless rip from the XP framework. ;-) Wraps PHP's internal date/time
 * functions for ease of use.
 *
 * @XmlTag(tagName='date')
 */
class Date extends BaseObject
{
    /**
     * internal date/time handle
     *
     * @type  \DateTime
     */
    protected $dateTime;

    /**
     * constructor
     *
     * Creates a new date object through either a
     * <ul>
     *   <li>integer - interpreted as timestamp</li>
     *   <li>string - parsed into a date</li>
     *   <li>DateTime object - will be used as is</li>
     *   <li>NULL - creates a date representing the current time</li>
     *  </ul>
     *
     * Timezone assignment works through these rules:
     * <ul>
     *   <li>If the time is given as string and contains a parseable timezone
     *       identifier that one is used.</li>
     *   <li>If no timezone could be determined, the timezone given by the
     *       second parameter is used.</li>
     *   <li>If no timezone has been given as second parameter, the system's
     *       default timezone is used.</li>
     *
     * @param   int|string|\DateTime  $dateTime  initial date
     * @param   TimeZone              $timeZone  initial timezone
     * @throws  IllegalArgumentException
     */
    public function __construct($dateTime = null, TimeZone $timeZone = null)
    {
        if (is_numeric($dateTime) === true) {
            $this->dateTime = date_create('@' . $dateTime, timezone_open('UTC'));
            if (false !== $this->dateTime) {
                date_timezone_set($this->dateTime, (null === $timeZone) ? (new \DateTimeZone(date_default_timezone_get())) : ($timeZone->getHandle()));
            }
        } elseif (is_string($dateTime) === true) {
            try {
                if (null === $timeZone) {
                    $this->dateTime = new \DateTime($dateTime);
                } else {
                    $this->dateTime = new \DateTime($dateTime, $timeZone->getHandle());
                }
            } catch (\Exception $e) {
                throw new IllegalArgumentException('Given datetime string ' . $dateTime . ' is not a valid date string.');
            }
        } else {
            $this->dateTime = $dateTime;
        }

        if (($this->dateTime instanceof \DateTime) === false) {
            throw new IllegalArgumentException('Datetime must be either unix timestamp, well-formed timestamp or instance of DateTime, but was ' . gettype($dateTime) . ' ' . $dateTime);
        }
    }

    /**
     * returns current date/time
     *
     * @param   TimeZone  $timeZone  initial timezone
     * @return  Date
     */
    public static function now(TimeZone $timeZone = null)
    {
        return new self(time(), $timeZone);
    }

    /**
     * returns internal date/time handle
     *
     * @return  \DateTime
     * @XmlIgnore
     */
    public function getHandle()
    {
        return clone $this->dateTime;
    }

    /**
     * returns way to change the date to another
     *
     * @return  DateModifier
     * @XmlIgnore
     */
    public function change()
    {
        return new DateModifier($this);
    }

    /**
     * returns timestamp for this date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function getTimestamp()
    {
        return (int) $this->dateTime->format('U');
    }

    /**
     * returns seconds of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function getSeconds()
    {
        return (int) $this->dateTime->format('s');
    }

    /**
     * returns minutes of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function getMinutes()
    {
        return (int) $this->dateTime->format('i');
    }

    /**
     * returns hours of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function getHours()
    {
        return (int) $this->dateTime->format('G');
    }

    /**
     * returns day of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function getDay()
    {
        return (int) $this->dateTime->format('d');
    }

    /**
     * returns month of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function getMonth()
    {
        return (int) $this->dateTime->format('m');
    }

    /**
     * returns year of current date/time
     *
     * @return  int
     * @XmlIgnore
     */
    public function getYear()
    {
        return (int) $this->dateTime->format('Y');
    }

    /**
     * returns offset to UTC in "+MMSS" notation
     *
     * @return  string
     * @XmlIgnore
     */
    public function getOffset()
    {
        return $this->dateTime->format('O');
    }

    /**
     * returns offset to UTC in seconds
     *
     * @return  int
     * @XmlIgnore
     */
    public function getOffsetInSeconds()
    {
        return (int) $this->dateTime->format('Z');
    }

    /**
     * checks whether this date is before a given date
     *
     * @param   Date  $date
     * @return  bool
     */
    public function isBefore(Date $date)
    {
        return $this->getTimestamp() < $date->getTimestamp();
    }

    /**
     * checks whether this date is after a given date
     *
     * @param   Date  $date
     * @return  bool
     */
    public function isAfter(Date $date)
    {
        return $this->getTimestamp() > $date->getTimestamp();
    }

    /**
     * returns time zone of this date
     *
     * @return  TimeZone
     * @XmlIgnore
     */
    public function getTimeZone()
    {
        return new TimeZone($this->dateTime->getTimezone());
    }

    /**
     * returns date as string
     *
     * @return  string
     * @XmlAttribute(attributeName='value')
     */
    public function asString()
    {
        return $this->format('Y-m-d H:i:sO');
    }

    /**
     * returns formatted date/time string
     *
     * @param   string    $format    format, see http://php.net/date
     * @param   TimeZone  $timeZone  target time zone of formatted string
     * @return  string
     */
    public function format($format, TimeZone $timeZone = null)
    {
        if (null !== $timeZone) {
            return $timeZone->translate($this)->format($format);
        }

        return $this->dateTime->format($format);
    }

    /**
     * checks whether a value is equal to the class
     *
     * @param   mixed  $compare
     * @return  bool
     */
    public function equals($compare)
    {
        if ($compare instanceof self) {
            return ($this->getTimestamp() === $compare->getTimestamp());
        }

        return false;
    }

    /**
     * returns a string representation of the class
     *
     * @return  string
     */
    public function __toString()
    {
        return self::getStringRepresentationOf($this, array('dateTime' => $this->format('Y-m-d H:i:sO')));
    }

    /**
     * make sure handle is cloned as well
     */
    public function __clone()
    {
        $this->dateTime = clone $this->dateTime;
    }
}
?>