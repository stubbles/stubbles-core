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
 * Class for resource based output streams.
 *
 * @internal
 */
abstract class ResourceOutputStream implements OutputStream
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
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     * @throws  \stubbles\lang\exception\IllegalStateException
     * @throws  \stubbles\lang\exception\IOException
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
     * @return  int     amount of written bytes
     */
    public function writeLine($bytes)
    {
        return $this->write($bytes . "\r\n");
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
        if (null !== $this->handle) {
            fclose($this->handle);
            $this->handle = null;
        }
    }
}
