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
use org\stubbles\test\ioc\PluginHandler;
/**
 * Test for list and map bindings.
 *
 * @group  ioc
 */
class MultibindingTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createsList()
    {
        $binder = new Binder();
        $binder->bindList('listConfig')
               ->withValue(303)
               ->withValueFromProvider($this->getProviderForValue(313));
        $binder->bindMap('mapConfig');
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(array(303, 313),
                            $pluginHandler->getConfigList()
        );
        $this->assertEquals(array(),
                            $pluginHandler->getConfigMap()
        );
    }

    /**
     * @test
     */
    public function bindListTwiceAddsToSameList()
    {
        $binder = new Binder();
        $binder->bindList('listConfig')
               ->withValue(303);
        $binder->bindList('listConfig')
               ->withValueFromProvider($this->getProviderForValue(313));
        $binder->bindMap('mapConfig');
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(array(303, 313),
                            $pluginHandler->getConfigList()
        );
        $this->assertEquals(array(),
                            $pluginHandler->getConfigMap()
        );
    }

    /**
     * @test
     */
    public function createsMap()
    {
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig')
               ->withEntry('tb', 303)
               ->withEntryFromProvider('dd', $this->getProviderForValue(313));
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(array(),
                            $pluginHandler->getConfigList()
        );
        $this->assertEquals(array('tb' => 303, 'dd' => 313),
                            $pluginHandler->getConfigMap()
        );
    }

    /**
     * @test
     */
    public function bindMapTwiceAddsToSameMap()
    {
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig')
               ->withEntry('tb', 303);
        $binder->bindMap('mapConfig')
               ->withEntryFromProvider('dd', $this->getProviderForValue(313));
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(array(),
                            $pluginHandler->getConfigList()
        );
        $this->assertEquals(array('tb' => 303, 'dd' => 313),
                            $pluginHandler->getConfigMap()
        );
    }

    /**
     * @test
     */
    public function createTypedList()
    {
        $mockPlugin1 = $this->getMock('org\\stubbles\\test\\ioc\\Plugin');
        $mockPlugin2 = $this->getMock('org\\stubbles\\test\\ioc\\Plugin');
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList('org\\stubbles\\test\\ioc\\Plugin')
               ->withValue($mockPlugin1)
               ->withValueFromProvider($this->getProviderForValue($mockPlugin2));
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(array($mockPlugin1, $mockPlugin2),
                            $pluginHandler->getPluginList()
        );
    }

    /**
     * @test
     */
    public function bindTypedListTwiceAddsToSameList()
    {
        $mockPlugin1 = $this->getMock('org\\stubbles\\test\\ioc\\Plugin');
        $mockPlugin2 = $this->getMock('org\\stubbles\\test\\ioc\\Plugin');
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList('org\\stubbles\\test\\ioc\\Plugin')
               ->withValue($mockPlugin1);
        $binder->bindList('org\\stubbles\\test\\ioc\\Plugin')
               ->withValueFromProvider($this->getProviderForValue($mockPlugin2));
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(array($mockPlugin1, $mockPlugin2),
                            $pluginHandler->getPluginList()
        );
    }

    /**
     * @test
     */
    public function createTypedMap()
    {
        $mockPlugin1 = $this->getMock('org\\stubbles\\test\\ioc\\Plugin');
        $mockPlugin2 = $this->getMock('org\\stubbles\\test\\ioc\\Plugin');
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap('org\\stubbles\\test\\ioc\\Plugin')
               ->withEntry('tb', $mockPlugin1)
               ->withEntryFromProvider('dd', $this->getProviderForValue($mockPlugin2));
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(array('tb' => $mockPlugin1, 'dd' => $mockPlugin2),
                            $pluginHandler->getPluginMap()
        );
    }

    /**
     * @test
     */
    public function bindTypedMapTwiceAddsToSameList()
    {
        $mockPlugin1 = $this->getMock('org\\stubbles\\test\\ioc\\Plugin');
        $mockPlugin2 = $this->getMock('org\\stubbles\\test\\ioc\\Plugin');
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap('org\\stubbles\\test\\ioc\\Plugin')
               ->withEntry('tb', $mockPlugin1);
        $binder->bindMap('org\\stubbles\\test\\ioc\\Plugin')
               ->withEntryFromProvider('dd', $this->getProviderForValue($mockPlugin2));
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(array('tb' => $mockPlugin1, 'dd' => $mockPlugin2),
                            $pluginHandler->getPluginMap()
        );
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function typedListWithInvalidValueThrowsBindingException()
    {
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList('org\\stubbles\\test\\ioc\\Plugin')
               ->withValue(303);
        $this->createPluginHandler($binder);
    }

    /**
     * @test
     * @expectedException  net\stubbles\ioc\binding\BindingException
     */
    public function typedMapWithInvalidValueThrowsBindingException()
    {
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap('org\\stubbles\\test\\ioc\\Plugin')
               ->withEntry('tb', 303);
        $this->createPluginHandler($binder);
    }

    /**
     * @test
     */
    public function mixedAnnotations()
    {
        $mockPlugin = $this->getMock('org\\stubbles\\test\\ioc\\Plugin');
        $binder     = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bind('org\\stubbles\\test\\ioc\\Plugin')
               ->named('foo')
               ->toInstance($mockPlugin);
        $binder->bindConstant('foo')
               ->to(42);
        $binder->bindList('aList')
               ->withValue(313);
        $binder->bindMap('aMap')
               ->withEntry('tb', 303);
        $this->assertEquals(array('std'    => $mockPlugin,
                                  'answer' => 42,
                                  'list'   => array(313),
                                  'map'    => array('tb' => 303)
                            ),
                            $this->createPluginHandler($binder)
                                 ->getArgs()
        );
    }

    /**
     * creates mocked provider
     *
     * @param   mixed  $value
     * @return  InjectionProvider
     */
    private function getProviderForValue($value)
    {
        $mockProvider = $this->getMock('net\\stubbles\\ioc\\InjectionProvider');
        $mockProvider->expects($this->once())
                     ->method('get')
                     ->will($this->returnValue($value));
        return $mockProvider;
    }

    /**
     * creates plugin handler instance
     *
     * @param   Binder  $binder
     * @return  PluginHandler
     */
    private function createPluginHandler(Binder $binder)
    {
        return $binder->getInjector()->getInstance('org\\stubbles\\test\\ioc\\PluginHandler');
    }
}
?>