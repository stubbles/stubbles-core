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
use net\stubbles\lang\BaseObject;
use org\stubbles\test\ioc\AppClassWithBindings;
use org\stubbles\test\ioc\AppUsingBindingModule;
/**
 * Test for net\stubbles\ioc\App.
 *
 * @group  ioc
 */
class AppTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createInstanceCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = App::createInstance('org\\stubbles\\test\\ioc\\AppClassWithBindings',
                                                      'projectPath'
                                  );
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\AppClassWithBindings',
                                $appCommandWithBindings
        );
        $this->assertEquals('projectPath', AppClassWithBindings::getProjectPath());
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceWithoutBindings()
    {
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\AppTestBindingModuleTwo',
                                App::createInstance('org\\stubbles\\test\\ioc\\AppTestBindingModuleTwo',
                                                    'projectPath'
                                )
        );
    }

    /**
     * @test
     */
    public function createInstanceWithArguments()
    {
        $appClassWithArgument = App::createInstance('org\\stubbles\\test\\ioc\\AppClassWithArgument',
                                                    'projectPath',
                                                    array('foo')
                                );
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\AppClassWithArgument',
                                $appClassWithArgument
        );
        $this->assertEquals('foo', $appClassWithArgument->getArgument());
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateModeBindingModule()
    {
        $this->assertInstanceOf('net\\stubbles\\ioc\\module\\ModeBindingModule',
                                AppUsingBindingModule::getModeBindingModule()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreatePropertiesBindingModule()
    {
        $this->assertInstanceOf('net\\stubbles\\ioc\\module\\PropertiesBindingModule',
                                AppUsingBindingModule::getPropertiesBindingModule(__DIR__)
        );
    }
}
?>