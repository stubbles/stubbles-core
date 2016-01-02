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
     * predicate which decides on whether a line is acceptable
     *
     * @type  stubbles\predicate\Predicate
     */
    private $predicate;

    /**
     * constructor
     *
     * @param   \stubbles\streams\OutputStream  $outputStream  stream to apply filter onto
     * @param   callable                        $predicate     predicate to check if something should be passed
     */
    public function __construct(OutputStream $outputStream, callable $predicate)
    {
        parent::__construct($outputStream);
        $this->predicate = $predicate;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        $isAcceptable = $this->predicate;
        if ($isAcceptable($bytes)) {
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
        $isAcceptable = $this->predicate;
        if ($isAcceptable($bytes)) {
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
