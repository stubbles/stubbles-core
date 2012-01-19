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
 * Abstract base class for decorated output streams.
 */
abstract class AbstractDecoratedOutputStream extends BaseObject implements DecoratedOutputStream
{
    /**
     * input stream to encode into internal encoding
     *
     * @type  OutputStream
     */
    protected $outputStream;

    /**
     * constructor
     *
     * @param  OutputStream  $outputStream
     */
    public function __construct(OutputStream $outputStream)
    {
        $this->outputStream = $outputStream;
    }

    /**
     * replace current enclosed output stream
     *
     * @param   OutputStream  $outputStream
     * @return  AbstractDecoratedOutputStream
     */
    public function setEnclosedOutputStream(OutputStream $outputStream)
    {
        $this->outputStream = $outputStream;
        return $this;
    }

    /**
     * returns enclosed output stream
     *
     * @return  OutputStream
     */
    public function getEnclosedOutputStream()
    {
        return $this->outputStream;
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
     * closes the stream
     */
    public function close()
    {
        $this->outputStream->close();
    }
}
?>