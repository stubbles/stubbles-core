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
/**
 * Helper class for ioc tests.
 */
class AppClassWithAnnotationCache extends App
{

    /**
     * return list of bindings required for this command
     *
     * @param   string  $projectPath
     * @return  array
     */
    public static function __bindings($projectPath)
    {
        self::useFileAnnotationCache($projectPath . '/annotations.cache');
        return array();
    }

    /**
     * runs the command
     */
    public function run() { }
}
?>