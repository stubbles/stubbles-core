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

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
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
        assert($question, equals(new Question(42)));
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
        assertFalse(Binder::createInjector()->hasConstant('answer'));
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function retrieveNonExistingConstantThrowsBindingException()
    {
        Binder::createInjector()->getConstant('answer');
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
        assert($binder->getInjector()->getConstant('answer'), equals(42));
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
        assert($injector->getConstant('answer'), equals(42));
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
        assert($injector->getConstant('answer'), equals(42));
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
        assert($injector->getConstant('answer'), equals(42));
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
