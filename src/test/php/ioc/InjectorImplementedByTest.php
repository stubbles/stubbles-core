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
use stubbles\lang\Mode;
use stubbles\test\ioc\Mikey;
use stubbles\test\ioc\Person;
use stubbles\test\ioc\Person3;
use stubbles\test\ioc\Person4;
use stubbles\test\ioc\Schst;
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
                Schst::class,
                $binder->getInjector()->getInstance(Person::class)
        );
    }

    /**
     * @test
     */
    public function explicitBindingOverwritesImplementedByAnnotation()
    {
        $binder = new Binder();
        $binder->bind(Person::class)->to(Mikey::class);
        assertInstanceOf(
                Mikey::class,
                $binder->getInjector()->getInstance(Person::class)
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
                Schst::class,
                $binder->getInjector()->getInstance(Person3::class)
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
                NewInstance::of(Mode::class)->mapCalls(['name' => 'PROD'])
        );
        assertInstanceOf(
                Schst::class,
                $binder->getInjector()->getInstance(Person3::class)
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
                NewInstance::of(Mode::class)->mapCalls(['name' => 'DEV'])
        );
        assertInstanceOf(
                Mikey::class,
                $binder->getInjector()->getInstance(Person3::class)
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
                NewInstance::of(Mode::class)->mapCalls(['name' => 'PROD'])
        );
        $binder->getInjector()->getInstance(Person4::class);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     * @since  6.0.0
     */
    public function throwsBindingExceptionWhenNoFallbackSpecifiedAndNoModeSet()
    {
        $binder = new Binder();
        $binder->getInjector()->getInstance(Person4::class);
    }
}
