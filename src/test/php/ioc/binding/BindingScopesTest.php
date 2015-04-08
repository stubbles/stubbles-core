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
use bovigo\callmap\NewInstance;
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
        assertInstanceOf(
                'stubbles\ioc\binding\SingletonBindingScope',
                $bindingScopes->singleton()
        );
    }

    /**
     * @test
     */
    public function usesPassedSingletonScope()
    {
        $singletonScope = NewInstance::of('stubbles\ioc\binding\BindingScope');
        $bindingScopes = new BindingScopes($singletonScope);
        assertSame($singletonScope, $bindingScopes->singleton());
    }

    /**
     * @test
     */
    public function usesPassedSessionScope()
    {
        $sessionScope = NewInstance::of('stubbles\ioc\binding\BindingScope');
        $bindingScopes = new BindingScopes(null, $sessionScope);
        assertSame($sessionScope, $bindingScopes->session());
    }
}
