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

use function bovigo\assert\assert;
use function bovigo\assert\predicate\isInstanceOf;
use function bovigo\assert\predicate\isSameAs;
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
        assert(
                $bindingScopes->singleton(),
                isInstanceOf(SingletonBindingScope::class)
        );
    }

    /**
     * @test
     */
    public function usesPassedSingletonScope()
    {
        $singletonScope = NewInstance::of(BindingScope::class);
        $bindingScopes  = new BindingScopes($singletonScope);
        assert($bindingScopes->singleton(), isSameAs($singletonScope));
    }

    /**
     * @test
     */
    public function usesPassedSessionScope()
    {
        $sessionScope  = NewInstance::of(BindingScope::class);
        $bindingScopes = new BindingScopes(null, $sessionScope);
        assert($bindingScopes->session(), isSameAs($sessionScope));
    }
}
