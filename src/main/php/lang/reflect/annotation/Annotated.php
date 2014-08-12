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
 * Description of Annotated
 *
 * @since  5.0.0
 */
trait Annotated
{
    /**
     * check whether the given annotation is present or not
     *
     * @param   string  $annotationName
     * @return  bool
     */
    public function hasAnnotation($annotationName)
    {
        return AnnotationFactory::has($this->getDocComment(), $annotationName, $this->annotationTargetName());
    }

    /**
     * return the specified annotation
     *
     * @param   string  $annotationName
     * @return  \stubbles\lang\reflect\annotation\Annotation
     */
    public function getAnnotation($annotationName)
    {
        return AnnotationFactory::create($this->getDocComment(), $annotationName, $this->annotationTargetName());
    }

    /**
     * returns map of all annotations for this element
     *
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     * @since   5.0.0
     */
    public function annotations()
    {
        return AnnotationFactory::createAll($this->getDocComment(), $this->annotationTargetName());
    }

    /**
     * target name of property annotations
     *
     * @return  string
     */
    protected abstract function annotationTargetName();
}
