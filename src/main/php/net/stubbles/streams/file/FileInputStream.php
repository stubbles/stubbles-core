<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\file;
use net\stubbles\lang\exception\IllegalArgumentException;
use net\stubbles\lang\exception\IllegalStateException;
use net\stubbles\lang\exception\IOException;
use net\stubbles\streams\ResourceInputStream;
use net\stubbles\streams\Seekable;
/**
 * Class for file based input streams.
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
     * @throws  IOException
     * @throws  IllegalArgumentException
     */
    public function __construct($file, $mode = 'rb')
    {
        if (is_string($file) === true) {
            $fp = @fopen($file, $mode);
            if (false === $fp) {
                throw new IOException('Can not open file ' . $file . ' with mode ' . $mode);
            }

            $this->fileName = $file;
        } elseif (is_resource($file) === true && get_resource_type($file) === 'stream') {
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
     * helper method to retrieve the length of the resource
     *
     * Not all stream wrappers support (f)stat - the extending class then
     * needs to take care to deliver the correct resource length then.
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
     * @throws  IllegalStateException
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
     * @throws  IllegalStateException
     * @throws  IOException
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
?>