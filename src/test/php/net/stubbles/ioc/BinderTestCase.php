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
 * Test for net\stubbles\ioc\Binder
 *
 * @group  ioc
 */
class BinderTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function returnsInjectorInstance()
    {
        $binder   = new Binder();
        $injector = $binder->getInjector();
        $this->assertInstanceOf('net\\stubbles\\ioc\\Injector', $injector);
    }

    /**
     * @test
     */
    public function passesSessionScopeToBindingIndex()
    {
        $mockIndex        = $this->getMock('net\\stubbles\\ioc\\binding\\BindingIndex');
        $binder           = new Binder($mockIndex);
        $mockSessionScope = $this->getMock('net\\stubbles\\ioc\\binding\\BindingScope');
        $mockIndex->expects($this->once())
                  ->method('setSessionScope')
                  ->with($this->equalTo($mockSessionScope));
        $this->assertSame($binder, $binder->setSessionScope($mockSessionScope));
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function addBindingReturnsAddedBinding()
    {
        $binder   = new Binder();
        $mockBinding = $this->getMock('net\\stubbles\\ioc\\binding\\Binding');
        $this->assertSame($mockBinding, $binder->addBinding($mockBinding));
    }

}
?>