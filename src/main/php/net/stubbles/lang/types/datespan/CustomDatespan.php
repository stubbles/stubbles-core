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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\types\Date;
/**
 * Datespan with a custom start and end date.
 */
class CustomDatespan extends BaseObject implements Datespan
{
    /**
     * start date of the span
     *
     * @type  Date
     */
    protected $start;
    /**
     * end date of the span
     *
     * @type  Date
     */
    protected $end;

    /**
     * constructor
     *
     * @param  string|Date  $start  start date of the span
     * @param  string|Date  $end    end date of the span
     */
    public function __construct($start, $end)
    {
        if (!($start instanceof Date)) {
            $start = new Date($start);
        }

        if (!($end instanceof Date)) {
            $end = new Date($end);
        }

        $this->start = $start->change()->timeTo('00:00:00');
        $this->end   = $end->change()->timeTo('23:59:59');
    }

    /**
     * returns the start date
     *
     * @return  Date
     */
    public function getStartDate()
    {
        return $this->start;
    }

    /**
     * returns the end date
     *
     * @return  Date
     */
    public function getEndDate()
    {
        return $this->end;
    }

    /**
     * returns the spans between the start date and the end date in given intervals
     *
     * If no interval is given DatespanInterval::$DAY is assumed.
     *
     * If the given interval is not supported by the datespan an
     * IllegalArgumentException will be thrown.
     *
     * @param   DatespanInterval  $interval
     * @return  Datespan[]
     * @throws  IllegalArgumentException
     */
    public function getDatespans(DatespanInterval $interval = null)
    {
        if (null === $interval) {
            $interval = DatespanInterval::$DAY;
        }

        $maxInterval = $this->getMaxInterval();
        if (!$maxInterval->contains($interval)) {
            throw new IllegalArgumentException('Interval type ' . $interval->name() . ' is not supported by ' . $this->getClassName());
        }

        if ($maxInterval->equals($interval)) {
            return array($this);
        }

        return $interval->getSpans($this->start, $this->end);
    }

    /**
     * returns maximum supported interval
     *
     * @return  DatespanInterval
     */
    protected function getMaxInterval()
    {
        return DatespanInterval::$CUSTOM;
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->start->format('d.m.Y') . ' - ' . $this->end->format('d.m.Y');
    }

    /**
     * checks whether the DateSpan is in the future compared to current date
     *
     * @return  bool
     */
    public function isInFuture()
    {
        $today = mktime(23, 59, 59, date('m'), date('d'), date('Y'));
        if ($this->start->format('U') > $today) {
            return true;
        }

        return false;
    }

    /**
     * checks whether the span contains the given date
     *
     * @param   Date  $date
     * @return  bool
     */
    public function containsDate(Date $date)
    {
        if (!$this->start->isBefore($date) && !$this->start->equals($date)) {
            return false;
        }

        if (!$this->end->isAfter($date) && !$this->end->equals($date)) {
            return false;
        }

        return true;
    }

    /**
     * returns amount of days in this datespan
     *
     * @return  int
     */
    public function getAmountOfDays()
    {
        return $this->end->getHandle()->diff($this->start->getHandle())->days + 1;
    }
}
?>