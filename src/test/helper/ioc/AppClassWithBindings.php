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
use stubbles\ioc\Binder;
/**
 * Helper class for ioc tests.
 */
class AppClassWithBindings extends App
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
        return array(new AppTestBindingModuleOne(),
                     new AppTestBindingModuleTwo(),
                     function(Binder $binder)
                     {
                         $binder->bindConstant('boundBy')
                                ->to('closure');
                     }
               );
    }

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
     * sets value by how it was bound
     *
     * @param  string  $boundBy
     * @Inject(optional=true)
     * @Named('boundBy')
     */
    public function setBoundBy($boundBy)
    {
        $this->boundBy = $boundBy;
    }

    /**
     * returns value and how it was bound
     *
     * @return  string
     */
    public function wasBoundBy()
    {
        return $this->boundBy;
    }

    /**
     * runs the command
     */
    public function run() { }
}
