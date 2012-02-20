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
 * Datespan that represents a single day.
 */
class Day extends AbstractDatespan implements Datespan
{
    /**
     * original date
     *
     * @type  Date
     */
    private $date;
    /**
     * constructor
     *
     * @param  string|Date  $day  day that the span covers
     */
    public function __construct($day = null)
    {
        if (null === $day) {
            $day = Date::now();
        } elseif (!($day instanceof Date)) {
            $day = new Date($day);
        }

        parent::__construct($day, $day);
        $this->date = $day;
    }

    /**
     * returns amount of days on this day
     *
     * Well, the amount of days on a day is obviously always one.
     *
     * @return  int
     */
    public function getAmountOfDays()
    {
        return 1;
    }

    /**
     * returns list of days
     *
     * @return  Day[]
     */
    public function getDays()
    {
        return array($this);
    }

    /**
     * checks if it represents the current day
     *
     * @return  bool
     */
    public function isToday()
    {
        return $this->date->format('Y-m-d') === Date::now($this->date->getTimeZone())->format('Y-m-d');
    }

    /**
     * returns a string representation of the day
     *
     * @return  string
     */
    public function asString()
    {
        return $this->date->format('Y-m-d');
    }

    /**
     * returns number of current day within month
     *
     * @return  int
     */
    public function asInt()
    {
        return (int) $this->date->format('d');
    }

    /**
     * returns formatted date/time string
     *
     * Please note that the returned string may also contain a time, depending
     * on your format string.
     *
     * @param   string  $format  format, see http://php.net/date
     * @return  string
     */
    public function format($format)
    {
        return $this->date->format($format);
    }
}
?>