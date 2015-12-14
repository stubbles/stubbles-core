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
use function bovigo\callmap\verify;
use bovigo\callmap\NewInstance;
use stubbles\test\ioc\AnotherQuestion;
use stubbles\test\ioc\Answer;
use stubbles\test\ioc\MyProviderClass;
/**
 * Test for stubbles\ioc\Injector with provider binding.
 *
 * @group  ioc
 */
class InjectorProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function injectWithProviderInstance()
    {
        $binder   = new Binder();
        $answer   = new Answer();
        $provider = NewInstance::of(InjectionProvider::class)
                ->mapCalls(['get' => $answer]);
        $binder->bind(Answer::class)->toProvider($provider);
        $question = $binder->getInjector()
                ->getInstance(AnotherQuestion::class);
        assertInstanceOf(
                AnotherQuestion::class,
                $question
        );
        assertSame($answer, $question->getAnswer());
        verify($provider, 'get')->received('answer');
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function injectWithInvalidProviderClassThrowsException()
    {
        $binder = new Binder();
        $binder->bind(Answer::class)->toProviderClass('\stdClass');
        $binder->getInjector()->getInstance(AnotherQuestion::class);
    }

    /**
     * @test
     */
    public function injectWithProviderClassName()
    {
        $binder = new Binder();
        $binder->bind(Answer::class)
                ->toProviderClass(MyProviderClass::class);
        $question = $binder->getInjector()
                ->getInstance(AnotherQuestion::class);
        assertInstanceOf(AnotherQuestion::class, $question);
        assertInstanceOf(Answer::class, $question->getAnswer());
    }

    /**
     * @test
     */
    public function injectWithProviderClass()
    {
        $binder = new Binder();
        $binder->bind(Answer::class)
                 ->toProviderClass(
                       new \ReflectionClass(MyProviderClass::class)
                );
        $question = $binder->getInjector()
                ->getInstance(AnotherQuestion::class);
        assertInstanceOf(AnotherQuestion::class, $question);
        assertInstanceOf(Answer::class, $question->getAnswer());
    }
}
