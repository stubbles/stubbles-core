<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles;
use bovigo\callmap\NewInstance;
use stubbles\ioc\Binder;
use stubbles\lang\Mode;
use org\bovigo\vfs\vfsStream;

use function bovigo\assert\assert;
use function bovigo\assert\assertFalse;
use function bovigo\assert\assertTrue;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isSameAs;
use function bovigo\callmap\verify;
/**
 * Test for stubbles\ioc\module\Runtime.
 *
 * @group  app
 */
class RuntimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * mocked mode instance
     *
     * @type  \stubbles\Environment
     */
    private $environment;
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
        $this->root        = vfsStream::setup('projects');
        // TODO: switch this to Environment::class with 8.0.0
        $this->environment = NewInstance::of(Mode::class);
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
        new Runtime();
        assertTrue(Runtime::initialized());
    }

    /**
     * @test
     */
    public function registerMethodsShouldBeCalledWithGivenProjectPath()
    {
        $runtime = new Runtime($this->environment);
        $runtime->configure(new Binder(), $this->root->url());
        verify($this->environment, 'registerErrorHandler')
                ->received($this->root->url());
        verify($this->environment, 'registerExceptionHandler')
                ->received($this->root->url());
    }

    /**
     * @test
     */
    public function givenEnvironmentShouldBeBound()
    {
        $runtime = new Runtime($this->environment);
        $binder  = new Binder();
        $runtime->configure($binder, $this->root->url());
        assert(
                $binder->getInjector()->getInstance(Environment::class),
                isSameAs($this->environment)
        );
    }

    /**
     * @test
     */
    public function noEnvironmentGivenDefaultsToProdEnvironment()
    {
        $runtime = new Runtime();
        $binder  = new Binder();
        try {
            $runtime->configure($binder, $this->root->url());
            $injector = $binder->getInjector();
            assert($injector->getInstance(Environment::class)->name(), equals('PROD'));
        } finally {
            restore_error_handler();
            restore_exception_handler();
        }
    }

    /**
     * @test
     * @deprecated  since 7.0.0, will be removed with 8.0.0
     */
    public function givenModeShouldBeBound()
    {
        $runtime = new Runtime($this->environment);
        $binder  = new Binder();
        $runtime->configure($binder, $this->root->url());
        assert(
                $binder->getInjector()->getInstance(Mode::class),
                isSameAs($this->environment)
        );
    }

    /**
     * @test
     * @since  4.0.0
     */
    public function bindsEnvironmentProvidedViaCallable()
    {
        $runtime = new Runtime(function() { return $this->environment; });
        $binder  = new Binder();
        $runtime->configure($binder, $this->root->url());
        assert(
                $binder->getInjector()->getInstance(Mode::class),
                isSameAs($this->environment)
        );
        verify($this->environment, 'registerErrorHandler')
                ->received($this->root->url());
        verify($this->environment, 'registerExceptionHandler')
                ->received($this->root->url());
    }

    /**
     * @test
     * @expectedException  InvalidArgumentException
     * @since  4.0.0
     */
    public function createWithNonModeThrowsIllegalArgumentException()
    {
        new Runtime(new \stdClass());
    }

    /**
     * @test
     * @since  3.4.0
     */
    public function doesNotBindPropertiesWhenConfigFileIsMissing()
    {
        $binder = NewInstance::of(Binder::class);
        $runtime = new Runtime($this->environment);
        $runtime->configure($binder, $this->root->url());
        verify($binder, 'bindProperties')->wasNeverCalled();
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
        $binder  = NewInstance::of(Binder::class);
        $runtime = new Runtime($this->environment);
        $runtime->configure($binder, $this->root->url());
        verify($binder, 'bindProperties')->wasCalledOnce();
    }

    /**
     * @test
     */
    public function projectPathIsBound()
    {
        $binder  = new Binder();
        $runtime = new Runtime($this->environment);
        $runtime->configure($binder, $this->root->url());
        assert(
                $binder->getInjector()->getConstant('stubbles.project.path'),
                equals($this->root->url())
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
        $runtime = new Runtime($this->environment);
        $runtime->configure($binder, $this->root->url());
        assert(
                $binder->getInjector()->getConstant($constantName),
                equals($this->getProjectPath($pathPart))
        );
    }

    /**
     * returns constant names and values
     *
     * @return  array
     */
    public function getWithAdditionalConstants()
    {
        return array_merge(
                $this->getConstants(),
                ['user' => ['user', 'stubbles.user.path']]
        );
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
        $runtime = new Runtime($this->environment);
        $runtime->addPathType('user')->configure($binder, $this->root->url());
        assert(
                $binder->getInjector()->getConstant($constantName),
                equals($this->getProjectPath($pathPart))
        );
    }
}
