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
use stubbles\streams\AbstractDecoratedOutputStream;
use stubbles\streams\OutputStream;
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
    private $streamFilter;

    /**
     * constructor
     *
     * @param   OutputStream            $outputStream  stream to apply filter onto
     * @param   StreamFilter|callable   $streamFilter  stream filter to be applied
     * @throws  IllegalArgumentException  in case given stream filter is neither a StreamFilter nor a callable
     */
    public function __construct(OutputStream $outputStream, $streamFilter)
    {
        parent::__construct($outputStream);
        if ($streamFilter instanceof StreamFilter) {
            $this->streamFilter = $streamFilter;
        } elseif (is_callable($streamFilter)) {
            $this->streamFilter = new CallableStreamFilter($streamFilter);
        } else {
            throw new IllegalArgumentException('Given stream filter is neither a callable nor an instance of stubbles\streams\filter\StreamFilter');
        }
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
     * @return  int     amount of written bytes
     */
    public function writeLine($bytes)
    {
        if (!$this->streamFilter->shouldFilter($bytes)) {
            return $this->outputStream->writeLine($bytes);
        }

        return 0;
    }

    /**
     * writes given bytes and appends a line break after each one
     *
     * @param   string[]  $bytes
     * @return  int       amount of written bytes
     * @since   3.2.0
     */
    public function writeLines(array $bytes)
    {
        $bytesWritten = 0;
        foreach ($bytes as $line) {
            $bytesWritten += $this->writeLine($line);
        }

        return $bytesWritten;

    }
}
