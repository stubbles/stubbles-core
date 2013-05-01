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
use org\stubbles\test\ioc\AppClassWithAnnotationCache;
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
     * @since  2.0.0
     * @test
     */
    public function createCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = AppClassWithBindings::create('projectPath');
        $this->assertInstanceOf('org\stubbles\test\ioc\AppClassWithBindings',
                                $appCommandWithBindings
        );
        $this->assertEquals('projectPath', AppClassWithBindings::getProjectPath());
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = App::createInstance('org\stubbles\test\ioc\AppClassWithBindings',
                                                      'projectPath'
                                  );
        $this->assertInstanceOf('org\stubbles\test\ioc\AppClassWithBindings',
                                $appCommandWithBindings
        );
        $this->assertEquals('projectPath', AppClassWithBindings::getProjectPath());
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceWithoutBindings()
    {
        $this->assertInstanceOf('org\stubbles\test\ioc\AppTestBindingModuleTwo',
                                App::createInstance('org\stubbles\test\ioc\AppTestBindingModuleTwo',
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
        $this->assertInstanceOf('net\stubbles\ioc\module\ModeBindingModule',
                                AppUsingBindingModule::getModeBindingModule()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreatePropertiesBindingModule()
    {
        $this->assertInstanceOf('net\stubbles\ioc\module\PropertiesBindingModule',
                                AppUsingBindingModule::getPropertiesBindingModule(__DIR__)
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
     * @since  2.2.0
     * @group  issue_58
     * @test
     */
    public function canCreateAppInstanceWithFileAnnotationCache()
    {
        $root = \org\bovigo\vfs\vfsStream::setup();
        $appClass = AppClassWithAnnotationCache::create($root->url());
        $this->assertInstanceOf('org\stubbles\test\ioc\AppClassWithAnnotationCache',
                                $appClass
        );
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        \net\stubbles\lang\reflect\annotation\AnnotationCache::stop();
    }
}
?>