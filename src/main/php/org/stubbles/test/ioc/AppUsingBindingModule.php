<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace org\stubbles\test\ioc;
use net\stubbles\ioc\App;
use net\stubbles\lang\Mode;
/**
 * Helper class to test binding module creations.
 *
 * @since  2.0.0
 */
class AppUsingBindingModule extends App
{

    /**
     * creates mode binding module
     *
     * @param   Mode  $mode  runtime mode
     * @return  ModeBindingModule
     */
    public static function getModeBindingModule(Mode $mode = null)
    {
        return self::createModeBindingModule($mode);
    }

    /**
     * creates properties binding module
     *
     * @param   string  $projectPath
     * @return  PropertiesBindingModule
     */
    public static function getPropertiesBindingModule($projectPath)
    {
        return self::createPropertiesBindingModule($projectPath);
    }

    /**
     * enables annotation persistence
     *
     * @param  Closure  $readCache
     * @param  Closure  $storeCache
     * @since  3.0.0
     */
    public static function callAnnotationPersistence(\Closure $readCache, \Closure $storeCache)
    {
        self::persistAnnotations($readCache, $storeCache);
    }

    /**
     * enabled annotation file persistence
     *
     * @param  string  $cacheFile
     * @since  3.0.0
     */
    public static function callAnnotationFilePersistence($cacheFile)
    {
        self::persistAnnotationsInFile($cacheFile);
    }

    /**
     * runs the command
     */
    public function run() { }
}
?>