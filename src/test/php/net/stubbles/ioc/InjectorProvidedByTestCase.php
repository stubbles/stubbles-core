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
use net\stubbles\lang\BaseObject;
/**
 * Test for net\stubbles\ioc\Injector with the ProvidedBy annotation.
 *
 * @group  ioc
 */
class InjectorProvidedByTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @group  bug226
     */
    public function annotatedProviderClassIsUsedWhenNoExplicitBindingSpecified()
    {
        $binder = new Binder();
        $this->assertInstanceOf('org\stubbles\test\ioc\Schst',
                                $binder->getInjector()
                                       ->getInstance('org\stubbles\test\ioc\Person2')
        );
    }

    /**
     * @test
     */
    public function explicitBindingOverwritesProvidedByAnnotation()
    {
        $binder = new Binder();
        $binder->bind('org\stubbles\test\ioc\Person2')->to('org\stubbles\test\ioc\Mikey');
        $this->assertInstanceOf('org\stubbles\test\ioc\Mikey',
                                $binder->getInjector()
                                       ->getInstance('org\stubbles\test\ioc\Person2')
        );
    }
}
?>