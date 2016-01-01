<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc;
use stubbles\App;
/**
 * Application base class.
 *
 * @deprecated  since 7.0.0, use stubbles\App instead, will be removed with 8.0.0
 */
abstract class App extends App
{
    /**
     * creates an object via injection
     *
     * If the class to create an instance of contains a static __bindings() method
     * this method will be used to configure the ioc bindings before using the ioc
     * container to create the instance.
     *
     * @api
     * @param   string  $projectPath  path to project
     * @return  \stubbles\App
     */
    public static function create($projectPath)
    {
        trigger_error(
                'Using ' . __CLASS__ . ' is deprecated since 7.0.0,'
                . ' use stubbles\App instead, will be removed with 8.0.0',
                E_USER_DEPRECATED
        );
        return parent::create($projectPath);
    }
}
