<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\memory;
use net\stubbles\streams\OutputStream;
/**
 * Class to stream data into memory.
 *
 * @api
 */
class MemoryOutputStream implements OutputStream
{
    /**
     * written data
     *
     * @type  string
     */
    protected $buffer = '';

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        $this->buffer .= $bytes;
        return strlen($bytes);
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function writeLine($bytes)
    {
        return $this->write($bytes . "\n");
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

    /**
     * closes the stream
     */
    public function close()
    {
        // intentionally empty
    }

    /**
     * returns written contents
     *
     * @return  string
     */
    public function getBuffer()
    {
        return $this->buffer;
    }
}
