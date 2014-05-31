<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\lang\reflect\annotation;
/**
 * Interface for reflected structures that may have annotations.
 *
 * @api
 */
interface Annotatable
{
    /**
     * check whether the class has the given annotation or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName);

    /**
     * return the specified annotation
     *
     * @param   string          $annotationName
     * @return  stubAnnotation
     */
    public function getAnnotation($annotationName);
}
