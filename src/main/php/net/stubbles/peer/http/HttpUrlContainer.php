<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer\http;
use net\stubbles\peer\HeaderList;
use net\stubbles\peer\UrlContainer;
/**
 * Interface for URLs of scheme hypertext transfer protocol.
 */
interface HttpUrlContainer extends UrlContainer
{
    /**
     * checks whether current scheme is http
     *
     * @return  bool
     * @since   2.0.0
     */
    public function isHttp();

    /**
     * checks whether current scheme is https
     *
     * @return  bool
     * @since   2.0.0
     */
    public function isHttps();

    /**
     * transposes url to http
     *
     * @return  HttpUrl
     * @since   2.0.0
     */
    public function toHttp();

    /**
     * transposes url to https
     *
     * @return  HttpUrl
     * @since   2.0.0
     */
    public function toHttps();

    /**
     * creates a http connection for this url
     *
     * To submit a complete HTTP request use this:
     * <code>
     * $response = $url->connect()->asUserAgent('Not Mozilla')
     *                            ->timeout(5)
     *                            ->usingHeader('X-Money', 'Euro')
     *                            ->get();
     * </code>
     *
     * @param   HeaderList  $headers  list of headers to be used
     * @return  HttpConnection
     */
    public function connect(HeaderList $headers = null);

    /**
     * opens socket to this url
     *
     * @param   int  $timeout  connection timeout
     * @return  Socket
     * @since   2.0.0
     */
    public function openSocket($timeout);
}
?>