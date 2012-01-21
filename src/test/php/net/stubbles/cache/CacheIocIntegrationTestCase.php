<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\cache;
use net\stubbles\ioc\Binder;
use org\bovigo\vfs\vfsStream;
/**
 * Tests for default bindings of net\stubbles\cache.
 *
 * @group  cache
 */
class CacheIocIntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * injector instance to create cache instance with
     *
     * @type  net\stubbles\ioc\Injector
     */
    protected $injector;
    /**
     * root cache directory
     *
     * @type  org\bovigo\vfs\vfsStreamDirectory
     */
    protected $root;
    /**
     * set up test environment
     */
    public function setUp()
    {
        $this->root = vfsStream::setup();
        $binder = new Binder();
        $binder->bindConstant()
               ->named('net.stubbles.cache.path')
               ->to(vfsStream::url('root'));
        $this->injector = $binder->getInjector();
    }

    /**
     * @test
     */
    public function cacheCanBeCreatedUsingDefaultBindings()
    {
        $this->assertInstanceOf('net\\stubbles\\cache\\Cache',
                                $this->injector->getInstance('net\\stubbles\\cache\\Cache')
        );
    }

    /**
     * @test
     */
    public function usesRootCachePathForDefaults()
    {
        $this->injector->getInstance('net\\stubbles\\cache\\Cache');
        $this->assertFalse($this->root->hasChildren());
    }

    /**
     * @test
     */
    public function createsSubCachePathIfRequestedWithName()
    {
        $this->injector->getInstance('net\\stubbles\\cache\\Cache', 'website');
        $this->assertTrue($this->root->hasChild('website'));
    }
}
?>