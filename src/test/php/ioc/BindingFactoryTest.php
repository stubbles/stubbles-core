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
use stubbles\test\ioc\AppTestBindingModuleOne;
use stubbles\test\ioc\AppTestBindingModuleTwo;
/**
 * Test for stubbles\ioc\App.
 *
 * @since  2.0.0
 * @group  ioc
 */
class BindingFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function invalidBindingModuleClassThrowsIllegalArgumentException()
    {
        BindingFactory::createInjector('\stdClass');
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     */
    public function invalidBindingModuleInstanceThrowsIllegalArgumentException()
    {
        BindingFactory::createInjector(new \stdClass());
    }

    /**
     * @test
     */
    public function bindingModulesAreProcessed()
    {
        $injector = BindingFactory::createInjector(
                new AppTestBindingModuleOne(),
                AppTestBindingModuleTwo::class
        );
        assertTrue($injector->hasBinding('foo'));
        assertTrue($injector->hasBinding('bar'));
        assertTrue($injector->hasBinding(Injector::class));
        assertSame($injector, $injector->getInstance(Injector::class));
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function bindingModulesAreProcessedIfPassedAsArray()
    {
        $injector = BindingFactory::createInjector([
                new AppTestBindingModuleOne(),
                AppTestBindingModuleTwo::class
        ]);
        assertTrue($injector->hasBinding('foo'));
        assertTrue($injector->hasBinding('bar'));
        assertTrue($injector->hasBinding(Injector::class));
        assertSame($injector, $injector->getInstance(Injector::class));
    }
}
