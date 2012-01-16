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
/**
 * Helper interface for the test.
 */
interface Number
{
    /**
     * display a number
     */
    public function display();
}
/**
 * Helper class for the test.
 */
class Random implements Number
{
    /**
     * value of the number
     *
     * @type  int
     */
    private $value;

    /**
     * constructor
     */
    public function __construct()
    {
        srand();
        $this->value = rand(0, 5000);
    }

    /**
     * display a number
     */
    public function display() {
        echo $this->value . "\n";
    }
}

/**
 * Class that is marked as Singleton
 *
 * @Singleton
 */
class RandomSingleton implements Number
{
    /**
     * value of the number
     *
     * @type  int
     */
    private $value;

    /**
     * constructor
     */
    public function __construct()
    {
        srand();
        $this->value = rand(0, 5000);
    }

    /**
     * display a number
     */
    public function display() {
        echo $this->value . "\n";
    }
}
/**
 * One more helper class for the test.
 */
class SlotMachine
{
    /**
     * selected number 1
     *
     * @type  Number
     */
    public $number1;
    /**
     * selected number 2
     *
     * @type  Number
     */
    public $number2;

    /**
     * Set number 1
     *
     * @param  Number  $number
     * @Inject
     */
    public function setNumber1(Number $number)
    {
        $this->number1 = $number;
    }

    /**
     * Set number 2
     *
     * @param  Number  $number
     * @Inject
     */
    public function setNumber2(Number $number)
    {
        $this->number2 = $number;
    }
}


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
        $this->assertInstanceOf('net\\stubbles\\ioc\\SlotMachine', $slot);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Number', $slot->number1);
        $this->assertInstanceOf($numberClass, $slot->number1);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Number', $slot->number2);
        $this->assertInstanceOf($numberClass, $slot->number2);
        $this->identicalTo($slot->number1, $slot->number2);
    }

    /**
     * @test
     */
    public function assigningSingletonScopeToBindingWillReuseInitialInstance()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Number')
               ->to('net\\stubbles\\ioc\\Random')
               ->asSingleton();
        $this->assertSlotMachineIsBuildCorrect($binder->getInjector()
                                                      ->getInstance('net\\stubbles\\ioc\\SlotMachine'),
                                               'net\\stubbles\\ioc\\Random'
        );
    }

    /**
     * @test
     */
    public function classAnnotatedWithSingletonWillOnlyBeCreatedOnce()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Number')
               ->to('net\\stubbles\\ioc\\RandomSingleton');
        $this->assertSlotMachineIsBuildCorrect($binder->getInjector()
                                                      ->getInstance('net\\stubbles\\ioc\\SlotMachine'),
                                               'net\\stubbles\\ioc\\RandomSingleton'
        );
    }
}
?>