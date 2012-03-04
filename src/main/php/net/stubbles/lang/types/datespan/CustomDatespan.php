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
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\types\Date;
/**
 * Datespan with a custom start and end date.
 *
 * @api
 */
class CustomDatespan extends AbstractDatespan
{
    /**
     * returns list of days within this datespan
     *
     * @return  Day[]
     */
    public function getDays()
    {
        $days         = array();
        $start        = $this->getStart();
        $endTimestamp = $this->getEnd()->format('U');
        while ($start->format('U') <= $endTimestamp) {
            $days[] =  new Day(clone $start);
            $start   = $start->change()->to('+1 day');
        }

        return $days;
    }

    /**
     * returns a string representation of the datespan
     *
     * @return  string
     */
    public function asString()
    {
        return $this->getStart()->format('d.m.Y') . ' - ' . $this->getEnd()->format('d.m.Y');
    }
}
?>