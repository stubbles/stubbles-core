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
    protected $propertiesBindingModule;
    /**
     * project path used throughout the test
     *
     * @type  string
     */
    protected $projectPath;
    /**
     * injector instance
     *
     * @type  Injector
     */
    protected $injector;

    /**
     * set up test environment
     */
    public function setUp()
    {
        $root = vfsStream::setup('projects');
        vfsStream::newFile('myproject/config/config.ini')
                 ->withContent("net.stubbles.locale=\"de_DE\"
net.stubbles.number.decimals=4
net.stubbles.webapp.xml.serializeMode=true")
                 ->at($root);
        $this->projectPath             = vfsStream::url('projects');
        $this->propertiesBindingModule = PropertiesBindingModule::create(vfsStream::url('projects/myproject'));
        $this->injector                = new Injector();
    }

    /**
     * returns complete path
     *
     * @param   string  $project
     * @param   string  $part
     * @return  string
     */
    protected function getProjectPath($project, $part)
    {
        return $this->projectPath . DIRECTORY_SEPARATOR . $project . DIRECTORY_SEPARATOR . $part;
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
     * @param  string  $pathPath
     * @param  string  $constantName
     * @test
     * @dataProvider  getConstants
     */
    public function pathesShouldBeBoundAsConstant($pathPart, $constantName)
    {
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertTrue($this->injector->hasConstant($constantName));
        $this->assertEquals($this->getProjectPath('myproject', $pathPart),
                            $this->injector->getConstant($constantName)
        );
    }

    /**
     * @param  string  $pathPath
     * @param  string  $constantName
     * @test
     * @dataProvider  getConstants
     */
    public function commonPathesAreNotBoundIfCommonPathDoesNotExist($pathPart, $constantName)
    {
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertFalse($this->injector->hasConstant($constantName . '.common'));
    }

    /**
     * @param  string  $pathPath
     * @param  string  $constantName
     * @test
     * @dataProvider  getConstants
     */
    public function commonPathesAreBoundIfCommonPathDoesExist($pathPart, $constantName)
    {
        $this->propertiesBindingModule = $this->getMock('net\\stubbles\\ioc\\module\\PropertiesBindingModule',
                                                        array('realpath'),
                                                        array(vfsStream::url('projects/myproject'))
                                         );
        $this->propertiesBindingModule->expects(($this->once()))
                                      ->method('realpath')
                                      ->will($this->returnValue(vfsStream::url('projects/common')));
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertTrue($this->injector->hasConstant($constantName . '.common'));
        $this->assertEquals($this->getProjectPath('common', $pathPart),
                            $this->injector->getConstant($constantName . '.common')
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
        $this->propertiesBindingModule = PropertiesBindingModule::create(vfsStream::url('projects/myproject'),
                                                                         array('user')
                                         );
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertTrue($this->injector->hasConstant($constantName));
        $this->assertEquals($this->getProjectPath('myproject', $pathPart),
                            $this->injector->getConstant($constantName)
        );
    }

    /**
     * @param  string  $pathPath
     * @param  string  $constantName
     * @test
     * @dataProvider  getWithAdditionalConstants
     */
    public function commonPathesContainsAdditionalPath($pathPart, $constantName)
    {
        $this->propertiesBindingModule = $this->getMock('net\\stubbles\\ioc\\module\\PropertiesBindingModule',
                                                        array('realpath'),
                                                        array(vfsStream::url('projects/myproject'),
                                                              array('user')
                                                        )
                                         );
        $this->propertiesBindingModule->expects(($this->once()))
                                      ->method('realpath')
                                      ->will($this->returnValue(vfsStream::url('projects/common')));
        $this->propertiesBindingModule->configure(new Binder($this->injector));
        $this->assertTrue($this->injector->hasConstant($constantName . '.common'));
        $this->assertEquals($this->getProjectPath('common', $pathPart),
                            $this->injector->getConstant($constantName . '.common')
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
}
?>