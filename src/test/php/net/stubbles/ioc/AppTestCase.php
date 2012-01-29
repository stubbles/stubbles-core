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
use net\stubbles\ioc\module\BindingModule;
use net\stubbles\lang\BaseObject;
use org\stubbles\test\ioc\AppClassWithBindings;
use org\stubbles\test\ioc\AppTestBindingModuleOne;
/**
 * Test for net\stubbles\ioc\App.
 *
 * @group  ioc
 */
class AppTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidBindingModuleClassThrowsIllegalArgumentException()
    {
        App::createInjector('\\stdClass');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidBindingModuleInstanceThrowsIllegalArgumentException()
    {
        App::createInjector(new \stdClass());
    }

    /**
     * @test
     */
    public function bindingModulesAreProcessed()
    {
        $injector = App::createInjector(new AppTestBindingModuleOne(),
                                        'org\\stubbles\\test\\ioc\\AppTestBindingModuleTwo'
                    );
        $this->assertTrue($injector->hasBinding('foo'));
        $this->assertTrue($injector->hasBinding('bar'));
        $this->assertTrue($injector->hasBinding('net\stubbles\ioc\Injector'));
        $this->assertSame($injector, $injector->getInstance('net\stubbles\ioc\Injector'));
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function bindingModulesAreProcessedIfPassedAsArray()
    {
        $injector = App::createInjector(array(new AppTestBindingModuleOne(),
                                              'org\\stubbles\\test\\ioc\\AppTestBindingModuleTwo'
                                        )
                    );
        $this->assertTrue($injector->hasBinding('foo'));
        $this->assertTrue($injector->hasBinding('bar'));
        $this->assertTrue($injector->hasBinding('net\stubbles\ioc\Injector'));
        $this->assertSame($injector, $injector->getInstance('net\stubbles\ioc\Injector'));
    }

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
}
?>