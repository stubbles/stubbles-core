<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\filter;
use stubbles\predicate\Predicate;
/**
 * Description of StreamFilterPredicate
 *
 * @author fkleine
 */
class StreamFilterPredicate extends Predicate
{
    /**
     * stream filter to use as predicate
     *
     * @type  StreamFilter
     */
    private $streamFilter;

    /**
     * constructor
     *
     * @param  \stubbles\streams\filter\StreamFilter  $streamFilter
     */
    public function __construct(StreamFilter $streamFilter)
    {
        $this->streamFilter = $streamFilter;
    }

    /**
     * evaluates predicate against given value
     *
     * @param   mixed  $value
     * @return  bool
     */
    public function test($value)
    {
        return !$this->streamFilter->shouldFilter($value);
    }
}
