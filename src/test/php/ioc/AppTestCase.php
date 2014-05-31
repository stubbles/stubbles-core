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
use stubbles\test\ioc\AppClassWithBindings;
use stubbles\test\ioc\AppUsingBindingModule;
/**
 * Test for stubbles\ioc\App.
 *
 * @group  ioc
 */
class AppTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @since  2.0.0
     * @test
     */
    public function createCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = AppClassWithBindings::create('projectPath');
        $this->assertInstanceOf('stubbles\test\ioc\AppClassWithBindings',
                                $appCommandWithBindings
        );
        $this->assertEquals('projectPath', AppClassWithBindings::getProjectPath());
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = App::createInstance('stubbles\test\ioc\AppClassWithBindings',
                                                      'projectPath'
                                  );
        $this->assertInstanceOf('stubbles\test\ioc\AppClassWithBindings',
                                $appCommandWithBindings
        );
        $this->assertEquals('projectPath', AppClassWithBindings::getProjectPath());
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceWithoutBindings()
    {
        $this->assertInstanceOf('stubbles\test\ioc\AppTestBindingModuleTwo',
                                App::createInstance('stubbles\test\ioc\AppTestBindingModuleTwo',
                                                    'projectPath'
                                )
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateModeBindingModule()
    {
        $this->assertInstanceOf('stubbles\ioc\module\ModeBindingModule',
                                AppUsingBindingModule::getModeBindingModule(__DIR__)
        );
    }

    /**
     * @since  2.1.0
     * @group  issue_33
     * @test
     */
    public function dynamicBindingViaClosure()
    {
        $this->assertEquals('closure',
                            AppClassWithBindings::create('projectPath')
                                                ->wasBoundBy()
        );
    }

    /**
     * @test
     * @since  3.4.0
     */
    public function bindCurrentWorkingDirectory()
    {
        $binder = new Binder();
        $module = AppUsingBindingModule::getBindCurrentWorkingDirectoryModule();
        $module($binder);
        $this->assertTrue($binder->hasConstant('stubbles.cwd'));
    }

    /**
     * @test
     * @since  3.4.0
     */
    public function bindHostname()
    {
        $binder = new Binder();
        $module = AppUsingBindingModule::getBindHostnameModule();
        $module($binder);
        $this->assertTrue($binder->hasConstant('stubbles.hostname.nq'));
        $this->assertTrue($binder->hasConstant('stubbles.hostname.fq'));
    }
}
