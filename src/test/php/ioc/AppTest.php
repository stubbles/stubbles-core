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
use stubbles\ioc\module\Runtime;
use stubbles\test\ioc\AppClassWithBindings;
use stubbles\test\ioc\AppClassWithInvalidBindingModule;
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
        assertInstanceOf(AppClassWithBindings::class, $appCommandWithBindings);
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = App::createInstance(
                AppClassWithBindings::class,
                'projectPath'
        );
        assertInstanceOf(AppClassWithBindings::class, $appCommandWithBindings);
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceWithoutBindings()
    {
        assertInstanceOf(
                AppClassWithoutBindings::class,
                App::createInstance(
                        AppClassWithoutBindings::class,
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
        assertEquals(
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
        assertEquals(
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
        assertInstanceOf(
                Runtime::class,
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
        assertEquals(
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
        assertTrue($binder->getInjector()->hasConstant('stubbles.cwd'));
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
        assertTrue($binder->getInjector()->hasConstant($key));
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function invalidBindingModuleThrowsIllegalArgumentException()
    {
        App::createInstance(AppClassWithInvalidBindingModule::class, 'projectPath');
    }

    /**
     * @test
     */
    public function bindingModulesAreProcessed()
    {
        $injector = App::createInstance(
                AppClassWithBindings::class,
                'projectPath'
        )->injector;
        assertTrue($injector->hasBinding('foo'));
        assertTrue($injector->hasBinding('bar'));
        assertTrue($injector->hasBinding(Injector::class));
        assertSame($injector, $injector->getInstance(Injector::class));
    }
}
