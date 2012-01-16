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
 * Interface with annotation.
 *
 * @ImplementedBy(net\stubbles\ioc\Schst.class)
 */
interface Person
{
    /**
     * a method
     */
    public function sayHello();
}
/**
 * The default implementation.
 */
class Schst implements Person
{
    /**
     * a method
     */
    public function sayHello()
    {
        return "My name is schst.";
    }
}
/**
 * An alternative implementation.
 */
class Mikey implements Person
{
    /**
     * a method
     */
    public function sayHello()
    {
        return "My name is mikey.";
    }
}

/**
 * Test for net\stubbles\ioc\Injector with the ImplementedBy annotation.
 *
 * @group  ioc
 */
class InjectorImplementedByTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createsInstanceFromImplementedByAnnotationIfNoExplicitBindingsSet()
    {
        $binder = new Binder();
        $this->assertInstanceOf('net\\stubbles\\ioc\\Schst',
                                $binder->getInjector()
                                       ->getInstance('net\\stubbles\\ioc\\Person')
        );
    }

    /**
     * @test
     */
    public function explicitBindingOverwritesImplementedByAnnotation()
    {
        $binder = new Binder();
        $binder->bind('net\\stubbles\\ioc\\Person')->to('net\\stubbles\\ioc\\Mikey');
        $this->assertInstanceOf('net\\stubbles\\ioc\\Mikey',
                                $binder->getInjector()
                                       ->getInstance('net\\stubbles\\ioc\\Person')
        );
    }
}
?>