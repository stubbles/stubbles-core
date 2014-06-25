<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\predicate;
/**
 * Class for validating that a string denotes an existing path.
 *
 *
 * @since  4.0.0
 */
abstract class FilesystemPredicate extends Predicate
{
    /**
     * base path where file must reside in
     *
     * @type  string
     */
    private $basePath;

    /**
     * constructor
     *
     * If no base path is given the test will be done against the whole
     * file system, given values can not be relative then.
     *
     * @param  string  $basePath
     */
    public function __construct($basePath = null)
    {
        $this->basePath = $basePath;
    }

    /**
     * test that the given value is represents an existing path
     *
     * @param   string|null  $value
     * @return  bool
     */
    public function test($value)
    {
        if (empty($value)) {
            return false;
        }

        if (null !== $this->basePath) {
            return $this->fileExists($this->basePath . '/' . $value);
        }

        return $this->fileExists($value);
    }

    /**
     * checks if given file exists
     *
     * @param   string  $path
     * @return  bool
     */
    protected abstract function fileExists($path);
}
