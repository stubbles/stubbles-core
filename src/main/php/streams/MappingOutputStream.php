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
 * Output stream which maps the input to the underlying output stream.
 *
 * @api
 * @since  4.0.0
 */
class MappingOutputStream extends AbstractDecoratedOutputStream
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
     * @param  \stubbles\streams\OutputStream  $outputStream
     * @param  callable  $mapper  mapper to apply
     */
    public function __construct(OutputStream $outputStream, callable $mapper)
    {
        parent::__construct($outputStream);
        $this->mapper = $mapper;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        return parent::write($this->map($bytes));
    }

    /**
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        return parent::writeLine($this->map($bytes));
    }

    /**
     * writes given list of bytes and appends a line break after each one
     *
     * @param   string[]  $bytes
     * @return  int       amount of written bytes
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
     * actual mapping
     *
     * @param   string  $bytes
     * @return  string
     */
    private function map($bytes)
    {
        $map = $this->mapper;
        return $map($bytes);
    }
}
