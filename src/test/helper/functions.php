<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\peer\http
{
    class CheckdnsrrResult
    {
        public static $value = null;
    }

    function checkdnsrr($host, $type = 'MX')
    {
        return CheckdnsrrResult::$value;
    }
}