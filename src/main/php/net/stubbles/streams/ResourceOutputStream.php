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
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\exception\IllegalStateException;
use net\stubbles\lang\exception\IOException;
/**
 * Class for resource based output streams.
 */
abstract class ResourceOutputStream extends BaseObject implements OutputStream
{
    /**
     * the descriptor for the stream
     *
     * @type  int
     */
    protected $handle;

    /**
     * sets the resource to be used
     *
     * @param   resource  $handle
     * @throws  IllegalArgumentException
     */
    protected function setHandle($handle)
    {
        if (!is_resource($handle)) {
            throw new IllegalArgumentException('Handle needs to be a stream resource.');
        }

        $this->handle = $handle;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     * @throws  IllegalStateException
     * @throws  IOException
     */
    public function write($bytes)
    {
        if (null === $this->handle) {
            throw new IllegalStateException('Can not write to closed output stream.');
        }

        $length = @fwrite($this->handle, $bytes);
        if (false === $length) {
            throw new IOException('Can not write to output stream.');
        }

        return $length;
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written
     */
    public function writeLine($bytes)
    {
        return $this->write($bytes . "\r\n");
    }

    /**
     * closes the stream
     */
    public function close()
    {
        if (null !== $this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
?>