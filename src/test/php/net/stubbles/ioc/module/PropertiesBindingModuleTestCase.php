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
use org\bovigo\vfs\vfsStream;
/**
 * Test for net\stubbles\ioc\module\PropertiesBindingModule.
 *
 * @group  ioc
 * @group  ioc_module
 */
class PropertiesBindingModuleTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * instance to test
     *
     * @type  PropertiesBindingModule
     */
    private $propertiesBindingModule;
    /**
     * project path used throughout the test
     *
     * @type  string
     */
    private $projectPath;
    /**
     * injector instance
     *
     * @type  Injector
     */
    private $injector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $root = vfsStream::setup('projects');
        vfsStream::newFile('config/config.ini')
                 ->withContent("[config]
net.stubbles.locale=\"de_DE\"
net.stubbles.number.decimals=4
net.stubbles.webapp.xml.serializeMode=true")
                 ->at($root);
        $this->projectPath             = vfsStream::url('projects');
        $this->propertiesBindingModule = PropertiesBindingModule::create($this->projectPath);
        $this->injector                = new Injector();
    }

    /**
     * returns complete path
     *
     * @param   string  $part
     * @return  string
     */
    private function getProjectPath($part)
    {
        return $this->projectPath . DIRECTORY_SEPARATOR . $part;
    }

    /**
     * returns constant names and values
     *
     * @return  array
     */
    public function getConstants()
    {
        return array('cache'   => array('cache', 'net.stubbles.cache.path', ),
                     'config'  => array('config', 'net.stubbles.config.path'),
                     'data'    => array('data', 'net.stubbles.data.path'),
                     'docroot' => array('docroot', 'net.stubbles.docroot.path'),
                     'log'     => array('log', 'net.stubbles.log.path'),
                     'pages'   => array('pages', 'net.stubbles.pages.path')
        );
    }

    /**
     * @test
     */
    public function projectPathIsBound()
    {
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertTrue($this->injector->hasConstant('net.stubbles.project.path'));
        $this->assertEquals($this->projectPath,
                            $this->injector->getConstant('net.stubbles.project.path')
        );
    }

    /**
     * @param  string  $pathPath
     * @param  string  $constantName
     * @test
     * @dataProvider  getConstants
     */
    public function pathesShouldBeBoundAsConstant($pathPart, $constantName)
    {
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertTrue($this->injector->hasConstant($constantName));
        $this->assertEquals($this->getProjectPath($pathPart),
                            $this->injector->getConstant($constantName)
        );
    }

    /**
     * returns constant names and values
     *
     * @return  array
     */
    public function getWithAdditionalConstants()
    {
        return array_merge($this->getConstants(), array('user' => array('user', 'net.stubbles.user.path')));
    }

    /**
     * @param  string  $pathPath
     * @param  string  $constantName
     * @test
     * @dataProvider  getWithAdditionalConstants
     */
    public function additionalPathesShouldBeBound($pathPart, $constantName)
    {
        $this->propertiesBindingModule = PropertiesBindingModule::create(vfsStream::url('projects'),
                                                                         array('user')
                                         );
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertTrue($this->injector->hasConstant($constantName));
        $this->assertEquals($this->getProjectPath($pathPart),
                            $this->injector->getConstant($constantName)
        );
    }

    /**
     * @test
     */
    public function propertiesShouldBeAvailableAsInjections()
    {
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertTrue($this->injector->hasConstant('net.stubbles.locale'));
        $this->assertTrue($this->injector->hasConstant('net.stubbles.number.decimals'));
        $this->assertTrue($this->injector->hasConstant('net.stubbles.webapp.xml.serializeMode'));
        $this->assertEquals('de_DE', $this->injector->getConstant('net.stubbles.locale'));
        $this->assertEquals(4, $this->injector->getConstant('net.stubbles.number.decimals'));
        $this->assertEquals(true, (bool) $this->injector->getConstant('net.stubbles.webapp.xml.serializeMode'));
    }

    /**
     * @test
     */
    public function noPropertiesAvailableIfConfigFileDoesNotExist()
    {
        vfsStream::setup();
        $propertiesBindingModule = PropertiesBindingModule::create(vfsStream::url('root'));
        $injector                = new Injector();
        $propertiesBindingModule->configure(new Binder($injector));
        $this->assertFalse($this->injector->hasConstant('net.stubbles.locale'));
        $this->assertFalse($this->injector->hasConstant('net.stubbles.number.decimals'));
        $this->assertFalse($this->injector->hasConstant('net.stubbles.webapp.xml.serializeMode'));
    }

    /**
     * @since  2.0.0
     * @test
     * @group  issue_5
     */
    public function propertiesAvailableViaInstance()
    {
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertTrue($this->injector->hasExplicitBinding('net\\stubbles\\lang\\Properties', 'config'));
        $properties = $this->injector->getInstance('net\\stubbles\\lang\\Properties', 'config');
        /* @var  $properties  \net\stubbles\lang\Properties */
        $this->assertTrue($properties->hasValue('config', 'net.stubbles.locale'));
        $this->assertTrue($properties->hasValue('config', 'net.stubbles.number.decimals'));
        $this->assertTrue($properties->hasValue('config', 'net.stubbles.webapp.xml.serializeMode'));
        $this->assertEquals('de_DE', $properties->getValue('config', 'net.stubbles.locale'));
        $this->assertEquals(4, $properties->parseInt('config', 'net.stubbles.number.decimals'));
        $this->assertEquals(true, $properties->parseBool('config', 'net.stubbles.webapp.xml.serializeMode'));
    }

    /**
     * @since  2.0.0
     * @test
     * @group  issue_5
     */
    public function noPropertiesInstanceAvailableIfConfigFileDoesNotExist()
    {
        vfsStream::setup();
        $propertiesBindingModule = PropertiesBindingModule::create(vfsStream::url('root'));
        $injector                = new Injector();
        $propertiesBindingModule->configure(new Binder($injector));
        $this->assertFalse($this->injector->hasExplicitBinding('net\\stubbles\\lang\\Properties', 'config'));
    }
}
?>