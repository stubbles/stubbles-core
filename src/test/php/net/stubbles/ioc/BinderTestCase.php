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
    public function binderAlwaysReturnsSameInjector()
    {
        $binder    = new Binder();
        $injector  = $binder->getInjector();
        $injector2 = $binder->getInjector();
        $this->identicalTo($injector, $injector2);
    }

    /**
     * @test
     */
    public function passesSessionScopeToInjector()
    {
        $mockInjector     = $this->getMock('net\\stubbles\\ioc\\Injector');
        $binder           = new Binder($mockInjector);
        $mockSessionScope = $this->getMock('net\\stubbles\\ioc\\binding\\BindingScope');
        $mockInjector->expects($this->once())
                     ->method('setSessionScope')
                     ->with($this->equalTo($mockSessionScope));
        $this->assertSame($binder, $binder->setSessionScope($mockSessionScope));
    }
}
?>