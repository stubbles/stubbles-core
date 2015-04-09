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
use bovigo\callmap\NewInstance;
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
     * @type  \stubbles\lang\Mode
     */
    private $mode;
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
        $this->root = vfsStream::setup('projects');
        $this->mode = NewInstance::of('stubbles\lang\Mode');
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
        assertFalse(Runtime::initialized());
    }

    /**
     * @test
     * @since  5.0.0
     */
    public function runtimeIsInitializedAfterFirstInstanceCreation()
    {
        new Runtime($this->root->url());
        assertTrue(Runtime::initialized());
    }

    /**
     * @test
     */
    public function registerMethodsShouldBeCalledWithGivenProjectPath()
    {
        new Runtime($this->root->url(), $this->mode);
        assertEquals(
                [$this->root->url()],
                $this->mode->argumentsReceived('registerErrorHandler')
        );
        assertEquals(
                [$this->root->url()],
                $this->mode->argumentsReceived('registerExceptionHandler')
        );
    }

    /**
     * @test
     */
    public function givenModeShouldBeBound()
    {
        $runtime = new Runtime($this->root->url(), $this->mode);
        $binder  = new Binder();
        $runtime->configure($binder);
        assertSame(
                $this->mode,
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
        assertTrue($injector->hasExplicitBinding('stubbles\lang\Mode'));
        assertEquals(
                'PROD',
                $injector->getInstance('stubbles\lang\Mode')->name()
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
        $runtime = new Runtime($this->root->url(), function() { return $this->mode; });
        $binder  = new Binder();
        $runtime->configure($binder);
        assertSame(
                $this->mode,
                $binder->getInjector()->getInstance('stubbles\lang\Mode')
        );
        assertEquals(
                [$this->root->url()],
                $this->mode->argumentsReceived('registerErrorHandler')
        );
        assertEquals(
                [$this->root->url()],
                $this->mode->argumentsReceived('registerExceptionHandler')
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
        $binder = NewInstance::of('stubbles\ioc\Binder');
        $runtime = new Runtime($this->root->url(), $this->mode);
        $runtime->configure($binder);
        assertEquals(0, $binder->callsReceivedFor('bindProperties'));
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
        $binder  = NewInstance::of('stubbles\ioc\Binder');
        $runtime = new Runtime($this->root->url(), $this->mode);
        $runtime->configure($binder);
        assertEquals(1, $binder->callsReceivedFor('bindProperties'));
    }

    /**
     * @test
     */
    public function projectPathIsBound()
    {
        $binder  = new Binder();
        $runtime = new Runtime($this->root->url(), $this->mode);
        $runtime->configure($binder);
        assertEquals(
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
        $runtime = new Runtime($this->root->url(), $this->mode);
        $runtime->configure($binder);
        assertEquals(
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
        $runtime = new Runtime($this->root->url(), $this->mode);
        $runtime->addPathType('user')->configure($binder);
        assertEquals(
                $this->getProjectPath($pathPart),
                $binder->getInjector()->getConstant($constantName)
        );
    }
}
