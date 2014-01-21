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
use net\stubbles\lang\reflect\annotation\Annotation;
use net\stubbles\lang\reflect\annotation\AnnotationCache;
use org\bovigo\vfs\vfsStream;
use org\stubbles\test\ioc\AppClassWithBindings;
use org\stubbles\test\ioc\AppUsingBindingModule;
/**
 * Test for net\stubbles\ioc\App.
 *
 * @group  ioc
 */
class AppTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @since  2.0.0
     * @test
     */
    public function createCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = AppClassWithBindings::create('projectPath');
        $this->assertInstanceOf('org\stubbles\test\ioc\AppClassWithBindings',
                                $appCommandWithBindings
        );
        $this->assertEquals('projectPath', AppClassWithBindings::getProjectPath());
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceUsingBindings()
    {
        $appCommandWithBindings = App::createInstance('org\stubbles\test\ioc\AppClassWithBindings',
                                                      'projectPath'
                                  );
        $this->assertInstanceOf('org\stubbles\test\ioc\AppClassWithBindings',
                                $appCommandWithBindings
        );
        $this->assertEquals('projectPath', AppClassWithBindings::getProjectPath());
    }

    /**
     * @test
     */
    public function createInstanceCreatesInstanceWithoutBindings()
    {
        $this->assertInstanceOf('org\stubbles\test\ioc\AppTestBindingModuleTwo',
                                App::createInstance('org\stubbles\test\ioc\AppTestBindingModuleTwo',
                                                    'projectPath'
                                )
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreateModeBindingModule()
    {
        $this->assertInstanceOf('net\stubbles\ioc\module\ModeBindingModule',
                                AppUsingBindingModule::getModeBindingModule()
        );
    }

    /**
     * @since  2.0.0
     * @test
     */
    public function canCreatePropertiesBindingModule()
    {
        $this->assertInstanceOf('net\stubbles\ioc\module\PropertiesBindingModule',
                                AppUsingBindingModule::getPropertiesBindingModule(__DIR__)
        );
    }

    /**
     * @since  2.1.0
     * @group  issue_33
     * @test
     */
    public function dynamicBindingViaClosure()
    {
        $this->assertEquals('closure',
                            AppClassWithBindings::create('projectPath')
                                                ->wasBoundBy()
        );
    }

    /**
     * creates a annotation cache with one annotation
     *
     * @return  string
     * @deprecated  since 3.1.0, will be removed with 4.0.0
     */
    private function createdCachedAnnotation()
    {
        return serialize(array(Annotation::TARGET_CLASS => array('foo' => array('bar' => new Annotation('bar')))));
    }

    /**
     * @deprecated  since 3.1.0, will be removed with 4.0.0
     * @since  3.0.0
     * @group  issue_58
     * @test
     */
    public function canCreateAppInstanceWithFileAnnotationCache()
    {
        $root = vfsStream::setup();
        $file = vfsStream::newFile('annotations.cache')
                         ->withContent($this->createdCachedAnnotation())
                         ->at($root);
        AppUsingBindingModule::callAnnotationFilePersistence($file->url());
        $this->assertTrue(AnnotationCache::has(Annotation::TARGET_CLASS, 'foo', 'bar'));

    }

    /**
     * @deprecated  since 3.1.0, will be removed with 4.0.0
     * @since  3.0.0
     * @group  issue_58
     * @test
     */
    public function canCreateAppInstanceWithOtherAnnotationCache()
    {
        $annotationData = $this->createdCachedAnnotation();
        AppUsingBindingModule::callAnnotationPersistence(function() use($annotationData)
                                                         {
                                                             return $annotationData;

                                                         },
                                                         function($data) {}
        );
        $this->assertTrue(AnnotationCache::has(Annotation::TARGET_CLASS, 'foo', 'bar'));
    }

    /**
     * clean up test environment
     *
     * @deprecated  since 3.1.0, will be removed with 4.0.0
     */
    public function tearDown()
    {
        AnnotationCache::stop();
    }

    /**
     * @test
     * @since  3.4.0
     */
    public function bindCurrentWorkingDirectory()
    {
        $binder = new Binder();
        $module = AppUsingBindingModule::getBindCurrentWorkingDirectoryModule();
        $module($binder);
        $this->assertTrue($binder->hasConstant('net.stubbles.cwd'));
    }

    /**
     * @test
     * @since  3.4.0
     */
    public function bindHostname()
    {
        $binder = new Binder();
        $module = AppUsingBindingModule::getBindHostnameModule();
        $module($binder);
        $this->assertTrue($binder->hasConstant('net.stubbles.hostname.nq'));
        $this->assertTrue($binder->hasConstant('net.stubbles.hostname.fq'));
    }
}
