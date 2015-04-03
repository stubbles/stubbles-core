<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc\binding;
/**
 * Test for stubbles\ioc\binding\BindingScopes
 *
 * @group  ioc
 * @group  ioc_binding
 */
class BindingScopesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createsSingletonScopeIfNonePassed()
    {
        $bindingScopes = new BindingScopes();
        $this->assertInstanceOf(
                'stubbles\ioc\binding\SingletonBindingScope',
                $bindingScopes->singleton()
        );
    }

    /**
     * @test
     */
    public function usesPassedSingletonScope()
    {
        $mockSingletonScope = $this->getMock('stubbles\ioc\binding\BindingScope');
        $bindingScopes = new BindingScopes($mockSingletonScope);
        $this->assertSame(
                $mockSingletonScope,
                $bindingScopes->singleton()
        );
    }

    /**
     * @test
     */
    public function usesPassedSessionScope()
    {
        $mockSessionScope = $this->getMock('stubbles\ioc\binding\BindingScope');
        $bindingScopes = new BindingScopes(null, $mockSessionScope);
        $this->assertSame(
                $mockSessionScope,
                $bindingScopes->session()
        );
    }
}
