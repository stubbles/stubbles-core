<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test;
use stubbles\App;
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
     * @param   \stubbles\lang\Mode  $mode  runtime mode
     * @return  \stubbles\ioc\module\Runtime
     */
    public static function callBindRuntime(Mode $mode = null)
    {
        return self::runtime($mode);
    }

    /**
     * returns binding module for current working directory
     *
     * @return  \Closure
     * @since   3.4.0
     */
    public static function currentWorkingDirectoryModule()
    {
        return self::currentWorkingDirectory();
    }

    /**
     * returns binding module for current hostname
     *
     * @return  \Closure
     * @since   3.4.0
     */
    public static function bindHostnameModule()
    {
        return self::hostname();
    }

    /**
     * runs the command
     */
    public function run() { }
}
