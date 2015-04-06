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
                $binder->getInjector()
                        ->getInstance('stubbles\test\ioc\Person')
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
                $binder->getInjector()
                        ->getInstance('stubbles\test\ioc\Person')
        );
    }
}
