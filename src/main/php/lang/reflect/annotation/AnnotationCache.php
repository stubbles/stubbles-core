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
use stubbles\lang\exception\RuntimeException;
/**
 * Static cache for annotations
 *
 * @internal
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
     * closure which stores the current annotation cache
     *
     * @type  Closure
     */
    private static $storeCache;

    /**
     * start annotation cache with given cache storage logic
     *
     * Calling this method will also flush the cache. If this method is never
     * called the annotation cache will not be persistent but only last as long
     * as the current request is running.
     *
     * The $readCache closure must return the stored annotation data. If no such
     * data is present it must return null. In case the stored annotation data
     * can't be unserialized into an array a
     * stubbles\lang\exception\RuntimeException will be thrown.
     *
     * The $storeCache closure must store passed annotation data. It doesn't
     * need to take care about serialization, as it already receives a
     * serialized representation.
     *
     * A possible implementation for the file cache would look like this:
     * <code>
     * AnnotationCache::start(function() use($cacheFile)
     *                        {
     *                            if (file_exists($cacheFile)) {
     *                                return file_get_contents($cacheFile);
     *                            }
     *
     *                            return null;
     *                        },
     *                        function($annotationData) use($cacheFile)
     *                        {
     *                            file_put_contents($cacheFile, $annotationData);
     *                        }
     * );
     * </code>
     *
     * @param   \Closure  $readCache   function which can return cached annotation data
     * @param   \Closure  $storeCache  function which takes cached annotation data and stores it
     * @throws  RuntimeException
     * @since   3.0.0
     */
    public static function start(\Closure $readCache, \Closure $storeCache)
    {
        $annotationData = $readCache();
        if (null != $annotationData) {
            self::$annotations  = @unserialize($annotationData);
            if (!is_array(self::$annotations)) {
                self::flush();
                throw new RuntimeException('Cached annotation data is not an array');
            }
        } else {
            self::flush();
        }

        self::$cacheChanged = false;
        self::$storeCache   = $storeCache;
        register_shutdown_function(array(__CLASS__, '__shutdown'));
    }

    /**
     * starts annotation cache with given cache file
     *
     * @param  string  $cacheFile  path to file wherein cached annotation data is stored
     * @since  3.0.0
     */
    public static function startFromFileCache($cacheFile)
    {
        self::start(function() use($cacheFile)
                    {
                        if (file_exists($cacheFile)) {
                            return file_get_contents($cacheFile);
                        }

                        return null;
                    },
                    function($annotationData) use($cacheFile)
                    {
                        file_put_contents($cacheFile, $annotationData);
                    }
        );
    }

    /**
     * stops annotation cache persistence
     *
     * @since  3.0.0
     */
    public static function stop()
    {
        self::$storeCache = null;
    }

    /**
     * static shutdown
     */
    public static function __shutdown()
    {
        if (self::$cacheChanged && null !== self::$storeCache) {
            $storeCache = self::$storeCache;
            $storeCache(serialize(self::$annotations));
        }
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
        if (!isset(self::$annotations[$target])) {
            self::$annotations[$target] = array();
        }

        if (!isset(self::$annotations[$target][$targetName])) {
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
     * check, whether an annotation is available in the cache
     *
     * @param   int     $target          target of the annotation
     * @param   string  $targetName      name of the target
     * @param   string  $annotationName  name of the annotation
     * @return  bool
     */
    public static function has($target, $targetName, $annotationName)
    {
        if (!isset(self::$annotations[$target])) {
            return false;
        }

        if (!isset(self::$annotations[$target][$targetName])) {
            return false;
        }

        if (!isset(self::$annotations[$target][$targetName][$annotationName])) {
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
        if (!isset(self::$annotations[$target])) {
            return false;
        }

        if (!isset(self::$annotations[$target][$targetName])) {
            return false;
        }

        if (!isset(self::$annotations[$target][$targetName][$annotationName])) {
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
        if (self::has($target, $targetName, $annotationName)) {
            return unserialize(self::$annotations[$target][$targetName][$annotationName]);
        }

        return null;
    }
}
