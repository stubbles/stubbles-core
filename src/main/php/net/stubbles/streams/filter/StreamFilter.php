<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\streams\filter;
use net\stubbles\lang\Object;
/**
 * Interface for stream filters.
 */
interface StreamFilter extends Object
{
    /**
     * Decides whether data should be filtered or not.
     *
     * @param   string  $data
     * @return  bool
     */
    public function shouldFilter($data);
}
?>