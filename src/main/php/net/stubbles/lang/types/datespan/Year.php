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
 *
 * @api
 */
class Year extends CustomDatespan
{
    /**
     * the represented year
     *
     * @type  int
     */
    private $year;

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
            $year = date('Y');
        }

        $start = new \DateTime();
        $start->setDate($year, 1, 1);
        $start->setTime(0, 0, 0);
        $end = new \DateTime();
        $end->setDate($year, 12, $start->format('t'));
        $end->setTime(23, 59, 59);
        parent::__construct(new Date($start), new Date($end));
        $this->year = (int) $year;
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
     * returns list of months for this year
     *
     * @return  Month[]
     */
    public function getMonths()
    {
        $month        = array();
        $start        = $this->getStart();
        $endTimestamp = $this->getEnd()->format('U');
        while ($start->format('U') <= $endTimestamp) {
            $month[] = new Month($start->getYear(), $start->getMonth());
            $start   = $start->change()->to('+1 month');
        }

        return $month;
    }

    /**
     * checks whether year is a leap year
     *
     * @return  bool
     */
    public function isLeapYear()
    {
        return $this->getStart()->format('L') == 1;
    }

    /**
     * checks if represented year is current year
     *
     * @return  bool
     */
    public function isCurrentYear()
    {
        return ((int) date('Y')) === $this->year;
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return (string) $this->year;
    }
}
?>