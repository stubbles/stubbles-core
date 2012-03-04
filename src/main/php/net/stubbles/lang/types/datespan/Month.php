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
class Month extends CustomDatespan
{
    /**
     * year where month is within
     *
     * @type  int
     */
    private $year;
    /**
     * actual month
     *
     * @type  int
     */
    private $month;
    /**
     * amount of days in this month
     *
     * @type  int
     */
    private $amountOfDays;

    /**
     * constructor
     *
     * If no value for the year is supplied the current year will be used.
     *
     * If no value for the month is supplied the current month will be used.
     *
     * If the current day is the first of a month, the datespan will
     * cover the last month. If today is the first of january, then the
     * datespan will cover the december of previous year.
     *
     * @param  int  $year   year of the span
     * @param  int  $month  month of the span
     */
    public function __construct($year = null, $month = null)
    {
        if (null === $year) {
            $year = (int) date('Y');
        }

        if (null === $month) {
            $month = (int) date('m');
            if ($this->isFirstOfMonth()) {
                if ($this->isJanuary($month)) {
                    $month = 12;
                    $year--;
                } else {
                    $month--;
                }
            }
        }

        $start              = new Date($year . '-' . $month . '-1 00:00:00');
        $this->amountOfDays = $start->format('t');
        $this->year         = $year;
        $this->month        = $month;
        parent::__construct($start,
                            new Date($year . '-' . $month . '-' . $this->amountOfDays . ' 23:59:59')
        );
    }

    /**
     * checks whether day is first of month
     *
     * @return  bool
     */
    private function isFirstOfMonth()
    {
        return 1 === (int) date('d');
    }

    /**
     * checks whether month is january
     *
     * @param   int  $month
     * @return  bool
     */
    private function isJanuary($month)
    {
        return 1 === $month;
    }

    /**
     * returns amount of days in this month
     *
     * @return  int
     */
    public function getAmountOfDays()
    {
        return $this->amountOfDays;
    }

    /**
     * checks if it represents the current month
     *
     * @return  bool
     */
    public function isCurrentMonth()
    {
        return $this->asString() === Date::now()->format('Y-m');
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->year . '-' . ((10 > $this->month && strlen($this->month) === 1) ? ('0' . $this->month) : ($this->month));
    }
}
?>