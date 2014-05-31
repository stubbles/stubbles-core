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
 * Encodes internal encoding into output charset.
 *
 * @api
 */
class EncodingOutputStream extends AbstractDecoratedOutputStream
{
    /**
     * charset of output stream
     *
     * @type  string
     */
    protected $charset;

    /**
     * constructor
     *
     * @param  OutputStream  $outputStream
     * @param  string        $charset       charset of output stream
     */
    public function __construct(OutputStream $outputStream, $charset)
    {
        parent::__construct($outputStream);
        $this->charset      = $charset;
    }

    /**
     * returns charset of output stream
     *
     * @return  string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * writes given bytes
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes
     */
    public function write($bytes)
    {
        return $this->outputStream->write(iconv('UTF-8', $this->charset, $bytes));
    }

    /**
     * writes given bytes and appends a line break after each one
     *
     * @param   string[]  $bytes
     * @return  int       amount of written bytes
     * @since   3.2.0
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
     * writes given bytes and appends a line break
     *
     * @param   string  $bytes
     * @return  int     amount of written bytes excluding line break
     */
    public function writeLine($bytes)
    {
        return $this->outputStream->writeLine(iconv('UTF-8', $this->charset, $bytes));
    }
}
