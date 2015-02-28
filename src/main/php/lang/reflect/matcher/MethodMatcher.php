<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\matcher;
use stubbles\lang\reflect\ReflectionMethod;
/**
 * Interface for matching methods.
 *
 * @api
 * @deprecated  will be removed with 6.0.0
 */
interface MethodMatcher
{
    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   \ReflectionMethod  $method
     * @return  bool
     */
    public function matchesMethod(\ReflectionMethod $method);

    /**
     * checks whether the matcher is satisfied with the given method
     *
     * @param   \stubbles\lang\reflect\ReflectionMethod  $method
     * @return  bool
     */
    public function matchesAnnotatableMethod(ReflectionMethod $method);
}
