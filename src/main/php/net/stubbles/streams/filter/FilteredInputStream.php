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
use net\stubbles\streams\AbstractDecoratedInputStream;
use net\stubbles\streams\InputStream;
/**
 * Input stream applying a filter on data read before returning to requestor.
 */
class FilteredInputStream extends AbstractDecoratedInputStream
{
    /**
     * stream filter to be applied
     *
     * @type  net\stubbles\streams\filter\StreamFilter
     */
    protected $streamFilter;

    /**
     * constructor
     *
     * @param  InputStream   $inputStream   input stream to filter
     * @param  StreamFilter  $streamFilter  stream filter to be applied
     */
    public function __construct(InputStream $inputStream, StreamFilter $streamFilter)
    {
        parent::__construct($inputStream);
        $this->streamFilter = $streamFilter;
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
?>