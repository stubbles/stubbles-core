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
use stubbles\predicate\Predicate;
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
     * stream filter to be applied
     *
     * @type  stubbles\predicate\Predicate
     */
    private $predicate;

    /**
     * constructor
     *
     * @param   InputStream                      $inputStream   input stream to filter
     * @param   StreamFilter|callable|Predicate  $predicate     predicate to check if something should be passed
     */
    public function __construct(InputStream $inputStream, $predicate)
    {
        parent::__construct($inputStream);
        if ($predicate instanceof StreamFilter) {
            $this->predicate = new StreamFilterPredicate($predicate);
        } else {
            $this->predicate = Predicate::castFrom($predicate);
        }
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        while (!$this->inputStream->eof()) {
            $data = $this->inputStream->read($length);
            if ($this->predicate->test($data)) {
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
        while (!$this->inputStream->eof()) {
            $data = $this->inputStream->readLine($length);
            if ($this->predicate->test($data)) {
                return $data;
            }
        }

        return '';
    }
}
