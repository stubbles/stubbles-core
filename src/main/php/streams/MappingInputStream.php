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
/**
 * Input stream which maps the result from the underlying input stream.
 *
 * @api
 * @since  4.0.0
 */
class MappingInputStream extends AbstractDecoratedInputStream
{
    /**
     * mapper to apply
     *
     * @type  callable
     */
    private $mapper;

    /**
     * constructor
     *
     * @param  \stubbles\streams\InputStream  $inputStream
     * @param  callable  $mapper  mapper to apply
     */
    public function __construct(InputStream $inputStream, callable $mapper)
    {
        parent::__construct($inputStream);
        $this->mapper = $mapper;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        return $this->map(parent::read($length));
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        return $this->map(parent::readLine($length));
    }

    /**
     * actual mapping
     *
     * @param   string  $line
     * @return  string
     */
    private function map($line)
    {
        $map = $this->mapper;
        return $map($line);
    }
}
