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
        $mockBindingScope = $this->getMock('net\stubbles\ioc\binding\BindingScope');
        $binder->bind('\stdClass')
               ->to('\stdClass')
               ->in($mockBindingScope);
        $injector = $binder->getInjector();

        $this->assertTrue($injector->hasBinding('\stdClass'));
        $instance = new \stdClass();
        $mockBindingScope->expects(($this->once()))
                         ->method('getInstance')
                         ->will($this->returnValue($instance));
        $this->assertSame($instance, $injector->getInstance('\stdClass'));
    }
}
?>