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
 *
 * @api
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
     * @param  int|string|\DateTime|Date  $day  day that the span covers
     */
    public function __construct($day = null)
    {
        $this->date = ((null === $day) ? (Date::now()) : (Date::castFrom($day, 'day')));
        parent::__construct($this->date, $this->date);
    }

    /**
     * create instance for tomorrow
     *
     * @return  Day
     * @since   3.5.1
     */
    public static function tomorrow()
    {
        return new self('tomorrow');
    }

    /**
     * create instance for yesterday
     *
     * @return  Day
     * @since   3.5.1
     */
    public static function yesterday()
    {
        return new self('yesterday');
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
