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
use bovigo\callmap\NewInstance;
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
                ->in(NewInstance::of('stubbles\ioc\binding\BindingScope'));
        assertTrue($binder->getInjector()->hasBinding('\stdClass'));
    }

    /**
     * @test
     */
    public function otherScopeIsUsedToCreateInstance()
    {
        $binder   = new Binder();
        $instance = new \stdClass();
        $binder->bind('\stdClass')
                ->to('\stdClass')
                ->in(NewInstance::of('stubbles\ioc\binding\BindingScope')
                        ->mapCalls(['getInstance' => $instance])
        );
        assertSame($instance, $binder->getInjector()->getInstance('\stdClass'));
    }
}
