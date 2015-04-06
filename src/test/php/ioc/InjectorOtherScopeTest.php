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
/**
 * Test for stubbles\ioc\Injector with the session scope.
 *
 * @group  ioc
 */
class InjectorOtherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function hasBindingWhenBoundToOtherScope()
    {
        $binder = new Binder();
        $binder->bind('\stdClass')
                ->to('\stdClass')
                ->in($this->getMock('stubbles\ioc\binding\BindingScope'));
        assertTrue($binder->getInjector()->hasBinding('\stdClass'));
    }

    /**
     * @test
     */
    public function otherScopeIsUsedToCreateInstance()
    {
        $binder = new Binder();
        $bindingScope = $this->getMock('stubbles\ioc\binding\BindingScope');
        $binder->bind('\stdClass')
                ->to('\stdClass')
                ->in($bindingScope);
        $injector = $binder->getInjector();

        $instance = new \stdClass();
        $bindingScope->method('getInstance')->will(returnValue($instance));
        assertSame($instance, $injector->getInstance('\stdClass'));
    }
}
