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
 * Datespan that represents a week.
 */
class Week extends CustomDatespan implements Datespan
{
    /**
     * constructor
     *
     * @param  string|Date  $date  first day of the week
     */
    public function __construct($date)
    {
        if (!($date instanceof Date)) {
            $date = new Date($date);
        }

        $end = $date->change()->to('+ 6 days');
        parent::__construct($date, $end);
    }

    /**
     * returns maximum supported interval
     *
     * @return  DatespanInterval
     */
    protected function getMaxInterval()
    {
        return DatespanInterval::$WEEK;
    }

    /**
     * returns a string representation of the date object
     *
     * @return  string
     */
    public function asString()
    {
        return $this->start->format('W');
    }
}
?>