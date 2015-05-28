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
use bovigo\callmap\NewInstance;
/**
 * Test for stubbles\ioc\Injector with the ImplementedBy annotation.
 *
 * @group  ioc
 */
class InjectorImplementedByTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createsInstanceFromImplementedByAnnotationIfNoExplicitBindingsSet()
    {
        $binder = new Binder();
        assertInstanceOf(
                'stubbles\test\ioc\Schst',
                $binder->getInjector()->getInstance('stubbles\test\ioc\Person')
        );
    }

    /**
     * @test
     */
    public function explicitBindingOverwritesImplementedByAnnotation()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Person')->to('stubbles\test\ioc\Mikey');
        assertInstanceOf(
                'stubbles\test\ioc\Mikey',
                $binder->getInjector()->getInstance('stubbles\test\ioc\Person')
        );
    }

    /**
     * @test
     * @since  6.0.0
     */
    public function fallsBackToDefaultImplementedByIfNoModeSet()
    {
        $binder = new Binder();
        assertInstanceOf(
                'stubbles\test\ioc\Schst',
                $binder->getInjector()->getInstance('stubbles\test\ioc\Person3')
        );
    }

    /**
     * @test
     * @since  6.0.0
     */
    public function usesFallbackIfNoSpecialImplementationDefinedForMode()
    {

        $binder = new Binder();
        $binder->bindMode(
                NewInstance::of('stubbles\lang\Mode')->mapCalls(['name' => 'PROD'])
        );
        assertInstanceOf(
                'stubbles\test\ioc\Schst',
                $binder->getInjector()->getInstance('stubbles\test\ioc\Person3')
        );
    }

    /**
     * @test
     * @since  6.0.0
     */
    public function usesImplementationSpecifiedForMode()
    {

        $binder = new Binder();
        $binder->bindMode(
                NewInstance::of('stubbles\lang\Mode')->mapCalls(['name' => 'DEV'])
        );
        assertInstanceOf(
                'stubbles\test\ioc\Mikey',
                $binder->getInjector()->getInstance('stubbles\test\ioc\Person3')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     * @since  6.0.0
     */
    public function throwsBindingExceptionWhenNoFallbackSpecified()
    {
        $binder = new Binder();
        $binder->bindMode(
                NewInstance::of('stubbles\lang\Mode')->mapCalls(['name' => 'PROD'])
        );
        $binder->getInjector()->getInstance('stubbles\test\ioc\Person4');
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     * @since  6.0.0
     */
    public function throwsBindingExceptionWhenNoFallbackSpecifiedAndNoModeSet()
    {
        $binder = new Binder();
        $binder->getInjector()->getInstance('stubbles\test\ioc\Person4');
    }
}
