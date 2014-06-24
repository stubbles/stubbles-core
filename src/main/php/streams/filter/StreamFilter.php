<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\streams\filter;
/**
 * Interface for stream filters.
 *
 * @api
 * @deprecated  since 4.0.0, use predicates instead, will be removed with 5.0.0
 */
interface StreamFilter
{
    /**
     * Decides whether data should be filtered or not.
     *
     * @param   string  $data
     * @return  bool
     */
    public function shouldFilter($data);
}
