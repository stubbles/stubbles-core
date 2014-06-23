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
use stubbles\streams\AbstractDecoratedInputStream;
use stubbles\streams\InputStream;
/**
 * Input stream applying a filter on data read before returning to requestor.
 *
 * @api
 */
class FilteredInputStream extends AbstractDecoratedInputStream
{
    /**
     * stream filter to be applied
     *
     * @type  stubbles\streams\filter\StreamFilter
     */
    private $streamFilter;

    /**
     * constructor
     *
     * @param   InputStream            $inputStream   input stream to filter
     * @param   StreamFilter|callable  $streamFilter  stream filter to be applied
     * @throws  IllegalArgumentException  in case given stream filter is neither a StreamFilter nor a callable
     */
    public function __construct(InputStream $inputStream, $streamFilter)
    {
        parent::__construct($inputStream);
        if ($streamFilter instanceof StreamFilter) {
            $this->streamFilter = $streamFilter;
        } elseif (is_callable($streamFilter)) {
            $this->streamFilter = new CallableStreamFilter($streamFilter);
        } else {
            throw new IllegalArgumentException('Given stream filter is neither a callable nor an instance of stubbles\streams\filter\StreamFilter');
        }
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        while (!$this->inputStream->eof()) {
            $data = $this->inputStream->read($length);
            if (!$this->streamFilter->shouldFilter($data)) {
                return $data;
            }
        }

        return '';
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        while (!$this->inputStream->eof()) {
            $data = $this->inputStream->readLine($length);
            if (!$this->streamFilter->shouldFilter($data)) {
                return $data;
            }
        }

        return '';
    }
}
