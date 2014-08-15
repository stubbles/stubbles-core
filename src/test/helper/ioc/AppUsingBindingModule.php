<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\ioc;
use stubbles\ioc\App;
use stubbles\lang\Mode;
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
     * @param   string  $projectPath  path to project
     * @param   Mode    $mode         runtime mode
     * @return  ModeBindingModule
     */
    public static function getModeBindingModule($projectPath, Mode $mode = null)
    {
        return self::createModeBindingModule($projectPath, $mode);
    }

    /**
     * returns binding module for current working directory
     *
     * @return  \Closure
     * @since   3.4.0
     */
    public static function getBindCurrentWorkingDirectoryModule()
    {
        return self::bindCurrentWorkingDirectory();
    }

    /**
     * returns binding module for current hostname
     *
     * @return  \Closure
     * @since   3.4.0
     */
    public static function getBindHostnameModule()
    {
        return self::bindHostname();
    }

    /**
     * runs the command
     */
    public function run() { }
}
