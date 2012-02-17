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
class Month extends CustomDatespan implements Datespan
{
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

        $start = new \DateTime();
        $start->setDate($year, $month, 1);
        $start->setTime(0, 0, 0);
        $end = new \DateTime();
        $end->setDate($year, $month, $start->format('t'));
        $end->setTime(23, 59, 59);
        parent::__construct(new Date($start), new Date($end));
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
     * returns maximum supported interval
     *
     * @return  DatespanInterval
     */
    protected function getMaxInterval()
    {
        return DatespanInterval::$MONTH;
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->start->format('Y-m');
    }

    /**
     * returns amount of days in this month
     *
     * @return  int
     */
    public function getAmountOfDays()
    {
        return $this->start->format('t');
    }
}
?>