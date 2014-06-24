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
use stubbles\lang\exception\IllegalArgumentException;
/**
 * Composite to combine a list of stream filters.
 *
 * @api
 * @deprecated  since 4.0.0, use predicates instead, will be removed with 5.0.0
 */
class CompositeStreamFilter implements StreamFilter
{
    /**
     * list of stream filters to apply
     *
     * @type  StreamFilter[]
     */
    protected $streamFilter = [];

    /**
     * add a stream filter
     *
     * @param   StreamFilter  $streamFilter
     * @return  CompositeStreamFilter
     * @throws  IllegalArgumentException
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
