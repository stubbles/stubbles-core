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
use org\stubbles\test\ioc\AppTestBindingModuleOne;
/**
 * Test for net\stubbles\ioc\App.
 *
 * @since  2.0.0
 * @group  ioc
 */
class BindingFactoryTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidBindingModuleClassThrowsIllegalArgumentException()
    {
        BindingFactory::createInjector('\stdClass');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidBindingModuleInstanceThrowsIllegalArgumentException()
    {
        BindingFactory::createInjector(new \stdClass());
    }

    /**
     * @test
     */
    public function bindingModulesAreProcessed()
    {
        $injector = BindingFactory::createInjector(new AppTestBindingModuleOne(),
                                                   'org\stubbles\test\ioc\AppTestBindingModuleTwo'
                    );
        $this->assertTrue($injector->hasBinding('foo'));
        $this->assertTrue($injector->hasBinding('bar'));
        $this->assertTrue($injector->hasBinding('net\stubbles\ioc\Injector'));
        $this->assertSame($injector, $injector->getInstance('net\stubbles\ioc\Injector'));
    }

    /**
     * @test
     * @since  1.6.0
     */
    public function bindingModulesAreProcessedIfPassedAsArray()
    {
        $injector = BindingFactory::createInjector(array(new AppTestBindingModuleOne(),
                                                         'org\stubbles\test\ioc\AppTestBindingModuleTwo'
                                                   )
                    );
        $this->assertTrue($injector->hasBinding('foo'));
        $this->assertTrue($injector->hasBinding('bar'));
        $this->assertTrue($injector->hasBinding('net\stubbles\ioc\Injector'));
        $this->assertSame($injector, $injector->getInstance('net\stubbles\ioc\Injector'));
    }
}
?>