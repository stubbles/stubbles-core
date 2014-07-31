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
use stubbles\lang\exception\IOException;
use stubbles\streams\ResourceOutputStream;
/**
 * Class for file based output streams.
 *
 * @api
 */
class FileOutputStream extends ResourceOutputStream
{
    /**
     * name of file
     *
     * @type  string
     */
    protected $file;
    /**
     * opening mode
     *
     * @type  string
     */
    protected $mode;

    /**
     * constructor
     *
     * The delayed param only works in conjunction with the $file param being a
     * string. If set to true and the file does not exist creation of the file
     * will be delayed until first bytes should be written to the output stream.
     *
     * @param   string|resource  $file
     * @param   string           $mode     opening mode if $file is a filename
     * @param   bool             $delayed
     * @throws  \stubbles\lang\exception\IllegalArgumentException
     */
    public function __construct($file, $mode = 'wb', $delayed = false)
    {
        if (is_string($file)) {
            if (false === $delayed) {
                $this->setHandle($this->openFile($file, $mode));
            } else {
                $this->file = $file;
                $this->mode = $mode;
            }
        } elseif (is_resource($file) && get_resource_type($file) === 'stream') {
            $this->setHandle($file);
        } else {
            throw new IllegalArgumentException('File must either be a filename or an already opened file/stream resource.');
        }
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        if ($this->isFileCreationDelayed()) {
            $this->setHandle($this->openFile($this->file, $this->mode));
        }

        return parent::write($bytes);
    }

    /**
     * checks whether file creation was delayed
     *
     * @return  bool
     */
    protected function isFileCreationDelayed()
    {
        return (null === $this->handle && null != $this->file);
    }

    /**
     * helper method to open a file handle
     *
     * @param   string   $file
     * @param   string   $mode
     * @return  resource
     * @throws  \stubbles\lang\exception\IOException
     */
    protected function openFile($file, $mode)
    {
        $fp = @fopen($file, $mode);
        if (false === $fp) {
            throw new IOException('Can not open file ' . $file . ' with mode ' . $mode);
        }

        return $fp;
    }
}
