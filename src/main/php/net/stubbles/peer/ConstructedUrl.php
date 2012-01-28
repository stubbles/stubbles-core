<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\peer;
use net\stubbles\lang\BaseObject;
/**
 * Class for URLs and methods on URLs.
 */
class ConstructedUrl extends Url
{
    /**
     * constructor
     *
     * @param  ParsedUrl  $url
     */
    protected function __construct(ParsedUrl $url)
    {
        $this->parsedUrl = $url;
    }
}
?>