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
use function stubbles\lastErrorMessage;
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
     * @throws  \InvalidArgumentException
     */
    protected function setHandle($handle)
    {
        if (!is_resource($handle)) {
            throw new \InvalidArgumentException(
                    'Handle needs to be a stream resource.'
            );
        }

        $this->handle = $handle;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     * @throws  \LogicException
     * @throws  \stubbles\streams\StreamException
     */
    public function read($length = 8192)
    {
        if (null === $this->handle) {
            throw new \LogicException('Can not read from closed input stream.');
        }

        return $this->doRead('fread', $length);
    }

    /**
     * reads given amount of bytes or until next line break and removes line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     * @throws  \LogicException
     * @throws  \stubbles\streams\StreamException
     */
    public function readLine($length = 8192)
    {
        if (null === $this->handle) {
            throw new \LogicException('Can not read from closed input stream.');
        }

        return rtrim($this->doRead('fgets', $length), "\n\r");
    }

    /**
     * do actual read
     *
     * @param   string  $read    function to use for reading from handle
     * @param   int     $length  max amount of bytes to read
     * @return  string
     * @throws  \stubbles\streams\StreamException
     */
    private function doRead($read, $length)
    {
        $data = @$read($this->handle, $length);
        if (false === $data) {
            $error = lastErrorMessage()->whenEmpty('unknown error');
            if (!@feof($this->handle)) {
                throw new StreamException(
                        'Can not read from input stream: ' . $error->value()
                );
            }

            return '';
        }

        return $data;
    }

    /**
     * returns the amount of bytes left to be read
     *
     * @return  int
     * @throws  \LogicException
     */
    public function bytesLeft()
    {
        if (null === $this->handle || !is_resource($this->handle)) {
            throw new \LogicException('Can not read from closed input stream.');
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
