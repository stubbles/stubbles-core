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
 * Helper class for the test.
 */
class Answer
{
    /**
     * the answer to all questions
     *
     * @return  int
     */
    public function answer()
    {
        return 42;
    }
}
/**
 * Helper class for the test.
 */
class AnotherQuestion
{
    /**
     * answer
     *
     * @type  Answer
     */
    private $answer;

    /**
     * @param  Answer  $answer
     * @Inject
     * @Named('answer')
     */
    public function setAnswer(Answer $answer)
    {
        $this->answer = $answer;
    }

    /**
     * returns answer
     *
     * @return  Answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }
}
/**
 * Helper class for the test.
 */
class MyProviderClass extends BaseObject implements InjectionProvider
{
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        return new Answer();
    }
}
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
        $answer       = new Answer();
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->with($this->equalTo('answer'))
                     ->will($this->returnValue($answer));
        $binder->bind('net\\stubbles\\ioc\\Answer')->toProvider($mockProvider);
        $question = $binder->getInjector()->getInstance('net\\stubbles\\ioc\\AnotherQuestion');
        $this->assertInstanceOf('net\\stubbles\\ioc\\AnotherQuestion', $question);
        $this->assertSame($answer, $question->getAnswer());
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\BindingException
     */
    public function injectWithInvalidProviderClassThrowsException()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Answer')->toProviderClass('\stdClass');
        $binder->getInjector()->getInstance('net\\stubbles\\ioc\\AnotherQuestion');
    }

    /**
     * @test
     */
    public function injectWithProviderClassName()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Answer')->toProviderClass('net\\stubbles\\ioc\\MyProviderClass');
        $question = $binder->getInjector()->getInstance('net\\stubbles\\ioc\\AnotherQuestion');
        $this->assertInstanceOf('net\\stubbles\\ioc\\AnotherQuestion', $question);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Answer', $question->getAnswer());
    }

    /**
     * @test
     */
    public function injectWithProviderClass()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Answer')->toProviderClass(new ReflectionClass('net\\stubbles\\ioc\\MyProviderClass'));
        $question = $binder->getInjector()->getInstance('net\\stubbles\\ioc\\AnotherQuestion');
        $this->assertInstanceOf('net\\stubbles\\ioc\\AnotherQuestion', $question);
        $this->assertInstanceOf('net\\stubbles\\ioc\\Answer', $question->getAnswer());
    }
}
?>