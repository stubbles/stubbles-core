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
use net\stubbles\streams\AbstractDecoratedOutputStream;
use net\stubbles\streams\OutputStream;
/**
 * Output stream applying a filter on data to write.
 *
 * @api
 */
class FilteredOutputStream extends AbstractDecoratedOutputStream
{
    /**
     * stream filter to be applied
     *
     * @type  StreamFilter
     */
    protected $streamFilter;

    /**
     * constructor
     *
     * @param  OutputStream  $outputStream  stream to apply filter onto
     * @param  StreamFilter  $streamFilter  stream filter to be applied
     */
    public function __construct(OutputStream $outputStream, StreamFilter $streamFilter)
    {
        parent::__construct($outputStream);
        $this->streamFilter = $streamFilter;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        if (!$this->streamFilter->shouldFilter($bytes)) {
            return $this->outputStream->write($bytes);
        }

        return 0;
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        if (!$this->streamFilter->shouldFilter($bytes)) {
            return $this->outputStream->writeLine($bytes);
        }

        return 0;
    }
}
?>