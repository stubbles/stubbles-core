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
use net\stubbles\ioc\module\BindingModule;
use net\stubbles\lang\BaseObject;
/**
 * Helper class for the test.
 */
class AppTestBindingModuleOne extends BaseObject implements BindingModule
{
    /**
     * configure the binder
     *
     * @param  net\stubbles\ioc\Binder  $binder
     */
    public function configure(Binder $binder)
    {
        $binder->bind('foo')->to('\\stdClass');
    }
}
/**
 * Helper class for the test.
 */
class AppTestBindingModuleTwo extends BaseObject implements BindingModule
{
    /**
     * configure the binder
     *
     * @param  net\stubbles\ioc\Binder  $binder
     */
    public function configure(Binder $binder)
    {
        $binder->bind('bar')->to('stdClass');
    }
}
/**
 * Helper class for the test.
 */
class AppClassWithBindings extends BaseObject
{
    /**
     * given project path
     *
     * @type  string
     */
    protected static $projectPath;

    /**
     * return list of bindings required for this command
     *
     * @param   string                           $projectPath
     * @return  array<string|net\stubbles\ioc\BindingModule>
     */
    public static function __bindings($projectPath)
    {
        self::$projectPath = $projectPath;
        return array(new AppTestBindingModuleOne(),
                     new AppTestBindingModuleTwo()
               );
    }

    /**
     * returns set project path
     *
     * @return  string
     */
    public static function getProjectPath()
    {
        return self::$projectPath;
    }

    /**
     * runs the command
     */
    public function run() { }
}
/**
 * Helper class for the test.
 */
class AppClassWithArgument extends BaseObject
{
    /**
     * given project path
     *
     * @type  string
     */
    protected $arg;

    /**
     * returns set project path
     *
     * @return  string
     * @Inject
     * @Named('argv.0')
     */
    public function setArgument($arg)
    {
        $this->arg = $arg;
    }

    /**
     * returns the argument
     *
     * @return  string
     */
    public function getArgument()
    {
        return $this->arg;
    }
}
/**
 * Test for net\stubbles\ioc\App.
 *
 * @group  ioc
 */
class AppTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidBindingModuleClassThrowsIllegalArgumentException()
    {
        App::createInjector('\\stdClass');
    }

    /**
     * @test
     * @expectedException  net\stubbles\lang\exception\IllegalArgumentException
     */
    public function invalidBindingModuleInstanceThrowsIllegalArgumentException()
    {
        App::createInjector(new \stdClass());
    }

    /**
     * @test
     */
    public function bindingModulesAreProcessed()
    {
        $injector = App::createInjector(new AppTestBindingModuleOne(),
                                        'net\\stubbles\\ioc\\AppTestBindingModuleTwo'
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
        $injector = App::createInjector(array(new AppTestBindingModuleOne(),
                                              'net\\stubbles\\ioc\\AppTestBindingModuleTwo'
                                        )
                    );
        $this->assertTrue($injector->hasBinding('foo'));
        $this->assertTrue($injector->hasBinding('bar'));
        $this->assertTrue($injector->hasBinding('net\stubbles\ioc\Injector'));
        $this->assertSame($injector, $injector->getInstance('net\stubbles\ioc\Injector'));
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = App::createInstance('net\\stubbles\\ioc\\AppClassWithBindings',
                                                      'projectPath'
                                  );
        $this->assertInstanceOf('net\\stubbles\\ioc\\AppClassWithBindings',
                                $appCommandWithBindings
        );
        $this->assertEquals('projectPath', AppClassWithBindings::getProjectPath());
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceWithoutBindings()
    {
        $this->assertInstanceOf('net\\stubbles\\ioc\\AppTestBindingModuleTwo',
                                App::createInstance('net\\stubbles\\ioc\\AppTestBindingModuleTwo',
                                                    'projectPath'
                                )
        );
    }

    /**
     * @test
     */
    public function createInstanceWithArguments()
    {
        $appClassWithArgument = App::createInstance('net\\stubbles\\ioc\\AppClassWithArgument',
                                                    'projectPath',
                                                    array('foo')
                                );
        $this->assertInstanceOf('net\\stubbles\\ioc\\AppClassWithArgument',
                                $appClassWithArgument
        );
        $this->assertEquals('foo', $appClassWithArgument->getArgument());
    }
}
?>