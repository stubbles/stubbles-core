<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\exception;
/**
 * Exception to be thrown in case a file could not be found.
 */
class FileNotFoundException extends IOException
{
    /**
     * constructor
     *
     * @param  string  $fileName  name of file that was not found
     */
    public function __construct($fileName)
    {
        parent::__construct("The file {$fileName} could not be found or is not readable.");
    }
}
