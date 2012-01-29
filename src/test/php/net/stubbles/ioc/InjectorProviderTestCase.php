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
use net\stubbles\lang\BaseObject;
use net\stubbles\lang\reflect\ReflectionClass;
/**
 * Test for net\stubbles\ioc\Injector with provider binding.
 *
 * @group  ioc
 */
class InjectorProviderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function injectWithProviderInstance()
    {
        $binder       = new Binder();
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $answer       = new \org\stubbles\test\ioc\Answer();
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->with($this->equalTo('answer'))
                     ->will($this->returnValue($answer));
        $binder->bind('org\\stubbles\\test\\ioc\\Answer')->toProvider($mockProvider);
        $question = $binder->getInjector()->getInstance('org\\stubbles\\test\\ioc\\AnotherQuestion');
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\AnotherQuestion', $question);
        $this->assertSame($answer, $question->getAnswer());
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function injectWithInvalidProviderClassThrowsException()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Answer')->toProviderClass('\stdClass');
        $binder->getInjector()->getInstance('org\\stubbles\\test\\ioc\\AnotherQuestion');
    }

    /**
     * @test
     */
    public function injectWithProviderClassName()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Answer')->toProviderClass('org\\stubbles\\test\\ioc\\MyProviderClass');
        $question = $binder->getInjector()->getInstance('org\\stubbles\\test\\ioc\\AnotherQuestion');
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\AnotherQuestion', $question);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Answer', $question->getAnswer());
    }

    /**
     * @test
     */
    public function injectWithProviderClass()
    {
        $binder = new Binder();
        $binder->bind('org\\stubbles\\test\\ioc\\Answer')->toProviderClass(new ReflectionClass('org\\stubbles\\test\\ioc\\MyProviderClass'));
        $question = $binder->getInjector()->getInstance('org\\stubbles\\test\\ioc\\AnotherQuestion');
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\AnotherQuestion', $question);
        $this->assertInstanceOf('org\\stubbles\\test\\ioc\\Answer', $question->getAnswer());
    }
}
?>