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
use stubbles\test\ioc\Mikey;
use stubbles\test\ioc\Person2;
use stubbles\test\ioc\Schst;
/**
 * Test for stubbles\ioc\Injector with the ProvidedBy annotation.
 *
 * @group  ioc
 */
class InjectorProvidedByTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group  bug226
     */
    public function annotatedProviderClassIsUsedWhenNoExplicitBindingSpecified()
    {
        $binder = new Binder();
        assertInstanceOf(
                Schst::class,
                $binder->getInjector()->getInstance(Person2::class)
        );
    }

    /**
     * @test
     */
    public function explicitBindingOverwritesProvidedByAnnotation()
    {
        $binder = new Binder();
        $binder->bind(Person2::class)->to(Mikey::class);
        assertInstanceOf(
                Mikey::class,
                $binder->getInjector()->getInstance(Person2::class)
        );
    }
}
