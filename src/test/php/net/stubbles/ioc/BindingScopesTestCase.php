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
 * Test for net\stubbles\ioc\BindingScopes
 *
 * @group  ioc
 */
class BindingScopesTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createsSingletonScopeIfNonePassed()
    {
        $bindingScopes = new binding\BindingScopes();
        $this->assertInstanceOf('net\\stubbles\\ioc\\binding\\SingletonBindingScope',
                                $bindingScopes->getSingletonScope()
        );
    }

    /**
     * @test
     */
    public function usesPassedSingletonScope()
    {
        $mockSingletonScope = $this->getMock('net\\stubbles\\ioc\\binding\\BindingScope');
        $bindingScopes = new binding\BindingScopes($mockSingletonScope);
        $this->assertSame($mockSingletonScope,
                          $bindingScopes->getSingletonScope()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\RuntimeException
     */
    public function retrievingSessionScopeWithoutPriorSettingThrowsRuntimeException()
    {
        $bindingScopes = new binding\BindingScopes();
        $bindingScopes->getSessionScope();
    }

    /**
     * @test
     */
    public function usesPassedSessionScope()
    {
        $mockSessionScope = $this->getMock('net\\stubbles\\ioc\\binding\\BindingScope');
        $bindingScopes = new binding\BindingScopes(null, $mockSessionScope);
        $this->assertSame($mockSessionScope,
                          $bindingScopes->getSessionScope()
        );
    }

    /**
     * @test
     */
    public function usesLaterSetSessionScope()
    {
        $mockSessionScope = $this->getMock('net\\stubbles\\ioc\\binding\\BindingScope');
        $bindingScopes = new binding\BindingScopes(null);
        $this->assertSame($mockSessionScope,
                          $bindingScopes->setSessionScope($mockSessionScope)
                                        ->getSessionScope()
        );
    }
}
?>