<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect\matcher;
use net\stubbles\lang\reflect\ReflectionProperty;
/**
 * Interface for matching properties.
 *
 * @api
 */
interface PropertyMatcher
{
    /**
     * checks whether the matcher is satisfied with the given property
     *
     * @param   \ReflectionProperty  $property
     * @return  bool
     */
    public function matchesProperty(\ReflectionProperty $property);

    /**
     * checks whether the matcher is satisfied with the given property
     *
     * @param   ReflectionProperty  $property
     * @return  bool
     */
    public function matchesAnnotatableProperty(ReflectionProperty $property);
}
?>