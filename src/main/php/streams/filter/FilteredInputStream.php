<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\filter;
use stubbles\streams\AbstractDecoratedInputStream;
use stubbles\streams\InputStream;
/**
 * Input stream applying a filter on data read before returning to requestor.
 *
 * @api
 */
class FilteredInputStream extends AbstractDecoratedInputStream
{
    /**
     * predicate which decides on whether a line is acceptable
     *
     * @type  callable
     */
    private $predicate;

    /**
     * constructor
     *
     * @param   \stubbles\streams\InputStream  $inputStream  input stream to filter
     * @param   callable                       $predicate    predicate to check if something should be passed
     */
    public function __construct(InputStream $inputStream, callable $predicate)
    {
        parent::__construct($inputStream);
        $this->predicate = $predicate;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        $isAcceptable = $this->predicate;
        while (!$this->inputStream->eof()) {
            $data = $this->inputStream->read($length);
            if ($isAcceptable($data)) {
                return $data;
            }
        }

        return '';
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        $isAcceptable = $this->predicate;
        while (!$this->inputStream->eof()) {
            $data = $this->inputStream->readLine($length);
            if ($isAcceptable($data)) {
                return $data;
            }
        }

        return '';
    }
}
