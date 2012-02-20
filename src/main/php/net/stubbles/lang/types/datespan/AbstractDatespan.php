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
abstract class AbstractDatespan extends BaseObject implements Datespan
{
    /**
     * start date of the span
     *
     * @type  Date
     */
    private $start;
    /**
     * end date of the span
     *
     * @type  Date
     */
    private $end;

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
    public function getStart()
    {
        return $this->start;
    }

    /**
     * returns the end date
     *
     * @return  Date
     */
    public function getEnd()
    {
        return $this->end;
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
}
?>