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
use stubbles\streams\StreamFactory;
/**
 * Factory for file streams.
 */
class FileStreamFactory implements StreamFactory
{
    /**
     * default file mode if directory for output stream should be created
     *
     * @type  int
     */
    protected $fileMode;

    /**
     * constructor
     *
     * @param  int  $fileMode  default file mode if directory for output stream should be created
     * @Named('stubbles.filemode')
     * @Property('stubbles.filemode')
     */
    public function __construct($fileMode = 0700)
    {
        $this->fileMode = $fileMode;
    }

    /**
     * creates an input stream for given source
     *
     * @param   mixed  $source   source to create input stream from
     * @param   array  $options  list of options for the input stream
     * @return  \stubbles\streams\file\FileInputStream
     */
    public function createInputStream($source, array $options = [])
    {
        if (isset($options['filemode'])) {
            return new FileInputStream($source, $options['filemode']);
        }

        return new FileInputStream($source);
    }

    /**
     * creates an output stream for given target
     *
     * @param   mixed  $target   target to create output stream for
     * @param   array  $options  list of options for the output stream
     * @return  \stubbles\streams\file\FileOutputStream
     */
    public function createOutputStream($target, array $options = [])
    {
        if (isset($options['createDirIfNotExists']) && true === $options['createDirIfNotExists']) {
            $dir = dirname($target);
            if (!file_exists($dir)) {
                $filemode = ((!isset($options['dirPermissions'])) ? ($this->fileMode) : ($options['dirPermissions']));
                mkdir($dir, $filemode, true);
            }
        }

        $filemode = (!isset($options['filemode'])) ? ('wb') : ($options['filemode']);
        $delayed  = (!isset($options['delayed'])) ? (false) : ($options['delayed']);
        return new FileOutputStream($target, $filemode, $delayed);
    }
}
