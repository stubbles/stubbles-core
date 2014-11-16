<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\file;
use stubbles\lang\exception\IllegalArgumentException;
use stubbles\lang\exception\IllegalStateException;
use stubbles\lang\exception\IOException;
use stubbles\streams\InputStream;
use stubbles\streams\ResourceInputStream;
use stubbles\streams\Seekable;
/**
 * Class for file based input streams.
 *
 * @api
 */
class FileInputStream extends ResourceInputStream implements Seekable
{
    /**
     * name of the file
     *
     * @type  string
     */
    protected $fileName;

    /**
     * constructor
     *
     * @param   string|resource  $file
     * @param   string           $mode  opening mode if $file is a filename
     * @throws  \stubbles\lang\exception\IOException
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     */
    public function __construct($file, $mode = 'rb')
    {
        if (is_string($file)) {
            $fp = @fopen($file, $mode);
            if (false === $fp) {
                throw new IOException('Can not open file ' . $file . ' with mode ' . $mode);
            }

            $this->fileName = $file;
        } elseif (is_resource($file) && get_resource_type($file) === 'stream') {
            $fp = $file;
        } else {
            throw new IllegalArgumentException('File must either be a filename or an already opened file/stream resource.');
        }

        $this->setHandle($fp);
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * casts given value to an input stream
     *
     * @param   \stubbles\streams\InputStream|string  $value
     * @return  \stubbles\streams\InputStream
     * @throws  IllegalArgumentException
     * @since   5.2.0
     */
    public static function castFrom($value)
    {
        if ($value instanceof InputStream) {
            return $value;
        }

        if (is_string($value)) {
            return new self($value);
        }

        throw new IllegalArgumentException('Given value is neither an instance of stubbles\streams\InputStream nor a string denoting a file');
    }

    /**
     * helper method to retrieve the length of the resource
     *
     * @return  int
     */
    protected function getResourceLength()
    {
        if (null === $this->fileName) {
            return parent::getResourceLength();
        }

        if (substr($this->fileName, 0, 16) === 'compress.zlib://') {
            return filesize(substr($this->fileName, 16));
        } elseif (substr($this->fileName, 0, 17) === 'compress.bzip2://') {
            return filesize(substr($this->fileName, 17));
        }
    }

    /**
     * seek to given offset
     *
     * @param   int  $offset
     * @param   int  $whence  one of Seekable::SET, Seekable::CURRENT or Seekable::END
     * @throws  \stubbles\lang\exception\IllegalStateException
     */
    public function seek($offset, $whence = Seekable::SET)
    {
        if (null === $this->handle) {
            throw new IllegalStateException('Can not read from closed input stream.');
        }

        fseek($this->handle, $offset, $whence);
    }

    /**
     * return current position
     *
     * @return  int
     * @throws  \stubbles\lang\exception\IllegalStateException
     * @throws  \stubbles\lang\exception\IOException
     */
    public function tell()
    {
        if (null === $this->handle) {
            throw new IllegalStateException('Can not read from closed input stream.');
        }

        $position = ftell($this->handle);
        if (false === $position) {
            throw new IOException('Can not read current position in file.');
        }

        return $position;
    }
}
