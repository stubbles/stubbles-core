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
/**
 * Test for stubbles\ioc\Injector with the singleton scope.
 *
 * @group  ioc
 */
class InjectorSingletonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * helper assertion
     *
     * @param  SlotMachine  $slot
     * @param  string       $numberClass
     */
    protected function assertSlotMachineIsBuildCorrect(SlotMachine $slot, $numberClass)
    {
        assertInstanceOf(SlotMachine::class, $slot);
        assertInstanceOf(Number::class, $slot->number1);
        assertInstanceOf($numberClass, $slot->number1);
        assertInstanceOf(Number::class, $slot->number2);
        assertInstanceOf($numberClass, $slot->number2);
        $this->identicalTo($slot->number1, $slot->number2);
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
               ->toClosure(function() { return new \stubbles\test\ioc\Random(); })
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
