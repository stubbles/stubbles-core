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
 * Helper class for the test.
 */
class OtherScoped extends BaseObject
{
    // intentionally empty
}
/**
 * Test for net\stubbles\ioc\Injector with the session scope.
 *
 * @group  ioc
 */
class InjectorOtherTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function otherScopeIsUsedToCreateInstance()
    {
        $binder = new Binder();
        $mockBindingScope = $this->getMock('net\\stubbles\\ioc\\BindingScope');
        $binder->bind('net\\stubbles\\ioc\\OtherScoped')
               ->to('net\\stubbles\\ioc\\OtherScoped')
               ->in($mockBindingScope);
        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('net\\stubbles\\ioc\\OtherScoped'));
        $instance = new OtherScoped();
        $mockBindingScope->expects(($this->once()))
                         ->method('getInstance')
                         ->will($this->returnValue($instance));
        $this->assertSame($instance, $injector->getInstance('net\\stubbles\\ioc\\OtherScoped'));
    }
}
?>