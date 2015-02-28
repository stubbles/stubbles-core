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
        $binder       = new Binder();
        $mockProvider = $this->getMock('stubbles\ioc\InjectionProvider');
        $answer       = new \stubbles\test\ioc\Answer();
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->with($this->equalTo('answer'))
                     ->will($this->returnValue($answer));
        $binder->bind('stubbles\test\ioc\Answer')->toProvider($mockProvider);
        $question = $binder->getInjector()->getInstance('stubbles\test\ioc\AnotherQuestion');
        $this->assertInstanceOf('stubbles\test\ioc\AnotherQuestion', $question);
        $this->assertSame($answer, $question->getAnswer());
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function injectWithInvalidProviderClassThrowsException()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Answer')->toProviderClass('\stdClass');
        $binder->getInjector()->getInstance('stubbles\test\ioc\AnotherQuestion');
    }

    /**
     * @test
     */
    public function injectWithProviderClassName()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Answer')->toProviderClass('stubbles\test\ioc\MyProviderClass');
        $question = $binder->getInjector()->getInstance('stubbles\test\ioc\AnotherQuestion');
        $this->assertInstanceOf('stubbles\test\ioc\AnotherQuestion', $question);
        $this->assertInstanceOf('stubbles\test\ioc\Answer', $question->getAnswer());
    }

    /**
     * @test
     */
    public function injectWithProviderClass()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Answer')
               ->toProviderClass(new \ReflectionClass('stubbles\test\ioc\MyProviderClass'));
        $question = $binder->getInjector()->getInstance('stubbles\test\ioc\AnotherQuestion');
        $this->assertInstanceOf('stubbles\test\ioc\AnotherQuestion', $question);
        $this->assertInstanceOf('stubbles\test\ioc\Answer', $question->getAnswer());
    }
}
