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
use net\stubbles\lang\BaseObject;
/**
 * Abstract base class for decorated input streams.
 */
abstract class AbstractDecoratedInputStream extends BaseObject implements DecoratedInputStream
{
    /**
     * input stream to encode into internal encoding
     *
     * @type  InputStream
     */
    protected $inputStream;

    /**
     * constructor
     *
     * @param  InputStream  $inputStream
     */
    public function __construct(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
    }

    /**
     * replace current enclosed input stream
     *
     * @param   InputStream  $inputStream
     * @return  AbstractDecoratedInputStream
     */
    public function setEnclosedInputStream(InputStream $inputStream)
    {
        $this->inputStream = $inputStream;
        return $this;
    }

    /**
     * returns enclosed input stream
     *
     * @return  InputStream
     */
    public function getEnclosedInputStream()
    {
        return $this->inputStream;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        return $this->inputStream->read($length);
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        return $this->inputStream->readLine($length);
    }

    /**
     * returns the amount of byted left to be read
     *
     * @return  int
     */
    public function bytesLeft()
    {
        return $this->inputStream->bytesLeft();
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof()
    {
        return $this->inputStream->eof();
    }

    /**
     * closes the stream
     */
    public function close()
    {
        $this->inputStream->close();
    }
}
?>