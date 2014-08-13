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
 * Collection of all methods for something that can be annotated.
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
    public function annotation($type)
    {
        $annotations = $this->annotations();
        if (!$annotations->contain($type)) {
            throw new \ReflectionException('Can not find annotation ' . $type);
        }

        return $annotations->of($type)[0];
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
        return $this->annotation($type);
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
        $return = null;
        foreach (AnnotationStateParser::parseFrom($this->getDocComment(), $sourceTarget) as $annotations) {
            AnnotationCache::put($annotations);
            if ($annotations->target() === $target) {
                $return = $annotations;
            }
        }

        if (null === $return) {
            $return = new Annotations($target);
            AnnotationCache::put($return);
        }

        return $return;
    }

    /**
     * target name of property annotations
     *
     * @return  string
     */
    protected abstract function annotationTarget();
}
