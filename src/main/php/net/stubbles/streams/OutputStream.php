<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams;
/**
 * Interface for output streams.
 *
 * @api
 */
interface OutputStream
{
    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes);

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function writeLine($bytes);

    /**
     * writes given bytes and appends a line break after each one
     *
     * @param   string[]  $bytes
     * @return  int       amount of written bytes
     * @since   3.2.0
     */
    public function writeLines(array $bytes);

    /**
     * closes the stream
     */
    public function close();
}
