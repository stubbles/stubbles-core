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
 * Test for stubbles\ioc\module\Runtime.
 *
 * @group  ioc
 * @group  ioc_module
 */
class RuntimeTest extends \PHPUnit_Framework_TestCase
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
        Runtime::reset();
    }

    /**
     * clean up test environment
     */
    public function tearDown()
    {
        Runtime::reset();
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function runtimeIsNotInitializedWhenNoInstanceCreated()
    {
        $this->assertFalse(Runtime::initialized());
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function runtimeIsInitializedAfterFirstInstanceCreation()
    {
        new Runtime($this->root->url());
        $this->assertTrue(Runtime::initialized());
    }

    /**
     * @test
     */
    public function registerMethodsShouldBeCalledWithGivenProjectPath()
    {
        $this->mockMode->expects(once())
                       ->method('registerErrorHandler')
                       ->with(equalTo($this->root->url()));
        $this->mockMode->expects(once())
                       ->method('registerExceptionHandler')
                       ->with(equalTo($this->root->url()));
        new Runtime($this->root->url(), $this->mockMode);
    }

    /**
     * @test
     */
    public function givenModeShouldBeBound()
    {
        $runtime = new Runtime($this->root->url(), $this->mockMode);
        $binder  = new Binder();
        $runtime->configure($binder);
        $this->assertSame(
                $this->mockMode,
                $binder->getInjector()->getInstance('stubbles\lang\Mode')
        );
    }

    /**
     * @test
     */
    public function noModeGivenDefaultsToProdMode()
    {
        $runtime = new Runtime($this->root->url());
        $binder  = new Binder();
        $runtime->configure($binder);
        $injector = $binder->getInjector();
        $this->assertTrue($injector->hasExplicitBinding('stubbles\lang\Mode'));
        $this->assertEquals('PROD',
                            $injector->getInstance('stubbles\lang\Mode')
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
        $this->mockMode->expects(once())
                ->method('registerErrorHandler')
                ->with(equalTo($this->root->url()));
        $this->mockMode->expects(once())
                ->method('registerExceptionHandler')
                ->with(equalTo($this->root->url()));
        $runtime = new Runtime($this->root->url(), function() { return $this->mockMode; });
        $binder  = new Binder();
        $runtime->configure($binder);
        $this->assertSame(
                $this->mockMode,
                $binder->getInjector()->getInstance('stubbles\lang\Mode')
        );
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  4.0.0
     */
    public function createWithNonModeThrowsIllegalArgumentException()
    {
        new Runtime($this->root->url(), new \stdClass());
    }

    /**
     * @test
     * @since  3.4.0
     */
    public function doesNotBindPropertiesWhenConfigFileIsMissing()
    {
        $mockBinder = $this->getMock('stubbles\ioc\Binder', ['bindProperties']);
        $mockBinder->expects(never())->method('bindProperties');
        $runtime = new Runtime($this->root->url(), $this->mockMode);
        $runtime->configure($mockBinder);
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
        $mockBinder->expects(once())->method('bindProperties');
        $runtime = new Runtime($this->root->url(), $this->mockMode);
        $runtime->configure($mockBinder);
    }

    /**
     * @test
     */
    public function projectPathIsBound()
    {
        $binder  = new Binder();
        $runtime = new Runtime($this->root->url(), $this->mockMode);
        $runtime->configure($binder);
        $this->assertEquals(
                $this->root->url(),
                $binder->getInjector()->getConstant('stubbles.project.path')
        );
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
        $binder  = new Binder();
        $runtime = new Runtime($this->root->url(), $this->mockMode);
        $runtime->configure($binder);
        $this->assertEquals(
                $this->getProjectPath($pathPart),
                $binder->getInjector()->getConstant($constantName)
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
        $binder  = new Binder();
        $runtime = new Runtime($this->root->url(), $this->mockMode);
        $runtime->addPathType('user')->configure($binder);
        $this->assertEquals(
                $this->getProjectPath($pathPart),
                $binder->getInjector()->getConstant($constantName)
        );
    }
}
