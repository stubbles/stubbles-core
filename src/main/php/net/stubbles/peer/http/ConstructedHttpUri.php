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
use net\stubbles\lang\BaseObject;
use net\stubbles\peer\ParsedUri;
/**
 * Class for URIs and methods on URIs.
 */
class ConstructedHttpUri extends HttpUri
{
    /**
     * constructor
     *
     * @param   ParsedUri  $uri
     */
    protected function __construct(ParsedUri $uri)
    {
        $this->parsedUri = $uri;
        if ($this->parsedUri->getPath() == null) {
            $this->parsedUri = $this->parsedUri->transpose(array("path" => "/"));
        }
    }
}
?>