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
use net\stubbles\Bootstrap;
/**
 * Static cache for annotations
 *
 * @static
 */
class AnnotationCache
{
    /**
     * Property to store annotations
     *
     * @type  array
     */
    private static $annotations  = array(Annotation::TARGET_CLASS    => array(),
                                         Annotation::TARGET_FUNCTION => array(),
                                         Annotation::TARGET_METHOD   => array(),
                                         Annotation::TARGET_PROPERTY => array()
                                   );
    /**
     * flag whether cache contents changed
     *
     * @type  bool
     */
    private static $cacheChanged = false;
    /**
     * file where annotation cache should be stored
     *
     * @type  string
     */
    private static $cacheFile;

    /**
     * sets cache file to be used
     *
     * @param  string  $cacheFile
     */
    public static function setCacheFile($cacheFile)
    {
        self::$cacheFile = $cacheFile;
    }

    /**
     * static initializer
     */
    public static function __static()
    {
        self::$cacheFile = Bootstrap::getRootPath() . '/cache/annotations.cache';
        if (file_exists(self::$cacheFile) == true) {
            self::$annotations = unserialize(file_get_contents(self::$cacheFile));
        }

        register_shutdown_function(array(__CLASS__, '__shutdown'));
    }

    /**
     * static shutdown
     */
    public static function __shutdown()
    {
        if (true === self::$cacheChanged) {
            file_put_contents(self::$cacheFile, serialize(self::$annotations));
        }
    }

    /**
     * refreshes cache data
     */
    public static function refresh()
    {
        file_put_contents(self::$cacheFile, serialize(self::$annotations));
        self::$annotations  = unserialize(file_get_contents(self::$cacheFile));
        self::$cacheChanged = false;
    }

    /**
     * flushes all contents from cache
     */
    public static function flush()
    {
        self::$annotations  = array(Annotation::TARGET_CLASS    => array(),
                                    Annotation::TARGET_FUNCTION => array(),
                                    Annotation::TARGET_METHOD   => array(),
                                    Annotation::TARGET_PROPERTY => array()
                              );
        self::$cacheChanged = true;
    }

    /**
     * store an annotation in the cache
     *
     * @param  int         $target          target of the annotation
     * @param  string      $targetName      name of the target
     * @param  string      $annotationName  name of the annotation
     * @param  Annotation  $annotation      the annotation to store
     */
    public static function put($target, $targetName, $annotationName, Annotation $annotation = null)
    {
        if (isset(self::$annotations[$target][$targetName]) === false) {
            self::$annotations[$target][$targetName] = array();
        }

        if (null !== $annotation) {
            self::$annotations[$target][$targetName][$annotationName] = serialize($annotation);
        } else {
            self::$annotations[$target][$targetName][$annotationName] = '';
        }

        self::$cacheChanged = true;
    }

    /**
     * removes an annotation from the cache
     *
     * @param  int     $target          target of the annotation
     * @param  string  $targetName      name of the target
     * @param  string  $annotationName  name of the annotation
     */
    public static function remove($target, $targetName, $annotationName)
    {
        if (isset(self::$annotations[$target][$targetName]) === false || isset(self::$annotations[$target][$targetName][$annotationName]) === false) {
            return;
        }

        unset(self::$annotations[$target][$targetName][$annotationName]);
        self::$cacheChanged = true;
    }

    /**
     * check, whether an annotation is available in the cache
     *
     * @param   int     $target          target of the annotation
     * @param   string  $targetName      name of the target
     * @param   string  $annotationName  name of the annotation
     * @return  bool
     */
    public static function has($target, $targetName, $annotationName)
    {
        if (isset(self::$annotations[$target][$targetName]) === false) {
            return false;
        }

        if (isset(self::$annotations[$target][$targetName][$annotationName]) === false) {
            return false;
        }

        return self::$annotations[$target][$targetName][$annotationName] !== '';
    }

    /**
     * check, whether an annotation is available in the cache
     *
     * @param   int     $target          target of the annotation
     * @param   string  $targetName      name of the target
     * @param   string  $annotationName  name of the annotation
     * @return  bool
     */
    public static function hasNot($target, $targetName, $annotationName)
    {
        if (isset(self::$annotations[$target][$targetName]) == false) {
            return false;
        }

        if (isset(self::$annotations[$target][$targetName][$annotationName]) == false) {
            return false;
        }

        return self::$annotations[$target][$targetName][$annotationName] === '';
    }

    /**
     * fetch an annotation from the cache
     *
     * @param   int         $target          target of the annotation
     * @param   string      $targetName      name of the target
     * @param   string      $annotationName  name of the annotation
     * @return  Annotation
     */
    public static function get($target, $targetName, $annotationName)
    {
        if (self::has($target, $targetName, $annotationName) === true) {
            return unserialize(self::$annotations[$target][$targetName][$annotationName]);
        }

        return null;
    }
}
AnnotationCache::__static();
?>