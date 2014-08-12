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
     * checks whether at least on occurrence of this annotation is present
     *
     * @param   string  $type
     * @return  bool
     */
    public function hasAnnotation($type)
    {
        return $this->annotations()->contain($type);
    }

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
    public function getAnnotation($type)
    {
        $annotations = $this->annotations();
        if (!$annotations->contain($type)) {
            throw new \ReflectionException('Can not find annotation ' . $type);
        }

        return $annotations->of($type)[0];
    }

    /**
     * returns all annotations for this element
     *
     * @return  \stubbles\lang\reflect\annotation\Annotations
     * @since   5.0.0
     */
    public function annotations()
    {
        $target = $this->annotationTarget();
        if (AnnotationCache::has($target)) {
            return AnnotationCache::get($target);
        }

        list($sourceTarget) = explode('#', $target);
        foreach (AnnotationStateParser::parseFrom($this->getDocComment(), $sourceTarget) as $annotations) {
            AnnotationCache::put($annotations);
        }

        return AnnotationCache::get($target);
    }

    /**
     * target name of property annotations
     *
     * @return  string
     */
    protected abstract function annotationTarget();
}
