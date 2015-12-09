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
use stubbles\test\ioc\AnswerConstantProvider;
use stubbles\test\ioc\Question;
/**
 * Helper class for the test.
/**
 * Test for stubbles\ioc\Injector with constant binding.
 *
 * @group  ioc
 */
class InjectorConstantTest extends \PHPUnit_Framework_TestCase
{
    /**
     * combined helper assertion for the test
     *
     * @param  Injector  $injector
     */
    private function assertConstantInjection(Injector $injector)
    {
        $question = $injector->getInstance(Question::class);
        assertInstanceOf(Question::class, $question);
        assertEquals(42, $question->getAnswer());
    }

    /**
     * @test
     */
    public function injectConstant()
    {
        $binder = new Binder();
        $binder->bindConstant('answer')->to(42);
        $this->assertConstantInjection($binder->getInjector());
    }

    /**
     * @test
     */
    public function checkForNonExistingConstantReturnsFalse()
    {
        $binder = new Binder();
        assertFalse($binder->getInjector()->hasConstant('answer'));
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function retrieveNonExistingConstantThrowsBindingException()
    {
        $binder = new Binder();
        $binder->getInjector()->getConstant('answer');
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function checkForExistingConstantReturnsTrue()
    {
        $binder = new Binder();
        $binder->bindConstant('answer')->to(42);
        assertTrue($binder->getInjector()->hasConstant('answer'));
    }

    /**
     * @test
     * @since  1.1.0
     */
    public function retrieveExistingConstantReturnsValue()
    {
        $binder = new Binder();
        $binder->bindConstant('answer')->to(42);
        assertEquals(42, $binder->getInjector()->getConstant('answer'));
    }

    /**
     * @test
     * @group  ioc_constantprovider
     * @since  1.6.0
     */
    public function constantViaInjectionProviderInstance()
    {
        $binder = new Binder();
        $binder->bindConstant('answer')
               ->toProvider(
                        NewInstance::of(InjectionProvider::class)
                                ->mapCalls(['get' => 42])
                );
        $injector = $binder->getInjector();
        assertTrue($injector->hasConstant('answer'));
        assertEquals(42, $injector->getConstant('answer'));
        $this->assertConstantInjection($binder->getInjector());
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     * @group              ioc_constantprovider
     * @since              1.6.0
     */
    public function constantViaInvalidInjectionProviderClassThrowsBindingException()
    {
        $binder = new Binder();
        $binder->bindConstant('answer')
               ->toProviderClass('\stdClass');
        $binder->getInjector()->getConstant('answer');
    }

    /**
     * @test
     * @group  ioc_constantprovider
     * @since  1.6.0
     */
    public function constantViaInjectionProviderClass()
    {
        $binder = new Binder();
        $binder->bindConstant('answer')
                ->toProviderClass(
                        new \ReflectionClass(AnswerConstantProvider::class)
                );
        $injector = $binder->getInjector();
        assertTrue($injector->hasConstant('answer'));
        assertEquals(42, $injector->getConstant('answer'));
        $this->assertConstantInjection($binder->getInjector());
    }

    /**
     * @test
     * @group  ioc_constantprovider
     * @since  1.6.0
     */
    public function constantViaInjectionProviderClassName()
    {
        $binder = new Binder();
        $binder->bindConstant('answer')
               ->toProviderClass(AnswerConstantProvider::class);
        $injector = $binder->getInjector();
        assertTrue($injector->hasConstant('answer'));
        assertEquals(42, $injector->getConstant('answer'));
        $this->assertConstantInjection($binder->getInjector());
    }

    /**
     * @since  2.1.0
     * @test
     * @group  issue_31
     */
    public function injectConstantViaClosure()
    {
        $binder = new Binder();
        $binder->bindConstant('answer')->toClosure(function() { return 42; });
        $this->assertConstantInjection($binder->getInjector());
    }

}
