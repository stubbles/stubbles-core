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
use net\stubbles\lang\BaseObject;
/**
 * Helper class for ioc tests.
 */
class AppClassWithBindings extends BaseObject
{
    /**
     * given project path
     *
     * @type  string
     */
    protected static $projectPath;

    /**
     * return list of bindings required for this command
     *
     * @param   string  $projectPath
     * @return  array
     */
    public static function __bindings($projectPath)
    {
        self::$projectPath = $projectPath;
        return array(new AppTestBindingModuleOne(),
                     new AppTestBindingModuleTwo()
               );
    }

    /**
     * returns set project path
     *
     * @return  string
     */
    public static function getProjectPath()
    {
        return self::$projectPath;
    }

    /**
     * runs the command
     */
    public function run() { }
}
?>