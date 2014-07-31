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
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\exception\IllegalStateException;
use stubbles\lang\exception\IOException;
/**
 * Class for resource based input streams.
 *
 * @internal
 */
abstract class ResourceInputStream implements InputStream
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
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     */
    protected function setHandle($handle)
    {
        if (!is_resource($handle)) {
            throw new IllegalArgumentException('Handle needs to be a stream resource.');
        }

        $this->handle = $handle;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     * @throws  \stubbles\lang\exception\IllegalStateException
     * @throws  \stubbles\lang\exception\IOException
     */
    public function read($length = 8192)
    {
        if (null === $this->handle) {
            throw new IllegalStateException('Can not read from closed input stream.');
        }

        $data = @fread($this->handle, $length);
        if (false === $data) {
            if (!@feof($this->handle)) {
                throw new IOException('Can not read from input stream.');
            }

            return '';
        }

        return $data;
    }

    /**
     * reads given amount of bytes or until next line break and removes line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     * @throws  \stubbles\lang\exception\IllegalStateException
     * @throws  \stubbles\lang\exception\IOException
     */
    public function readLine($length = 8192)
    {
        if (null === $this->handle) {
            throw new IllegalStateException('Can not read from closed input stream.');
        }

        $data = @fgets($this->handle, $length);
        if (false === $data) {
            if (!@feof($this->handle)) {
                throw new IOException('Can not read from input stream.');
            }

            return '';
        }

        return rtrim($data, "\r\n");
    }

    /**
     * returns the amount of bytes left to be read
     *
     * @return  int
     * @throws  \stubbles\lang\exception\IllegalStateException
     */
    public function bytesLeft()
    {
        if (null === $this->handle || !is_resource($this->handle)) {
            throw new IllegalStateException('Can not read from closed input stream.');
        }

        $bytesRead = ftell($this->handle);
        if (!is_int($bytesRead)) {
            return 0;
        }

        return $this->getResourceLength() - $bytesRead;
    }

    /**
     * returns true if the stream pointer is at EOF
     *
     * @return  bool
     */
    public function eof()
    {
        return feof($this->handle);
    }

    /**
     * helper method to retrieve the length of the resource
     *
     * Not all stream wrappers support (f)stat - the extending class then
     * needs to take care to deliver the correct resource length then.
     *
     * @return  int
     */
    protected function getResourceLength()
    {
        $fileData = fstat($this->handle);
        return $fileData['size'];
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
