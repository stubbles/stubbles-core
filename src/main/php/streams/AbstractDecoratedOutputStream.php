<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams;
/**
 * Abstract base class for decorated output streams.
 *
 * @api
 */
abstract class AbstractDecoratedOutputStream implements OutputStream
{
    /**
     * input stream to encode into internal encoding
     *
     * @type  \stubbles\streams\OutputStream
     */
    protected $outputStream;

    /**
     * constructor
     *
     * @param  \stubbles\streams\OutputStream  $outputStream
     */
    public function __construct(OutputStream $outputStream)
    {
        $this->outputStream = $outputStream;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        return $this->outputStream->write($bytes);
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        return $this->outputStream->writeLine($bytes);
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
        return $this->outputStream->writeLines($bytes);
    }

    /**
     * closes the stream
     */
    public function close()
    {
        $this->outputStream->close();
    }
}
