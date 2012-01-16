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
/**
 * Provider class for the test.
 */
class InjectorProvidedByProvider extends BaseObject implements InjectionProvider
{
    /**
     * returns the value to provide
     *
     * @param   string  $name  optional
     * @return  mixed
     */
    public function get($name = null)
    {
        return new Schst2();
    }
}
/**
 * Helper class for the test
 *
 * @ProvidedBy('net\\stubbles\\ioc\\InjectorProvidedByProvider')
 */
interface Person1
{
    public function sayHello();
}
/**
 * Helper class for the test
 *
 * @ProvidedBy(net\stubbles\ioc\InjectorProvidedByProvider.class)
 */
interface Person2
{
    public function sayHello2();
}
/**
 * Helper class for the test
 */
class Schst2 implements Person1, Person2
{
    public function sayHello()
    {
        return 'My name is schst.';
    }

    public function sayHello2()
    {
        return 'My name is still schst.';
    }
}
/**
 * Helper class for the test
 */
class Mikey2 implements Person1
{
    public function sayHello()
    {
        return 'My name is mikey.';
    }
}
/**
 * Test for net\stubbles\ioc\Injector with the ProvidedBy annotation.
 *
 * @group  ioc
 */
class InjectorProvidedByTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function annotatedProviderClassNameIsUsedWhenNoExplicitBindingSpecified()
    {
        $binder   = new Binder();
        $this->assertInstanceOf('net\\stubbles\\ioc\\Schst2',
                                $binder->getInjector()
                                       ->getInstance('net\\stubbles\\ioc\\Person1')
        );
    }

    /**
     * @test
     * @group  bug226
     */
    public function annotatedProviderClassIsUsedWhenNoExplicitBindingSpecified()
    {
        $binder = new Binder();
        $this->assertInstanceOf('net\\stubbles\\ioc\\Schst2',
                                $binder->getInjector()
                                       ->getInstance('net\\stubbles\\ioc\\Person2')
        );
    }

    /**
     * @test
     */
    public function explicitBindingOverwritesProvidedByAnnotation()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Person1')->to('net\\stubbles\\ioc\\Mikey2');
        $this->assertInstanceOf('net\\stubbles\\ioc\\Mikey2',
                                $binder->getInjector()
                                       ->getInstance('net\\stubbles\\ioc\\Person1')
        );
    }
}
?>