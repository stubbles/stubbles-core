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
 * Container for http constants.
 *
 * @since  2.0.0
 */
class Http
{
    /**
     * default uri scheme
     */
    const SCHEME                    = 'http';
    /**
     * uri scheme for ssl
     */
    const SCHEME_SSL                = 'https';
    /**
     * default non-ssl port
     */
    const PORT                      = 80;
    /**
     * default ssl port
     */
    const PORT_SSL                  = 443;
    /**
     * request method type: GET
     */
    const GET                       = 'GET';
    /**
     * request method type: POST
     */
    const POST                      = 'POST';
    /**
     * request method type: HEAD
     */
    const HEAD                      = 'HEAD';
    /**
     * request method type: PUT
     */
    const PUT                       = 'PUT';
    /**
     * request method type: DELETE
     */
    const DELETE                    = 'DELETE';
    /**
     * request method type: OPTIONS
     *
     * @since  4.0.0
     */
    const OPTIONS                   = 'OPTIONS';
    /**
     * HTTP version: 1.0
     *
     * @deprecated  since 4.0.0, use stubbles\peer\http\HttpVersion instead, will be removed with 5.0.0
     */
    const VERSION_1_0               = 'HTTP/1.0';
    /**
     * HTTP version: 1.1
     *
     * @deprecated  since 4.0.0, use stubbles\peer\http\HttpVersion instead, will be removed with 5.0.0
     */
    const VERSION_1_1               = 'HTTP/1.1';

    /**
     * end-of-line marker
     */
    const END_OF_LINE               = "\r\n";

    /**
     * response status class: informational (100-199)
     */
    const STATUS_CLASS_INFO         = 'Informational';
    /**
     * response status class: successful request (200-299)
     */
    const STATUS_CLASS_SUCCESS      = 'Success';
    /**
     * response status class: redirection (300-399)
     */
    const STATUS_CLASS_REDIRECT     = 'Redirection';
    /**
     * response status class: errors by client (400-499)
     */
    const STATUS_CLASS_ERROR_CLIENT = 'Client Error';
    /**
     * response status class: errors on server (500-599)
     */
    const STATUS_CLASS_ERROR_SERVER = 'Server Error';
    /**
     * response status class: unknown status code
     */
    const STATUS_CLASS_UNKNOWN      = 'Unknown';
    /**
     * reference to RFC 2616 which defined HTTP/1.1 first
     *
     * @link   http://tools.ietf.org/html/rfc2616
     * @since  4.0.0
     */
    const RFC_2616                  = 'RFC 2616';
    /**
     * reference to RFC 7230, a revised version of HTTP/1.1
     *
     * @ink    http://tools.ietf.org/html/rfc7230
     * @since  4.0.0
     */
    const RFC_7230                  = 'RFC 7230';

    /**
     * map of status code classes
     *
     * @type type
     */
    private static $statusClass     = [0 => Http::STATUS_CLASS_UNKNOWN,
                                       1 => Http::STATUS_CLASS_INFO,
                                       2 => Http::STATUS_CLASS_SUCCESS,
                                       3 => Http::STATUS_CLASS_REDIRECT,
                                       4 => Http::STATUS_CLASS_ERROR_CLIENT,
                                       5 => Http::STATUS_CLASS_ERROR_SERVER
                                      ];
    /**
     * map of status codes to reason phrases
     *
     * @type  array
     */
    private static $reasonPhrases   = [100 => 'Continue',
                                       101 => 'Switching Protocols',
                                       102 => 'Processing',
                                       118 => 'Connection timed out',
                                       200 => 'OK',
                                       201 => 'Created',
                                       202 => 'Accepted',
                                       203 => 'Non-Authoritative Information',
                                       204 => 'No Content',
                                       205 => 'Reset Content',
                                       206 => 'Partial Content',
                                       207 => 'Multi-Status',
                                       300 => 'Multiple Choices',
                                       301 => 'Moved Permanently',
                                       302 => 'Found',
                                       303 => 'See Other',
                                       304 => 'Not Modified',
                                       305 => 'Use Proxy',
                                       307 => 'Temporary Redirect',
                                       400 => 'Bad Request',
                                       401 => 'Unauthorized',
                                       402 => 'Payment Required',
                                       403 => 'Forbidden',
                                       404 => 'Not Found',
                                       405 => 'Method Not Allowed',
                                       406 => 'Not Acceptable',
                                       407 => 'Proxy Authentication Required',
                                       408 => 'Request Timeout',
                                       409 => 'Conflict',
                                       410 => 'Gone',
                                       411 => 'Length Required',
                                       412 => 'Precondition Failed',
                                       413 => 'Request Entity Too Large',
                                       414 => 'Request-URI Too Long',
                                       415 => 'Unsupported Media Type',
                                       416 => 'Requested Range Not Satisfiable',
                                       417 => 'Expectation Failed',
                                       418 => 'I\'m a Teapot',
                                       421 => 'There are too many connections from your internet address',
                                       422 => 'Unprocessable Entity',
                                       423 => 'Locked',
                                       424 => 'Failed Dependency',
                                       425 => 'Unordered Collection',
                                       426 => 'Upgrade Required',
                                       500 => 'Internal Server Error',
                                       501 => 'Not Implemented',
                                       502 => 'Bad Gateway',
                                       503 => 'Service Unavailable',
                                       504 => 'Gateway Timeout',
                                       505 => 'HTTP Version Not Supported',
                                       506 => 'Variant Also Negotiates',
                                       507 => 'Insufficient Storage',
                                       509 => 'Bandwidth Limit Exceeded',
                                       510 => 'Not Extended'
                                      ];

