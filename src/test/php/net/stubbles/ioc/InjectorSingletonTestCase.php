<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc;
use org\stubbles\test\ioc\SlotMachine;
/**
 * Test for net\stubbles\ioc\Injector with the singleton scope.
 *
 * @group  ioc
 */
class InjectorSingletonTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * helper assertion
     *
     * @param  SlotMachine  $slot
     * @param  string       $numberClass
     */
    protected function assertSlotMachineIsBuildCorrect(SlotMachine $slot, $numberClass)
    {
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\SlotMachine', $slot);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Number', $slot->number1);
        $this->assertInstanceOf($numberClass, $slot->number1);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Number', $slot->number2);
        $this->assertInstanceOf($numberClass, $slot->number2);
        $this->identicalTo($slot->number1, $slot->number2);
    }

    /**
     * @test
     */
    public function assigningSingletonScopeToBindingWillReuseInitialInstance()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Number')
               ->to('org\\stubbles\\test\\ioc\\Random')
               ->asSingleton();
        $this->assertSlotMachineIsBuildCorrect($binder->getInjector()
                                                      ->getInstance('org\\stubbles\\test\\ioc\\SlotMachine'),
                                               'org\\stubbles\\test\\ioc\\Random'
        );
    }

    /**
     * @test
     */
    public function classAnnotatedWithSingletonWillOnlyBeCreatedOnce()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Number')
               ->to('org\\stubbles\\test\\ioc\\RandomSingleton');
        $this->assertSlotMachineIsBuildCorrect($binder->getInjector()
                                                      ->getInstance('org\\stubbles\\test\\ioc\\SlotMachine'),
                                               'org\\stubbles\\test\\ioc\\RandomSingleton'
        );
    }
}
?>