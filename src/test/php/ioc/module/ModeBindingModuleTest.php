<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc\module;
use stubbles\ioc\Binder;
use org\bovigo\vfs\vfsStream;
/**
 * Test for stubbles\ioc\module\ModeBindingModule.
 *
 * @group  ioc
 * @group  ioc_module
 */
class ModeBindingModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * mocked mode instance
     *
     * @type  \PHPUnit_Framework_MockObject_MockObject
     */
    private $mockMode;
    /**
     * root path
     *
     * @type  org\bovigo\vfs\vfsStreamDirectory
     */
    private $root;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root     = vfsStream::setup('projects');
        $this->mockMode = $this->getMock('stubbles\lang\Mode');
    }

    /**
     * @test
     */
    public function registerMethodsShouldBeCalledWithGivenProjectPath()
    {
        $this->mockMode->expects($this->once())
                       ->method('registerErrorHandler')
                       ->with($this->equalTo($this->root->url()));
        $this->mockMode->expects($this->once())
                       ->method('registerExceptionHandler')
                       ->with($this->equalTo($this->root->url()));
        new ModeBindingModule($this->root->url(), $this->mockMode);
    }

    /**
     * @test
     */
    public function givenModeShouldBeBound()
    {
        $modeBindingModule = new ModeBindingModule($this->root->url(), $this->mockMode);
        $binder            = new Binder();
        $modeBindingModule->configure($binder);
        $this->assertTrue($binder->hasExplicitBinding('stubbles\lang\Mode'));
        $this->assertSame($this->mockMode,
                          $binder->getInjector()
                                 ->getInstance('stubbles\lang\Mode')
        );
    }

    /**
     * @test
     */
    public function noModeGivenDefaultsToProdMode()
    {
        $modeBindingModule = new ModeBindingModule($this->root->url());
        $binder            = new Binder();
        $modeBindingModule->configure($binder);
        $this->assertTrue($binder->hasExplicitBinding('stubbles\lang\Mode'));
        $this->assertEquals('PROD',
                            $binder->getInjector()
                                   ->getInstance('stubbles\lang\Mode')
                                   ->name()
        );
        restore_error_handler();
        restore_exception_handler();
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function bindsModeProvidedViaCallable()
    {
        $this->mockMode->expects($this->once())
                       ->method('registerErrorHandler')
                       ->with($this->equalTo($this->root->url()));
        $this->mockMode->expects($this->once())
                       ->method('registerExceptionHandler')
                       ->with($this->equalTo($this->root->url()));
        $modeBindingModule = new ModeBindingModule($this->root->url(), function() { return $this->mockMode; });
        $binder            = new Binder();
        $modeBindingModule->configure($binder);
        $this->assertTrue($binder->hasExplicitBinding('stubbles\lang\Mode'));
        $this->assertSame($this->mockMode,
                          $binder->getInjector()
                                 ->getInstance('stubbles\lang\Mode')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\lang\exception\IllegalArgumentException
     * @since  4.0.0
     */
    public function createWithNonModeThrowsIllegalArgumentException()
    {
        new ModeBindingModule($this->root->url(), new \stdClass());
    }

    /**
     * @test
     * @since  3.4.0
     */
    public function doesNotBindPropertiesWhenConfigFileIsMissing()
    {
        $mockBinder = $this->getMock('stubbles\ioc\Binder', ['bindProperties']);
        $mockBinder->expects($this->never())
                   ->method('bindProperties');
        $modeBindingModule = new ModeBindingModule($this->root->url(), $this->mockMode);
        $modeBindingModule->configure($mockBinder);
    }

    /**
     * @test
     * @since  3.4.0
     */
    public function bindsPropertiesWhenConfigFilePresent()
    {
        vfsStream::newFile('config/config.ini')
                 ->withContent("[config]
stubbles.locale=\"de_DE\"
stubbles.number.decimals=4
stubbles.webapp.xml.serializeMode=true")
                 ->at($this->root);
        $mockBinder = $this->getMock('stubbles\ioc\Binder', ['bindProperties']);
        $mockBinder->expects($this->once())
                   ->method('bindProperties');
        $modeBindingModule = new ModeBindingModule($this->root->url(), $this->mockMode);
        $modeBindingModule->configure($mockBinder);
    }

    /**
     * returns constant names and values
     *
     * @return  array
     */
    public function getConstants()
    {
        return ['config' => ['config', 'stubbles.config.path'],
                'log'    => ['log', 'stubbles.log.path']
        ];
    }

    /**
     * returns complete path
     *
     * @param   string  $part
     * @return  string
     */
    private function getProjectPath($part)
    {
        return $this->root->url() . DIRECTORY_SEPARATOR . $part;
    }

    /**
     * @param  string  $pathPart
     * @param  string  $constantName
     * @test
     * @dataProvider  getConstants
     */
    public function pathesShouldBeBoundAsConstant($pathPart, $constantName)
    {
        $binder = new Binder();
        $modeBindingModule = new ModeBindingModule($this->root->url(), $this->mockMode);
        $modeBindingModule->configure($binder);
        $this->assertTrue($binder->hasConstant($constantName));
        $this->assertEquals($this->getProjectPath($pathPart),
                            $binder->getInjector()
                                   ->getConstant($constantName)
        );
    }

    /**
     * returns constant names and values
     *
     * @return  array
     */
    public function getWithAdditionalConstants()
    {
        return array_merge($this->getConstants(), ['user' => ['user', 'stubbles.user.path']]);
    }

    /**
     * @param  string  $pathPart
     * @param  string  $constantName
     * @test
     * @dataProvider  getWithAdditionalConstants
     */
    public function additionalPathTypesShouldBeBound($pathPart, $constantName)
    {
        $binder = new Binder();
        $modeBindingModule = new ModeBindingModule($this->root->url(), $this->mockMode);
        $modeBindingModule->addPathType('user')
                          ->configure($binder);
        $this->assertTrue($binder->hasConstant($constantName));
        $this->assertEquals($this->getProjectPath($pathPart),
                            $binder->getInjector()
                                   ->getConstant($constantName)
        );
    }
}
