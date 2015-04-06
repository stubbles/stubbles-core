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
                'stubbles\test\ioc\Schst',
                $binder->getInjector()
                        ->getInstance('stubbles\test\ioc\Person2')
        );
    }

    /**
     * @test
     */
    public function explicitBindingOverwritesProvidedByAnnotation()
    {
        $binder = new Binder();
        $binder->bind('stubbles\test\ioc\Person2')->to('stubbles\test\ioc\Mikey');
        assertInstanceOf(
                'stubbles\test\ioc\Mikey',
                $binder->getInjector()
                        ->getInstance('stubbles\test\ioc\Person2')
        );
    }
}
