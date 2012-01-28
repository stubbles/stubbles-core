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
use net\stubbles\peer\ParsedUrl;
/**
 * Class for URLs and methods on URLs.
 */
class ConstructedHttpUrl extends HttpUrl
{
    /**
     * constructor
     *
     * @param   ParsedUrl  $url
     */
    protected function __construct(ParsedUrl $url)
    {
        $this->parsedUrl = $url;
        if ($this->parsedUrl->getPath() == null) {
            $this->parsedUrl = $this->parsedUrl->transpose(array("path" => "/"));
        }
    }
}
?>