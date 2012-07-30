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
 * Test for net\stubbles\ioc\Injector with the ImplementedBy annotation.
 *
 * @group  ioc
 */
class InjectorImplementedByTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createsInstanceFromImplementedByAnnotationIfNoExplicitBindingsSet()
    {
        $binder = new Binder();
        $this->assertInstanceOf('org\stubbles\test\ioc\Schst',
                                $binder->getInjector()
                                       ->getInstance('org\stubbles\test\ioc\Person')
        );
    }

    /**
     * @test
     */
    public function explicitBindingOverwritesImplementedByAnnotation()
    {
        $binder = new Binder();
        $binder->bind('org\stubbles\test\ioc\Person')->to('org\stubbles\test\ioc\Mikey');
        $this->assertInstanceOf('org\stubbles\test\ioc\Mikey',
                                $binder->getInjector()
                                       ->getInstance('org\stubbles\test\ioc\Person')
        );
    }
}
?>