<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles {

    /**
     * returns error message from last error that occurred
     *
     * @param   string  $default  optional  message to return in case no last error available
     * @return  \stubbles\Result
     * @since   3.4.2
     */
    function lastErrorMessage()
    {
        return Result::of(error_get_last())
                ->map(function(array $error) { return $error['message']; });
    }
}
