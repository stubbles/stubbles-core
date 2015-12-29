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

use function bovigo\assert\assert;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
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
        assert($appCommandWithBindings, isInstanceOf(AppClassWithBindings::class));
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
        assert($appCommandWithBindings, isInstanceOf(AppClassWithBindings::class));
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceWithoutBindings()
    {
        assert(
                App::createInstance(
                        AppClassWithoutBindings::class,
                        'projectPath'
                ),
                isInstanceOf(AppClassWithoutBindings::class)
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function projectPathIsBoundWithExplicitBindings()
    {
        assert(
                AppClassWithBindings::create('projectPath')->projectPath,
                equals('projectPath')
        );
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function projectPathIsBoundWithoutExplicitBindings()
    {
        assert(
                AppClassWithoutBindings::create('projectPath')->projectPath,
                equals('projectPath')
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateRuntime()
    {
        assert(
                AppUsingBindingModule::callBindRuntime(),
                isInstanceOf(Runtime::class)
        );
    }

    /**
     * @since  2.1.0
     * @group  issue_33
     * @test
     */
    public function dynamicBindingViaClosure()
    {
        assert(
                AppClassWithBindings::create('projectPath')->wasBoundBy(),
                equals('closure')
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
     * @return  array
     */
    public function assertions()
    {
        return [
            [function(Injector $injector) { assertTrue($injector->hasBinding('foo')); }],
            [function(Injector $injector) { assertTrue($injector->hasBinding('bar')); }],
            [function(Injector $injector) { assertTrue($injector->hasBinding(Injector::class)); }],
            [function(Injector $injector) { assert($injector->getInstance(Injector::class), isSameAs($injector)); }]
        ];
    }

    /**
     * @test
     * @dataProvider  assertions
     */
    public function bindingModulesAreProcessed(callable $assertion)
    {
        $injector = App::createInstance(
                AppClassWithBindings::class,
                'projectPath'
        )->injector;
        $assertion($injector);
    }
}
