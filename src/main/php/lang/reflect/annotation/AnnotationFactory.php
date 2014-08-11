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
     * Creates an annotation from the given docblock comment.
     *
     * @param   string      $comment          the docblock comment that contains the annotation data
     * @param   string      $annotationName   name of the annotation to create
     * @param   int         $target           the target for which the annotation should be created
     * @param   string      $targetName       the name of the target (property, class, method or function name)
     * @return  \stubbles\lang\reflect\annotation\Annotation
     * @throws  \ReflectionException
     */
    public static function create($comment, $annotationName, $target, $targetName)
    {
        if (AnnotationCache::has($target, $targetName, $annotationName)) {
            return AnnotationCache::get($target, $targetName, $annotationName);
        }

        if (AnnotationCache::hasNot($target, $targetName, $annotationName)) {
            throw new \ReflectionException('Can not find annotation ' . $annotationName);
        }

        $annotations = self::parse($comment, $targetName);
        foreach ($annotations as $name => $annotation) {
            AnnotationCache::put($target, $targetName, $name, $annotation);
        }

        if (!isset($annotations[$annotationName])) {
            // put null into cache to save that the annotation does not exist
            AnnotationCache::put($target, $targetName, $annotationName);
            throw new \ReflectionException('Can not find annotation ' . $annotationName);
        }

        return $annotations[$annotationName];
    }

    /**
     * parses doc comments and returns data about all annotations found
     *
     * @staticvar  \stubbles\lang\reflect\annotation\parser\AnnotationStateParser  $parser
     * @param      string  $comment
     * @param      string  $targetName
     * @return     array
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
     * @param   int     $target          the target for which the annotation should be created
     * @param   string  $targetName      the name of the target (property, class, method or function name)
     * @return  bool
     */
    public static function has($comment, $annotationName, $target, $targetName)
    {
        try {
            $annotation = self::create($comment, $annotationName, $target, $targetName);
        } catch (\ReflectionException $e) {
            $annotation = null;
        }

        return (null != $annotation);
    }
}
