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
 * @deprecated  since 7.0.0, will be removed with 8.0.0
 */
class IsHttpUri extends Predicate
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
            HttpUri::fromString($value);
        } catch (MalformedUriException $murle) {
            return false;
        }

        return true;
    }
}
