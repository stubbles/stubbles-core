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
use stubbles\peer\ParsedUri;
/**
 * Class for URIs and methods on URIs.
 *
 * @internal
 */
class ConstructedHttpUri extends HttpUri
{
    /**
     * constructor
     *
     * @param  \stubbles\peer\ParsedUri  $uri
     */
    protected function __construct(ParsedUri $uri)
    {
        $this->parsedUri = $uri;
    }
}
