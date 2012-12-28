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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\streams\InputStream;
use net\stubbles\streams\Seekable;
/**
 * Class to stream data from memory.
 *
 * @api
 */
class MemoryInputStream extends BaseObject implements InputStream, Seekable
{
    /**
     * written data
     *
     * @type  string
     */
    protected $buffer   = '';
    /**
     * current position in buffer
     *
     * @type  int
     */
    protected $position = 0;

    /**
     * constructor
     *
     * @param  string  $buffer
     */
    public function __construct($buffer)
    {
        $this->buffer = $buffer;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        $bytes           = substr($this->buffer, $this->position, $length);
        $this->position += strlen($bytes);
        return $bytes;
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        $bytes        = substr($this->buffer, $this->position, $length);
        $linebreakpos = strpos($bytes, "\n");
        if (false !== $linebreakpos) {
            $line = substr($bytes, 0, $linebreakpos);
            $this->position += strlen($line) + 1;
        } else {
            $line = $bytes;
            $this->position += strlen($line);
        }

        return rtrim($line);
    }

    /**
     * returns the amount of byted left to be read
     *
     * @return  int
     */
    public function bytesLeft()
    {
        return strlen($this->buffer) - $this->position;
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof()
    {
        return (strlen($this->buffer) === $this->position);
    }

    /**
     * closes the stream
     */
    public function close()
    {
        // intentionally empty
    }

    /**
     * seek to given offset
     *
     * @param   int  $offset  new position or amount of bytes to seek
     * @param   int  $whence  one of Seekable::SET, Seekable::CURRENT or Seekable::END
     * @throws  IllegalArgumentException
     */
    public function seek($offset, $whence = Seekable::SET)
    {
        switch ($whence) {
            case Seekable::SET:
                $this->position = $offset;
                break;

            case Seekable::CURRENT:
                $this->position += $offset;
                break;

            case Seekable::END:
                $this->position = strlen($this->buffer) + $offset;
                break;

            default:
                throw new IllegalArgumentException('Wrong value for $whence, must be one of Seekable::SET, Seekable::CURRENT or Seekable::END.');
        }
    }

    /**
     * return current position
     *
     * @return  int
     */
    public function tell()
    {
        return $this->position;
    }
}
?>