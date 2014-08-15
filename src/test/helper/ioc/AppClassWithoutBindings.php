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
class AppClassWithoutBindings extends App
{

    public $projectPath;

    /**
     *
     * @param  string  $projectPath
     * @Inject
     * @Named('stubbles.project.path')
     */
    public function setProjectPath($projectPath)
    {
        $this->projectPath = $projectPath;
    }

    /**
     * runs the command
     */
    public function run() { }
}
