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
 * Predicate to test that a string is an existing http uri, i.e. has a DNS record.
 *
 * @api
 * @since  4.0.0
 */
class IsExistingHttpUri extends Predicate
{
    use ReusablePredicate;

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
            return HttpUri::fromString($value)->hasDnsRecord();
        } catch (MalformedUriException $murle) {
            return false;
        }
    }
}
