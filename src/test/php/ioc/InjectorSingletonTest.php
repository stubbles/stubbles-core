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
        $this->assertInstanceOf('stubbles\test\ioc\SlotMachine', $slot);
        $this->assertInstanceOf('stubbles\test\ioc\Number', $slot->number1);
        $this->assertInstanceOf($numberClass, $slot->number1);
        $this->assertInstanceOf('stubbles\test\ioc\Number', $slot->number2);
        $this->assertInstanceOf($numberClass, $slot->number2);
        $this->identicalTo($slot->number1, $slot->number2);
    }

    /**
     * @test
     */
    public function assigningSingletonScopeToBindingWillReuseInitialInstance()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Number')
               ->to('stubbles\test\ioc\Random')
               ->asSingleton();
        $this->assertSlotMachineIsBuildCorrect($binder->getInjector()
                                                      ->getInstance('stubbles\test\ioc\SlotMachine'),
                                               'stubbles\test\ioc\Random'
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
        $binder->bind('stubbles\test\ioc\Number')
               ->toClosure(function() { return new \stubbles\test\ioc\Random(); })
               ->asSingleton();
        $this->assertSlotMachineIsBuildCorrect($binder->getInjector()
                                                      ->getInstance('stubbles\test\ioc\SlotMachine'),
                                               'stubbles\test\ioc\Random'
        );
    }

    /**
     * @test
     */
    public function classAnnotatedWithSingletonWillOnlyBeCreatedOnce()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Number')
               ->to('stubbles\test\ioc\RandomSingleton');
        $this->assertSlotMachineIsBuildCorrect($binder->getInjector()
                                                      ->getInstance('stubbles\test\ioc\SlotMachine'),
                                               'stubbles\test\ioc\RandomSingleton'
        );
    }
}
