<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer\http;
use stubbles\lang\exception\IllegalArgumentException;
/**
 * Represents a HTTP version.
 *
 * @since  4.0.0
 * @link   http://tools.ietf.org/html/rfc7230#section-2.6
 */
class HttpVersion
{
    /**
     * major http version
     *
     * @type  int
     */
    private $major;
    /**
     * minor http version
     *
     * @type  int
     */
    private $minor;

    /**
     * parses http version from given string
     *
     * @param   string  $httpVersion  a http version string like "HTTP/1.1"
     * @return  Version
     * @throws  IllegalArgumentException  in case string can not be parsed successfully
     */
    public static function fromString($httpVersion)
    {
        if (empty($httpVersion)) {
            throw new IllegalArgumentException('Given HTTP version is empty');
        }

        $major = null;
        $minor = null;
        if (2 != sscanf($httpVersion, 'HTTP/%d.%d', $major, $minor)) {
            throw new IllegalArgumentException('Given HTTP version "' . $httpVersion . '" can not be parsed');
        }

        return new self($major, $minor);
    }

    /**
     * constructor
     *
     * In case the given major or minor version can not be casted to a valid
     * integer or casting them yields a different result an
     * IllegalArgumentException is thrown.
     *
     * @param  int|string  $major
     * @param  int|string  $minor
     */
    public function __construct($major, $minor)
    {
        $this->major = $this->castInt($major, 'major');
        $this->minor = $this->castInt($minor, 'minor');
    }

    /**
     * casts given number to integer
     *
     * @param   int|string  $number
     * @param   int|string  $type
     * @return  int
     * @throws  IllegalArgumentException
     */
    private function castInt($number, $type)
    {
        $result = (int) $number;
        if (strlen($result) !== strlen($number)) {
            throw new IllegalArgumentException('Given ' . $type . ' version "' . $number . '" is not an integer');
        }

        if (0 > $result) {
            throw new IllegalArgumentException(ucfirst($type) . ' version can not be negative');
        }

        return $result;
    }

    /**
     * returns major version number
     *
     * @return  int
     */
    public function major()
    {
        return $this->major;
    }

    /**
     * returns minor version number
     *
     * @return  int
     */
    public function minor()
    {
        return $this->minor;
    }

    /**
     * returns string representation
     *
     * @return  string
     */
    public function __toString()
    {
        return 'HTTP/' . $this->major . '.' . $this->minor;
    }
}

