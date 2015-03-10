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
 * Test for stubbles\ioc\BindingScopes
 *
 * @group  ioc
 */
class BindingScopesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createsSingletonScopeIfNonePassed()
    {
        $bindingScopes = new binding\BindingScopes();
        $this->assertInstanceOf('stubbles\ioc\binding\SingletonBindingScope',
                                $bindingScopes->singleton()
        );
    }

    /**
     * @test
     */
    public function usesPassedSingletonScope()
    {
        $mockSingletonScope = $this->getMock('stubbles\ioc\binding\BindingScope');
        $bindingScopes = new binding\BindingScopes($mockSingletonScope);
        $this->assertSame($mockSingletonScope,
                          $bindingScopes->singleton()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\RuntimeException
     */
    public function retrievingSessionScopeWithoutPriorSettingThrowsRuntimeException()
    {
        $bindingScopes = new binding\BindingScopes();
        $bindingScopes->session();
    }

    /**
     * @test
     */
    public function usesPassedSessionScope()
    {
        $mockSessionScope = $this->getMock('stubbles\ioc\binding\BindingScope');
        $bindingScopes = new binding\BindingScopes(null, $mockSessionScope);
        $this->assertSame($mockSessionScope,
                          $bindingScopes->session()
        );
    }

    /**
     * @test
     */
    public function usesLaterSetSessionScope()
    {
        $mockSessionScope = $this->getMock('stubbles\ioc\binding\BindingScope');
        $bindingScopes = new binding\BindingScopes(null);
        $this->assertSame($mockSessionScope,
                          $bindingScopes->setSessionScope($mockSessionScope)
                                        ->session()
        );
    }
}
