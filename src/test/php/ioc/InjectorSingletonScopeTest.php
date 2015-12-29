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
use stubbles\test\ioc\Number;
use stubbles\test\ioc\Random;
use stubbles\test\ioc\RandomSingleton;
use stubbles\test\ioc\SlotMachine;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
/**
 * Test for stubbles\ioc\Injector with the singleton scope.
 *
 * @group  ioc
 */
class InjectorSingletonScopeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * helper assertion
     *
     * @param  SlotMachine  $slot
     * @param  string       $numberClass
     */
    protected function assertSlotMachineIsBuildCorrect(SlotMachine $slot, $numberClass)
    {
        assert($slot->number1, isInstanceOf($numberClass));
        assert($slot->number2, isInstanceOf($numberClass));
        assert($slot->number1, isSameAs($slot->number2));
    }

    /**
     * @test
     */
    public function assigningSingletonScopeToBindingWillReuseInitialInstance()
    {
        $binder = new Binder();
        $binder->bind(Number::class)
               ->to(Random::class)
               ->asSingleton();
        $this->assertSlotMachineIsBuildCorrect(
                $binder->getInjector()->getInstance(SlotMachine::class),
                Random::class
        );
    }

    /**
     * @since  2.1.0
     * @test
     * @group  issue_31
     */
    public function assigningSingletonScopeToClosureBindingWillReuseInitialInstance()
    {
        $binder = new Binder();
        $binder->bind(Number::class)
               ->toClosure(function() { return new Random(); })
               ->asSingleton();
        $this->assertSlotMachineIsBuildCorrect(
                $binder->getInjector()->getInstance(SlotMachine::class),
                Random::class
        );
    }

    /**
     * @test
     */
    public function classAnnotatedWithSingletonWillOnlyBeCreatedOnce()
    {
        $binder = new Binder();
        $binder->bind(Number::class)
               ->to(RandomSingleton::class);
        $this->assertSlotMachineIsBuildCorrect(
                $binder->getInjector()->getInstance(SlotMachine::class),
                RandomSingleton::class
        );
    }
}
