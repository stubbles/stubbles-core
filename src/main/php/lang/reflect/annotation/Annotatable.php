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
     * checks whether at least on occurrence of this annotation is present
     *
     * @param   string  $type
     * @return  bool
     */
    public function hasAnnotation($type);

    /**
     * return the specified annotation
     *
     * In case there is more than one annotation of this type the first one is
     * returned. To retrieve all annotations of a certain type use
     * annotations()->of($type) instead.
     *
     * @param   string  $type
     * @return  \stubbles\lang\reflect\annotation\Annotation
     * @throws  \ReflectionException  when annotation is not present
     */
    public function annotation($type);
    /**
     * return the specified annotation
     *
     * In case there is more than one annotation of this type the first one is
     * returned. To retrieve all annotations of a certain type use
     * annotations()->of($type) instead.
     *
     * @param   string  $type
     * @return  \stubbles\lang\reflect\annotation\Annotation
     * @throws  \ReflectionException  when annotation is not present
     * @deprecated  since 5.0.0, use annotation() instead
     */
    public function getAnnotation($type);

    /**
     * returns all annotations for this element
     *
     * @return  \stubbles\lang\reflect\annotation\Annotations
     * @since   5.0.0
     */
    public function annotations();
}
