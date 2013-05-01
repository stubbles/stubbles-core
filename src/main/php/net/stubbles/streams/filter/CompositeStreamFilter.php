<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\filter;
/**
 * Composite to combine a list of stream filters.
 *
 * @api
 */
class CompositeStreamFilter implements StreamFilter
{
    /**
     * list of stream filters to apply
     *
     * @type  StreamFilter[]
     */
    protected $streamFilter = array();

    /**
     * add a stream filter
     *
     * @param   StreamFilter  $streamFilter
     * @return  CompositeStreamFilter
     */
    public function addStreamFilter(StreamFilter $streamFilter)
    {
        $this->streamFilter[] = $streamFilter;
        return $this;
    }

    /**
     * Decides whether data should be filtered or not.
     *
     * @param   string  $data
     * @return  bool
     */
    public function shouldFilter($data)
    {
        foreach ($this->streamFilter as $streamFilter) {
            if ($streamFilter->shouldFilter($data)) {
                return true;
            }
        }

        return false;
    }
}
?>