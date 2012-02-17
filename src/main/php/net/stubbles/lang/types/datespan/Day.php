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
class Day extends CustomDatespan implements Datespan
{
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

        parent::__construct($day->change()->timeTo('00:00:00'),
                            $day->change()->timeTo('23:59:59')
        );
    }

    /**
     * returns maximum supported interval
     *
     * @return  DatespanInterval
     */
    protected function getMaxInterval()
    {
        return DatespanInterval::$DAY;
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->start->format('l, d.m.Y');
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
}
?>