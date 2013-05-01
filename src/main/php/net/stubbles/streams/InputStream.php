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
 * Interface for input streams.
 *
 * @api
 */
interface InputStream
{
    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192);

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192);

    /**
     * returns the amount of byted left to be read
     *
     * @return  int
     */
    public function bytesLeft();

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof();

    /**
     * closes the stream
     */
    public function close();
}
?>