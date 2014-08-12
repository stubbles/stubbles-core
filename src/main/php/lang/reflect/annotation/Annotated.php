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
use stubbles\lang\reflect\annotation\parser\AnnotationStateParser;
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
        $annotations = $this->annotations();
        return isset($annotations[$annotationName]);
    }

    /**
     * return the specified annotation
     *
     * @param   string  $annotationName
     * @return  \stubbles\lang\reflect\annotation\Annotation
     * @throws  \ReflectionException  when annotation is not present
     */
    public function getAnnotation($annotationName)
    {
        $annotations = $this->annotations();
        if (!isset($annotations[$annotationName])) {
            throw new \ReflectionException('Can not find annotation ' . $annotationName);
        }

        return $annotations[$annotationName];
    }

    /**
     * returns map of all annotations for this element
     *
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     * @since   5.0.0
     */
    public function annotations()
    {
        $target = $this->annotationTargetName();
        if (AnnotationCache::has($target)) {
            return AnnotationCache::get($target);
        }

        list($realTarget) = explode('#', $target);
        $annotations = AnnotationStateParser::parseFrom($this->getDocComment(), $realTarget);
        if (empty($annotations)) {
            AnnotationCache::putEmpty($target);
            if ($realTarget !== $target) {
                AnnotationCache::putEmpty($realTarget);
            }

            return [];
        }

        $return = [];
        foreach ($annotations as $annotation) {
            AnnotationCache::put($annotation);
            if ($annotation->targetName() === $target) {
                $return[$annotation->originalType()] = $annotation;
            }
        }

        return $return;
    }

    /**
     * target name of property annotations
     *
     * @return  string
     */
    protected abstract function annotationTargetName();
}
