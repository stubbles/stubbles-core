<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\predicate;
use stubbles\peer\MalformedUriException;
use stubbles\peer\http\HttpUri;
/**
 * Predicate to test that a string is a http uri.
 *
 * @api
 * @since  4.0.0
 */
class IsHttpUri extends Predicate
{
    /**
     * whether to check dns for existence of given url or not
     *
     * @type  bool
     */
    private $checkDns = false;

    /**
     * enables dns check for test
     *
     * Enabling the dns check means that even if the HTTP URI is syntactically
     * valid it must have an DNS entry to be valid at all.
     *
     * @return  HttpUriPredicate
     */
    public function enableDnsCheck()
    {
        $this->checkDns = true;
        return $this;
    }

    /**
     * test that the given value is a http url
     *
     * @param   string  $value
     * @return  bool
     */
    public function test($value)
    {
        if (empty($value)) {
            return false;
        }

        try {
            $uri = HttpUri::fromString($value);
            if ($this->checkDns) {
                return $uri->hasDnsRecord();
            }
        } catch (MalformedUriException $murle) {
            return false;
        }

        return true;
    }
}
