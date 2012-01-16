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
 * Class for date/time modifications.
 *
 * @since   1.7.0
 */
class DateModifier extends BaseObject
{
    /**
     * original date to base modifications on
     *
     * @type  Date
     */
    protected $originalDate;

    /**
     * constructor
     *
     * @param  Date  $originalDate
     */
    public function __construct(Date $originalDate)
    {
        $this->originalDate = $originalDate;
    }

    /**
     * returns a new date instance which represents the changed date
     *
     * @param   string  $target  relative format accepted by strtotime()
     * @return  Date
     */
    public function to($target)
    {
        $modifiedHandle = clone $this->originalDate->getHandle();
        $modifiedHandle->modify($target);
        return new Date($modifiedHandle);
    }

    /**
     * returns a new date instance with same date but changed time
     *
     * @param   string  $time  time representation in format HH:MM:SS
     * @return  Date
     * @throws  IllegalArgumentException
     */
    public function timeTo($time)
    {
        $times = explode(':', $time);
        if (count($times) != 3) {
            throw new IllegalArgumentException('Given time ' . $time . ' does not follow required format HH:MM:SS');
        }

        list($hour, $minute, $second) = $times;
        return $this->createDateWithNewTime($hour, $minute, $second);
    }

    /**
     * returns a new date instance with same date, minute and second but changed hour
     *
     * @param   int  $hour
     * @return  Date
     */
    public function hourTo($hour)
    {
        return $this->createDateWithNewTime($hour, $this->originalDate->getMinutes(), $this->originalDate->getSeconds());
    }

    /**
     * changes date by given amount of hours
     *
     * @param   int  $hours
     * @return  Date
     */
    public function byHours($hours)
    {
        return $this->hourTo($this->originalDate->getHours() + $hours);
    }

    /**
     * returns a new date instance with same date, hour and second but changed minute
     *
     * @param   int  $minute
     * @return  Date
     */
    public function minuteTo($minute)
    {
        return $this->createDateWithNewTime($this->originalDate->getHours(), $minute, $this->originalDate->getSeconds());
    }

    /**
     * changes date by given amount of minutes
     *
     * @param   int  $minutes
     * @return  Date
     */
    public function byMinutes($minutes)
    {
        return $this->minuteTo($this->originalDate->getMinutes() + $minutes);
    }

    /**
     * returns a new date instance with same date, hour and minute but changed second
     *
     * @param   int  $second
     * @return  Date
     */
    public function secondTo($second)
    {
        return $this->createDateWithNewTime($this->originalDate->getHours(), $this->originalDate->getMinutes(), $second);
    }

    /**
     * changes date by given amount of seconds
     *
     * @param   int  $seconds
     * @return  Date
     */
    public function bySeconds($seconds)
    {
        return $this->secondTo($this->originalDate->getSeconds() + $seconds);
    }

    /**
     * creates new date instance with changed time
     *
     * @param   int  $hour
     * @param   int  $minute
     * @param   int  $second
     * @return  Date
     * @throws  IllegalArgumentException
     */
    protected function createDateWithNewTime($hour, $minute, $second)
    {
        $modifiedHandle = clone $this->originalDate->getHandle();
        if (false === @$modifiedHandle->setTime($hour, $minute, $second)) {
            throw new IllegalArgumentException('Given values for hour, minute and/or second not suitable for changing the time.');
        }

        return new Date($modifiedHandle);
    }

    /**
     * returns a new date instance with changed date but same time
     *
     * @param   string  $date  date representation in format YYYY-MM-DD
     * @return  Date
     * @throws  IllegalArgumentException
     */
    public function dateTo($date)
    {
        $dates = explode('-', $date);
        if (count($dates) != 3) {
            throw new IllegalArgumentException('Given date ' . $date . ' does not follow required format YYYY-MM-DD');
        }

        list($year, $month, $day) = $dates;
        return $this->createNewDateWithExistingTime($year, $month, $day);
    }

    /**
     * returns a new date instance with changed year but same time, month and day
     *
     * @param   string  $year
     * @return  Date
     */
    public function yearTo($year)
    {
        return $this->createNewDateWithExistingTime($year, $this->originalDate->getMonth(), $this->originalDate->getDay());
    }

    /**
     * changes date by given amount of years
     *
     * @param   int  $years
     * @return  Date
     */
    public function byYears($years)
    {
        return $this->yearTo($this->originalDate->getYear() + $years);
    }

    /**
     * returns a new date instance with changed month but same time, year and day
     *
     * @param   string  $month
     * @return  Date
     */
    public function monthTo($month)
    {
        return $this->createNewDateWithExistingTime($this->originalDate->getYear(), $month, $this->originalDate->getDay());
    }

    /**
     * changes date by given amount of months
     *
     * @param   int  $months
     * @return  Date
     */
    public function byMonths($months)
    {
        return $this->monthTo($this->originalDate->getMonth() + $months);
    }

    /**
     * returns a new date instance with changed day but same time, year and month
     *
     * @param   string  $day
     * @return  Date
     */
    public function dayTo($day)
    {
        return $this->createNewDateWithExistingTime($this->originalDate->getYear(), $this->originalDate->getMonth(), $day);
    }

    /**
     * changes date by given amount of days
     *
     * @param   int  $days
     * @return  Date
     */
    public function byDays($days)
    {
        return $this->dayTo($this->originalDate->getDay() + $days);
    }

    /**
     * creates new date instance with changed date but same time
     *
     * @param   int   $year
     * @param   int   $month
     * @param   int   $day
     * @return  Date
     * @throws  IllegalArgumentException
     */
    protected function createNewDateWithExistingTime($year, $month, $day)
    {
        $modifiedHandle = clone $this->originalDate->getHandle();
        if (false === @$modifiedHandle->setDate($year, $month, $day)) {
            throw new IllegalArgumentException('Given values for year, month and/or day not suitable for changing the date.');
        }

        return new Date($modifiedHandle);
    }
}
?>