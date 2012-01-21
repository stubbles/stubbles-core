<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang\reflect\annotation;
use net\stubbles\lang\reflect\annotation\parser\AnnotationStateParser;
/**
 * Factory to create annotations.
 *
 * @static
 */
class AnnotationFactory
{
    /**
     * instance of the annotation parser
     *
     * @type  AnnotationStateParser
     */
    private static $parser      = null;
    /**
     * list of annotation data
     *
     * @type  array
     */
    private static $annotations = array();

    /**
     * Creates an annotation from the given docblock comment.
     *
     * @param   string      $comment          the docblock comment that contains the annotation data
     * @param   string      $annotationName   name of the annotation to create
     * @param   int         $target           the target for which the annotation should be created
     * @param   string      $targetName       the name of the target (property, class, method or function name)
     * @return  Annotation
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

        $hash = md5($comment . $targetName);
        if (!isset(self::$annotations[$hash])) {
            if (null === self::$parser) {
                self::$parser = new AnnotationStateParser();
            }

            self::$annotations[$hash] = self::$parser->parse($comment);
        }

        if (!isset(self::$annotations[$hash][$annotationName])) {
            // put null into cache to save that the annotation does not exist
            AnnotationCache::put($target, $targetName, $annotationName);
            throw new \ReflectionException('Can not find annotation ' . $annotationName);
        }

        $annotation = new Annotation(self::$annotations[$hash][$annotationName]['type']);
        foreach (self::$annotations[$hash][$annotationName]['params'] as $name => $value) {
            if ('__value' !== $name) {
                $annotation->$name = $value;
            } else {
                $annotation->setValue($value);
            }
        }

        AnnotationCache::put($target, $targetName, $annotationName, $annotation);
        return $annotation;
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
?>