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
use net\stubbles\lang\Enum;
use net\stubbles\lang\types\Date;
/**
 * Description of DatespanInterval
 */
class DatespanInterval extends Enum
{
    /**
     * one day between two dates
     *
     * @type  DatespanInterval
     */
    public static $DAY;
    /**
     * one week between to dates
     *
     * @type  DatespanInterval
     */
    public static $WEEK;
    /**
     * one month between to dates
     *
     * @type  DatespanInterval
     */
    public static $MONTH;
    /**
     * one month between to dates
     *
     * @type  DatespanInterval
     */
    public static $YEAR;
    /**
     * custom interval
     *
     * @type  DatespanInterval
     */
    public static $CUSTOM;
    /**
     * constructor for datespan covering this interval
     *
     * @type  \Closure
     */
    private $constructor;
    /**
     * function to advance to start of next datespan in this interval
     *
     * @type  \Closure
     */
    private $advance;

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$DAY    = new self('day',
                                 1,
                                 function(Date $start) { return new Day(clone $start);},
                                 function(Date $start) { return $start->change()->to('+1 day');}
                        );
        self::$WEEK   = new self('week',
                                 2,
                                 function(Date $start) { return new Week(clone $start);},
                                 function(Date $start) { return $start->change()->to('+7 days');}
                        );
        self::$MONTH  = new self('month',
                                 3,
                                 function(Date $start) { return new Month($start->getYear(), $start->getMonth());},
                                 function(Date $start) { return $start->change()->to('+1 month');}
                        );
        self::$YEAR   = new self('year',
                                 4,
                                 function(Date $start) { return new Year($start->getYear());},
                                 function(Date $start) { return $start->change()->to('+1 year');}
                        );
        self::$CUSTOM = new self('custom',
                                 5,
                                 function(Date $start, Date $end) { return new CustomDatespan($start, $end);},
                                 function(Date $start, Date $end) { return $end->change()->to('+1 sec');}
                        );
    }

    /**
     * constructor
     *
     * @param  string    $name
     * @param  int       $value
     * @param  \Closure  $constructor
     * @param  \Closure  $advance
     */
    protected function __construct($name, $value, \Closure $constructor, \Closure$advance)
    {
        parent::__construct($name, $value);
        $this->constructor = $constructor;
        $this->advance     = $advance;
    }

    /**
     * returns list datespans with this interval
     *
     * @param   Date  $start  start date of first interval
     * @param   Date  $end    end date of last interval
     * @return  Datespan[]
     */
    public function getSpans(Date $start, Date $end)
    {
        $constructor  = $this->constructor;
        $advance      = $this->advance;
        $spans        = array();
        $endTimestamp = $end->format('U');
        while ($start->format('U') <= $endTimestamp) {
            $spans[] = $constructor($start, $end);
            $start   = $advance($start, $end);
        }

        return $spans;
    }

    /**
     * checks whether this interval contains other interval
     *
     * The other interval is contained if one datespan instance can be broken
     * down to at least one of the other interval.
     *
     * @param   DatespanInterval  $other
     * @return  bool
     */
    public function contains(DatespanInterval $other)
    {
        return $other->value() <= $this->value();
    }
}
DatespanInterval::__static();
?>