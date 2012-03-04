<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams;
/**
 * Decodes input stream into internal charset.
 *
 * @api
 */
class DecodingInputStream extends AbstractDecoratedInputStream
{
    /**
     * charset of input stream
     *
     * @type  string
     */
    protected $charset;

    /**
     * constructor
     *
     * @param  InputStream  $inputStream
     * @param  string       $charset      charset of input stream
     */
    public function __construct(InputStream $inputStream, $charset)
    {
        parent::__construct($inputStream);
        $this->charset     = $charset;
    }

    /**
     * returns charset of input stream
     *
     * @return  string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * reads given amount of bytes
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function read($length = 8192)
    {
        return iconv($this->charset, 'UTF-8', $this->inputStream->read($length));
    }

    /**
     * reads given amount of bytes or until next line break
     *
     * @param   int  $length  max amount of bytes to read
     * @return  string
     */
    public function readLine($length = 8192)
    {
        return iconv($this->charset, 'UTF-8', $this->inputStream->readLine($length));
    }
}
?>