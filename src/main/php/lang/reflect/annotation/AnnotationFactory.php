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
 * Factory to create annotations.
 *
 * @internal
 * @static
 */
class AnnotationFactory
{
    /**
     * creates an annotation from the given docblock comment
     *
     * @param   string  $comment         the docblock comment that contains the annotation data
     * @param   string  $annotationName  name of the annotation to create
     * @param   string  $targetName      the name of the target, must be unique (property, class, method or function name)
     * @return  \stubbles\lang\reflect\annotation\Annotation
     * @throws  \ReflectionException
     */
    public static function create($comment, $annotationName, $targetName)
    {
        if (AnnotationCache::has($targetName, $annotationName)) {
            return AnnotationCache::get($targetName, $annotationName);
        }

        if (AnnotationCache::hasNot($targetName, $annotationName)) {
            throw new \ReflectionException('Can not find annotation ' . $annotationName);
        }

        $annotations = self::createAll($comment, $targetName);
        if (!isset($annotations[$annotationName])) {
            // put null into cache to save that the annotation does not exist
            AnnotationCache::put($targetName, $annotationName);
            throw new \ReflectionException('Can not find annotation ' . $annotationName);
        }

        return $annotations[$annotationName];
    }

    /**
     * creates all annotations from the given docblock comment
     *
     * @param   string  $comment
     * @param   string  $targetName
     * @return  \stubbles\lang\reflect\annotation\Annotation[]
     * @since   5.0.0
     */
    public static function createAll($comment, $targetName)
    {
        $annotations = self::parse($comment, $targetName);
        foreach ($annotations as $name => $annotation) {
            AnnotationCache::put($targetName, $name, $annotation);
        }

        return $annotations;
    }

    /**
     * parses doc comments and returns data about all annotations found
     *
     * @staticvar  \stubbles\lang\reflect\annotation\parser\AnnotationStateParser  $parser
     * @param      string  $comment
     * @param      string  $targetName
     * @return     \stubbles\lang\reflect\annotation\Annotation[]
     */
    private static function parse($comment, $targetName)
    {
        static $parser = null;
        if (null === $parser) {
            $parser = new AnnotationStateParser();
        }

        return $parser->parse($comment, $targetName);
    }

    /**
     * Checks whether the given docblock has the requested annotation
     *
     * @param   string  $comment         the docblock comment that contains the annotation data
     * @param   string  $annotationName  name of the annotation to check for
     * @param   string  $targetName      the name of the target, must be unique (property, class, method or function name)
     * @return  bool
     */
    public static function has($comment, $annotationName, $targetName)
    {
        try {
            $annotation = self::create($comment, $annotationName, $targetName);
        } catch (\ReflectionException $e) {
            $annotation = null;
        }

        return (null != $annotation);
    }
}
