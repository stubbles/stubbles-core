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
 * Datespan that represents a month.
 */
class Year extends CustomDatespan implements Datespan
{
    /**
     * constructor
     *
     * If no value for the year is supplied the current year will be used.
     *
     * @param  int  $year   year of the span
     */
    public function __construct($year = null)
    {
        if (null === $year) {
            $year = (int) date('Y');
        }

        $start = new \DateTime();
        $start->setDate($year, 1, 1);
        $start->setTime(0, 0, 0);
        $end = new \DateTime();
        $end->setDate($year, 12, $start->format('t'));
        $end->setTime(23, 59, 59);
        parent::__construct(new Date($start), new Date($end));
    }

    /**
     * returns maximum supported interval
     *
     * @return  DatespanInterval
     */
    protected function getMaxInterval()
    {
        return DatespanInterval::$YEAR;
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->start->format('Y');
    }

    /**
     * returns amount of days in this year
     *
     * @return  int
     */
    public function getAmountOfDays()
    {
       if ($this->isLeapYear()) {
           return 366;
       }

       return 365;
    }

    /**
     * checks whether year is a leap year
     *
     * @return  bool
     */
    public function isLeapYear()
    {
        return $this->start->format('L') == 1;
    }
}
?>