<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc;
use net\stubbles\ioc\module\ModeBindingModule;
use net\stubbles\ioc\module\PropertiesBindingModule;
use net\stubbles\lang\Mode;
/**
 * Application base class.
 */
abstract class App
{
    /**
     * runs the application
     *
     * @api
     * @since  2.0.0
     */
    public abstract function run();

    /**
     * creates an object via injection
     *
     * If the class to create an instance of contains a static __bindings() method
     * this method will be used to configure the ioc bindings before using the ioc
     * container to create the instance.
     *
     * @api
     * @param   string  $projectPath  path to project
     * @return  App
     */
    public static function create($projectPath)
    {
        return self::createInstance(get_called_class(), $projectPath);
    }

    /**
     * creates an object via injection
     *
     * If the class to create an instance of contains a static __bindings() method
     * this method will be used to configure the ioc bindings before using the ioc
     * container to create the instance.
     *
     * @api
     * @param   string  $className    full qualified class name of class to create an instance of
     * @param   string  $projectPath  path to project
     * @return  App
     */
    public static function createInstance($className, $projectPath)
    {
        self::enforceInternalEncoding();
        return BindingFactory::createInjector(self::getBindingsForApp($className, $projectPath))
                             ->getInstance($className);
    }

    /**
     * creates list of bindings from given class
     *
     * @param   string  $className    full qualified class name of class to create an instance of
     * @param   string  $projectPath  path to project
     * @return  BindingModule[]
     * @since   1.3.0
     */
    private static function getBindingsForApp($className, $projectPath)
    {
        if (method_exists($className, '__bindings')) {
            return $className::__bindings($projectPath);
        }

        return array();
    }

    /**
     * enable persistent annotation cache with given cache storage logic
     *
     * The $readCache closure must return the stored annotation data. If no such
     * data is present it must return null. In case the stored annotation data
     * can't be unserialized into an array a
     * net\stubbles\lang\exception\RuntimeException will be thrown.
     *
     * The $storeCache closure must store passed annotation data. It doesn't
     * need to take care about serialization, as it already receives a
     * serialized representation.
     *
     * A possible implementation for the file cache would look like this:
     * <code>
     * self::persistAnnotations(function() use($cacheFile)
     *                          {
     *                              if (file_exists($cacheFile)) {
     *                                  return file_get_contents($cacheFile);
     *                              }
     *
     *                              return null;
     *                          },
     *                          function($annotationData) use($cacheFile)
     *                          {
     *                              file_put_contents($cacheFile, $annotationData);
     *                          }
     * );
     * </code>
     *
     * @param  \Closure  $readCache
     * @param  \Closure  $storeCache
     * @since  3.0.0
     */
    protected static function persistAnnotations(\Closure $readCache, \Closure $storeCache)
    {
        \net\stubbles\lang\reflect\annotation\AnnotationCache::start($readCache, $storeCache);
    }

    /**
     * enable persistent annotation cache by telling where to store cache data
     *
     * @param  string  $cacheFile
     * @since  3.0.0
     */
    protected static function persistAnnotationsInFile($cacheFile)
    {
        \net\stubbles\lang\reflect\annotation\AnnotationCache::startFromFileCache($cacheFile);
    }

    /**
     * creates mode binding module
     *
     * @api
     * @param   Mode  $mode  runtime mode
     * @return  ModeBindingModule
     * @since   2.0.0
     */
    protected static function createModeBindingModule(Mode $mode = null)
    {
        return new ModeBindingModule($mode);
    }

    /**
     * creates properties binding module
     *
     * @api
     * @param   string  $projectPath
     * @return  PropertiesBindingModule
     * @since   2.0.0
     */
    protected static function createPropertiesBindingModule($projectPath)
    {
        return new PropertiesBindingModule($projectPath);
    }

    /**
     * switch whether internal encoding already set
     *
     * @type  bool
     */
    private static $encodingEnforced = false;

    /**
     * enforces internal encoding to be UTF-8
     */
    private static function enforceInternalEncoding()
    {
        if (self::$encodingEnforced) {
            return;
        }

        iconv_set_encoding('internal_encoding', 'UTF-8');
        if (($ctype = getenv('LC_CTYPE')) || ($ctype = setlocale(LC_CTYPE, 0))) {
            $language = $charset = null;
            sscanf($ctype, '%[^.].%s', $language, $charset);
            if (is_numeric($charset)) {
                $charset = 'CP' . $charset;
            } elseif (null == $charset) {
                $charset = 'iso-8859-1';
            }

            iconv_set_encoding('output_encoding', $charset);
            iconv_set_encoding('input_encoding', $charset);
        }

        self::$encodingEnforced = true;
    }
}
?>