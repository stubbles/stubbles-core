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
use net\stubbles\lang\Object;
use net\stubbles\lang\types\Date;
/**
 * Interface for the datespan classes.
 */
interface Datespan extends Object
{
    /**
     * returns the start date
     *
     * @return  Date
     */
    public function getStartDate();

    /**
     * returns the end date
     *
     * @return  Date
     */
    public function getEndDate();

    /**
     * returns the spans between the start date and the end date in given intervals
     *
     * @param   DatespanInterval  $interval
     * @return  Datespan[]
     */
    public function getDatespans(DatespanInterval $interval = null);

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString();

    /**
     * checks whether the span is in the future compared to current date
     *
     * @return  bool
     */
    public function isInFuture();

    /**
     * checks whether the span contains the given date
     *
     * @param   Date  $date
     * @return  bool
     */
    public function containsDate(Date $date);

    /**
     * returns amount of days in this datespan
     *
     * @return  int
     */
    public function getAmountOfDays();
}
?>