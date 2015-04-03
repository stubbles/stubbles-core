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
use stubbles\test\ioc\AppClassWithoutBindings;
use stubbles\test\ioc\AppUsingBindingModule;
/**
 * Test for stubbles\ioc\App.
 *
 * @group  ioc
 */
class AppTest extends \PHPUnit_Framework_TestCase
{
    /**
     * clean up test environment
     */
    public function tearDown()
    {
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function createCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = AppClassWithBindings::create('projectPath');
        $this->assertInstanceOf(
                'stubbles\test\ioc\AppClassWithBindings',
                $appCommandWithBindings
        );
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = App::createInstance('stubbles\test\ioc\AppClassWithBindings',
                                                      'projectPath'
                                  );
        $this->assertInstanceOf(
                'stubbles\test\ioc\AppClassWithBindings',
                $appCommandWithBindings
        );
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceWithoutBindings()
    {
        $this->assertInstanceOf(
                'stubbles\test\ioc\AppClassWithoutBindings',
                App::createInstance('stubbles\test\ioc\AppClassWithoutBindings',
                                    'projectPath'
                )
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function projectPathIsBoundWithExplicitBindings()
    {
        $this->assertEquals(
                'projectPath',
                AppClassWithBindings::create('projectPath')->projectPath
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function projectPathIsBoundWithoutExplicitBindings()
    {
        $this->assertEquals(
                'projectPath',
                AppClassWithoutBindings::create('projectPath')->projectPath
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateRuntime()
    {
        $this->assertInstanceOf(
                'stubbles\ioc\module\Runtime',
                AppUsingBindingModule::callBindRuntime()
        );
    }

    /**
     * @since  2.1.0
     * @group  issue_33
     * @test
     */
    public function dynamicBindingViaClosure()
    {
        $this->assertEquals(
                'closure',
                AppClassWithBindings::create('projectPath')->wasBoundBy()
        );
    }

    /**
     * @test
     * @since  3.4.0
     */
    public function bindCurrentWorkingDirectory()
    {
        $binder = new Binder();
        $module = AppUsingBindingModule::currentWorkingDirectoryModule();
        $module($binder);
        $this->assertTrue($binder->getInjector()->hasConstant('stubbles.cwd'));
    }

    /**
     * @return  array
     */
    public function hostnameKeys()
    {
        return [
            ['stubbles.hostname.nq'],
            ['stubbles.hostname.fq']
        ];
    }

    /**
     * @test
     * @since  3.4.0
     * @dataProvider  hostnameKeys
     */
    public function bindHostname($key)
    {
        $binder = new Binder();
        $module = AppUsingBindingModule::bindHostnameModule();
        $module($binder);
        $this->assertTrue($binder->getInjector()->hasConstant($key));
    }
}
