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
 * Test for net\stubbles\ioc\module\ModeBindingModule.
 *
 * @group  ioc
 * @group  ioc_module
 */
class ModeBindingModuleTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * mocked mode instance
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockMode;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->mockMode = $this->getMock('net\\stubbles\\lang\\Mode');
    }

    /**
     * @test
     */
    public function registerMethodsShouldBeCalledWithGivenProjectPath()
    {
        $projectPath = '/tmp';
        $this->mockMode->expects($this->once())
                       ->method('registerErrorHandler')
                       ->with($this->equalTo($projectPath));
        $this->mockMode->expects($this->once())
                       ->method('registerExceptionHandler')
                       ->with($this->equalTo($projectPath));
        $modeBindingModule = new ModeBindingModule($projectPath, $this->mockMode);
    }

    /**
     * @test
     */
    public function givenModeShouldBeBound()
    {
        $modeBindingModule = new ModeBindingModule('/tmp', $this->mockMode);
        $injector          = new Injector();
        $modeBindingModule->configure(new Binder($injector));
        $this->assertTrue($injector->hasExplicitBinding('net\\stubbles\\lang\\Mode'));
        $this->assertSame($this->mockMode, $injector->getInstance('net\\stubbles\\lang\\Mode'));
    }

    /**
     * @test
     */
    public function noModeGivenDefaultsToProdMode()
    {
        $modeBindingModule = new ModeBindingModule('/tmp');
        $injector          = new Injector();
        $modeBindingModule->configure(new Binder($injector));
        $this->assertTrue($injector->hasExplicitBinding('net\\stubbles\\lang\\Mode'));
        $this->assertEquals('PROD',
                            $injector->getInstance('net\\stubbles\\lang\\Mode')->name()
        );
        restore_error_handler();
        restore_exception_handler();
    }
}
?>