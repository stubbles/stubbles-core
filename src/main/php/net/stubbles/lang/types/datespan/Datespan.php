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
 *
 * @api
 */
interface Datespan extends Object
{
    /**
     * returns the start date
     *
     * @return  Date
     */
    public function getStart();

    /**
     * returns the end date
     *
     * @return  Date
     */
    public function getEnd();

    /**
     * returns amount of days in this datespan
     *
     * @return  int
     */
    public function getAmountOfDays();

    /**
     * returns list of days
     *
     * @return  Day[]
     */
    public function getDays();

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
     * returns a string representation of the datespan
     *
     * @return  string
     */
    public function asString();
}
?>