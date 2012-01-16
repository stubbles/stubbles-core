<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\ioc\module;
use net\stubbles\ioc\Binder;
use net\stubbles\ioc\Injector;
/**
 * Test for net\stubbles\ioc\module\ArgumentsBindingModule.
 *
 * @group  ioc
 * @group  ioc_module
 */
class ArgumentsBindingModuleTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function argumentsAreBound()
    {
        $injector               = new Injector();
        $argumentsBindingModule = new ArgumentsBindingModule(array('foo', 'bar', 'baz'));
        $argumentsBindingModule->configure(new Binder($injector));
        $this->assertTrue($injector->hasConstant('argv.0'));
        $this->assertTrue($injector->hasConstant('argv.1'));
        $this->assertTrue($injector->hasConstant('argv.2'));
        $this->assertEquals('foo', $injector->getConstant('argv.0'));
        $this->assertEquals('bar', $injector->getConstant('argv.1'));
        $this->assertEquals('baz', $injector->getConstant('argv.2'));
    }
}
?>