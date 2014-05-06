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
 * Interface for the datespan classes.
 *
 * @api
 */
interface Datespan
{
    /**
     * returns the start date
     *
     * @return  Date
     */
    public function getStart();

    /**
     * checks whether datespan starts before a given date
     *
     * @param   int|string|\DateTime|Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function startsBefore($date);

    /**
     * checks whether datespan starts after a given date
     *
     * @param   int|string|\DateTime|Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function startsAfter($date);

    /**
     * returns the end date
     *
     * @return  Date
     */
    public function getEnd();

    /**
     * checks whether datespan ends before a given date
     *
     * @param   int|string|\DateTime|Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function endsBefore($date);

    /**
     * checks whether datespan ends after a given date
     *
     * @param   int|string|\DateTime|Date  $date
     * @return  bool
     * @since   3.5.0
     */
    public function endsAfter($date);

    /**
     * returns formatted date/time string for start date
     *
     * @param   string    $format    format, see http://php.net/date
     * @param   TimeZone  $timeZone  target time zone of formatted string
     * @return  string
     * @since   3.5.0
     */
    public function formatStart($format, TimeZone $timeZone = null);

    /**
     * returns formatted date/time string for end date
     *
     * @param   string    $format    format, see http://php.net/date
     * @param   TimeZone  $timeZone  target time zone of formatted string
     * @return  string
     * @since   3.5.0
     */
    public function formatEnd($format, TimeZone $timeZone = null);

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
     * @param   int|string|\DateTime|Date  $date
     * @return  bool
     */
    public function containsDate($date);

    /**
     * returns a string representation of the datespan
     *
     * @return  string
     */
    public function asString();
}
