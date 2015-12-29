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
use stubbles\ioc\binding\BindingScope;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\assert\predicate\isTrue;
/**
 * Test for stubbles\ioc\Injector with user-defined scope.
 *
 * @group  ioc
 */
class InjectorUserDefinedScopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function hasBindingWhenBoundToOtherScope()
    {
        $binder = new Binder();
        $binder->bind(\stdClass::class)
                ->to(\stdClass::class)
                ->in(NewInstance::of(BindingScope::class));
        assert($binder->getInjector()->hasBinding(\stdClass::class), isTrue());
    }

    /**
     * @test
     */
    public function otherScopeIsUsedToCreateInstance()
    {
        $binder   = new Binder();
        $instance = new \stdClass();
        $binder->bind(\stdClass::class)
                ->to(\stdClass::class)
                ->in(NewInstance::of(BindingScope::class)
                        ->mapCalls(['getInstance' => $instance])
        );
        assert(
                $binder->getInjector()->getInstance(\stdClass::class),
                isSameAs($instance)
        );
    }
}
