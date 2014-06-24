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
     * @type  stubbles\predicate\Predicate
     */
    private $predicate;

    /**
     * constructor
     *
     * @param   OutputStream                     $outputStream  stream to apply filter onto
     * @param   StreamFilter|callable|Predicate  $predicate     predicate to check if something should be passed
     */
    public function __construct(OutputStream $outputStream, $predicate)
    {
        parent::__construct($outputStream);
        if ($predicate instanceof StreamFilter) {
            $this->predicate = new StreamFilterPredicate($predicate);
        } else {
            $this->predicate = Predicate::castFrom($predicate);
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
        if ($this->predicate->test($bytes)) {
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
        if ($this->predicate->test($bytes)) {
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
