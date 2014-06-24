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
     * @param   StreamFilter|callable  $streamFilter
     * @return  CompositeStreamFilter
     * @throws  IllegalArgumentException
     */
    public function addStreamFilter($streamFilter)
    {
        if ($streamFilter instanceof StreamFilter) {
            $this->streamFilter[] = $streamFilter;
        } elseif (is_callable($streamFilter)) {
            $this->streamFilter[] = new CallableStreamFilter($streamFilter);
        } else {
            throw new IllegalArgumentException('Given stream filter is neither a callable nor an instance of stubbles\streams\filter\StreamFilter');
        }

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
