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
 * Stream filter which uses a callable to do the actual filter check.
 *
 * @since  4.0.0
 */
class CallableStreamFilter implements StreamFilter
{
    /**
     * callable to apply for filtering
     *
     * @type  callable
     */
    private $callable;

    /**
     * constructor
     *
     * @param  callable  $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * Decides whether data should be filtered or not.
     *
     * @param   string  $data
     * @return  bool
     */
    public function shouldFilter($data)
    {
        $callable = $this->callable;
        return $callable($data);
    }
}
