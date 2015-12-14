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
/**
 * Helper class for ioc tests.
 */
class AppClassWithInvalidBindingModule extends App
{
    /**
     * bound by value for retrieval
     *
     * @type  string
     */
    private $boundBy;

    public $projectPath;

    /**
     * return list of bindings required for this command
     *
     * @return  array
     */
    public static function __bindings()
    {
        return ['\stdClass'];
    }
    /**
     * runs the command
     */
    public function run() { }
}