    /**
     * checks if given http version is valid
     *
     * @api
     * @param   string  $version
     * @return  bool
     * @deprecated  since 4.0.0, use stubbles\peer\http\HttpVersion instead, will be removed with 5.0.0
     */
    public static function isVersionValid($version)
    {
        if (self::VERSION_1_0 == $version || self::VERSION_1_1 == $version) {
            return true;
        }

        return false;
    }

    /**
     * returns status class for given status code
     *
     * Returns null if given status code is empty.
     *
     * @api
     * @param   int  $statusCode
     * @return  string
     * @since   4.0.0
     */
    public static function statusClassFor($statusCode)
    {
        $class = substr($statusCode, 0, 1);
        if (isset(self::$statusClass[$class])) {
            return self::$statusClass[$class];
        }

        return self::STATUS_CLASS_UNKNOWN;
    }

    /**
     * returns status class for given status code
     *
     * Returns null if given status code is empty.
     *
     * @api
     * @param   int  $statusCode
     * @return  string
     * @deprecated  since 4.0.0, use statusClassFor() instead, will be removed with 5.0.0
     */
    public static function getStatusClass($statusCode)
    {
        $class = substr($statusCode, 0, 1);
        if (isset(self::$statusClass[$class])) {
            return self::$statusClass[$class];
        }

        return self::statusClassFor($statusCode);
    }

    /**
     * returns list of known status codes
     *
     * @api
     * @return  array
     * @since   4.0.0
     */
    public static function statusCodes()
    {
        return self::$reasonPhrases;
    }

    /**
     * returns list of known status codes
     *
     * @api
     * @return  array
     * @deprecated  since 4.0.0, use statusCodes() instead, will be removed with 5.0.0
     */
    public static function getStatusCodes()
    {
        return self::statusCodes();
    }

    /**
     * returns reason phrase for given status code
     *
     * @api
     * @param   int  $statusCode
     * @return  string
     * @throws  IllegalArgumentException
     * @since   4.0.0
     */
    public static function reasonPhraseFor($statusCode)
    {
        if (isset(self::$reasonPhrases[$statusCode])) {
            return self::$reasonPhrases[$statusCode];
        }

        throw new IllegalArgumentException('Invalid or unknown HTTP status code ' . $statusCode);
    }

    /**
     * returns reason phrase for given status code
     *
     * @api
     * @param   int  $statusCode
     * @return  string
     * @throws  IllegalArgumentException
     * @deprecated  since 4.0.0, use reasonPhraseFor() instead, will be removed with 5.0.0
     */
    public static function getReasonPhrase($statusCode)
    {
        if (isset(self::$reasonPhrases[$statusCode])) {
            return self::$reasonPhrases[$statusCode];
        }

        throw new IllegalArgumentException('Invalid or unknown HTTP status code ' . $statusCode);
    }

    /**
     * creates valid http line
     *
     * @param   string  $line
     * @return  string
     */
    public static function line($line)
    {
        return $line . self::END_OF_LINE;
    }

    /**
     * creates empty http line
     *
     * @return  string
     */
    public static function emptyLine()
    {
        return self::END_OF_LINE;
    }

    /**
     * checks if given RFC is a valid and known RFC
     *
     * @param   string  $rfc
     * @return  bool
     */
    public static function isValidRfc($rfc)
    {
        return in_array($rfc, [self::RFC_2616, self::RFC_7230]);
    }
}
