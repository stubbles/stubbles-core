<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\ioc;
use stubbles\test\ioc\PluginHandler;
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
               ->withValueFromProvider($this->getProviderForValue(313))
               ->withValueFromClosure(function() { return 323; });
        $binder->bindMap('mapConfig');
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals([303, 313, 323],
                            $pluginHandler->getConfigList()
        );
        $this->assertEquals([],
                            $pluginHandler->getConfigMap()
        );
    }

    /**
     * @test
     */
    public function injectorReturnsFalseForNonAddedListOnCheck()
    {
        $binder = new Binder();
        $this->assertFalse($binder->getInjector()->hasList('listConfig'));
    }

    /**
     * @test
     */
    public function injectorReturnsTrueForAddedListOnCheck()
    {
        $binder = new Binder();
        $binder->bindList('listConfig')
               ->withValue(303)
               ->withValueFromProvider($this->getProviderForValue(313))
               ->withValueFromClosure(function() { return 323; });
        $this->assertTrue($binder->getInjector()->hasList('listConfig'));
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function injectorRetrievesNonAddedListThrowsBindingException()
    {
        $binder = new Binder();
        $this->assertFalse($binder->getInjector()->getList('listConfig'));
    }

    /**
     * @test
     */
    public function injectorRetrievesAddedList()
    {
        $binder = new Binder();
        $binder->bindList('listConfig')
               ->withValue(303)
               ->withValueFromProvider($this->getProviderForValue(313))
               ->withValueFromClosure(function() { return 323; });
        $this->assertEquals([303, 313, 323],
                            $binder->getInjector()->getList('listConfig')
        );
    }

    /**
     * @test
     */
    public function bindListMoreThanOnceAddsToSameList()
    {
        $binder = new Binder();
        $binder->bindList('listConfig')
               ->withValue(303);
        $binder->bindList('listConfig')
               ->withValueFromProvider($this->getProviderForValue(313));
        $binder->bindList('listConfig')
               ->withValueFromClosure(function() { return 323; });
        $binder->bindMap('mapConfig');
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals([303, 313, 323],
                            $pluginHandler->getConfigList()
        );
        $this->assertEquals([],
                            $pluginHandler->getConfigMap()
        );
    }

    /**
     * @test
     */
    public function injectorReturnsFalseForNonAddedMapOnCheck()
    {
        $binder = new Binder();
        $this->assertFalse($binder->getInjector()->hasMap('mapConfig'));
    }

    /**
     * @test
     */
    public function injectorReturnsTrueForAddedMapOnCheck()
    {
        $binder = new Binder();
        $binder->bindMap('mapConfig')
               ->withEntry('tb', 303)
               ->withEntryFromProvider('dd', $this->getProviderForValue(313))
               ->withEntryFromClosure('hf', function() { return 323; });
        $this->assertTrue($binder->getInjector()->hasMap('mapConfig'));
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function injectorRetrievesNonAddedMapThrowsBindingException()
    {
        $binder = new Binder();
        $this->assertFalse($binder->getInjector()->getMap('mapConfig'));
    }

    /**
     * @test
     */
    public function injectorRetrievesAddedMap()
    {
        $binder = new Binder();
        $binder->bindMap('mapConfig')
               ->withEntry('tb', 303)
               ->withEntryFromProvider('dd', $this->getProviderForValue(313))
               ->withEntryFromClosure('hf', function() { return 323; });
        $this->assertEquals(['tb' => 303, 'dd' => 313, 'hf' => 323],
                            $binder->getInjector()->getMap('mapConfig')
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
               ->withEntryFromProvider('dd', $this->getProviderForValue(313))
               ->withEntryFromClosure('hf', function() { return 323; });
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals([],
                            $pluginHandler->getConfigList()
        );
        $this->assertEquals(['tb' => 303, 'dd' => 313, 'hf' => 323],
                            $pluginHandler->getConfigMap()
        );
    }

    /**
     * @test
     */
    public function bindMapMoreThanOnceAddsToSameMap()
    {
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig')
               ->withEntry('tb', 303);
        $binder->bindMap('mapConfig')
               ->withEntryFromProvider('dd', $this->getProviderForValue(313));
        $binder->bindMap('mapConfig')
               ->withEntryFromClosure('hf', function() { return 323; });
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals([],
                            $pluginHandler->getConfigList()
        );
        $this->assertEquals(['tb' => 303, 'dd' => 313, 'hf' => 323],
                            $pluginHandler->getConfigMap()
        );
    }

    /**
     * @test
     */
    public function createTypedList()
    {
        $mockPlugin1 = $this->getMock('stubbles\test\ioc\Plugin');
        $mockPlugin2 = $this->getMock('stubbles\test\ioc\Plugin');
        $mockPlugin3 = $this->getMock('stubbles\test\ioc\Plugin');
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList('stubbles\test\ioc\Plugin')
               ->withValue($mockPlugin1)
               ->withValueFromProvider($this->getProviderForValue($mockPlugin2))
               ->withValueFromClosure(function() use($mockPlugin3) { return $mockPlugin3; });
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals([$mockPlugin1, $mockPlugin2, $mockPlugin3],
                            $pluginHandler->getPluginList()
        );
    }

    /**
     * @test
     */
    public function bindTypedListMoreThanOnceAddsToSameList()
    {
        $mockPlugin1 = $this->getMock('stubbles\test\\ioc\Plugin');
        $mockPlugin2 = $this->getMock('stubbles\test\\ioc\Plugin');
        $mockPlugin3 = $this->getMock('stubbles\test\ioc\Plugin');
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList('stubbles\\test\\ioc\\Plugin')
               ->withValue($mockPlugin1);
        $binder->bindList('stubbles\\test\\ioc\\Plugin')
               ->withValueFromProvider($this->getProviderForValue($mockPlugin2));
        $binder->bindList('stubbles\test\ioc\Plugin')
               ->withValueFromClosure(function() use($mockPlugin3) { return $mockPlugin3; });
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals([$mockPlugin1, $mockPlugin2, $mockPlugin3],
                            $pluginHandler->getPluginList()
        );
    }

    /**
     * @test
     */
    public function createTypedMap()
    {
        $mockPlugin1 = $this->getMock('stubbles\test\ioc\Plugin');
        $mockPlugin2 = $this->getMock('stubbles\test\ioc\Plugin');
        $mockPlugin3 = $this->getMock('stubbles\test\ioc\Plugin');
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap('stubbles\test\ioc\Plugin')
               ->withEntry('tb', $mockPlugin1)
               ->withEntryFromProvider('dd', $this->getProviderForValue($mockPlugin2))
               ->withEntryFromClosure('hf', function() use($mockPlugin3) { return $mockPlugin3; });
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(['tb' => $mockPlugin1, 'dd' => $mockPlugin2, 'hf' => $mockPlugin3],
                            $pluginHandler->getPluginMap()
        );
    }

    /**
     * @test
     */
    public function bindTypedMapMoreThanOnceAddsToSameList()
    {
        $mockPlugin1 = $this->getMock('stubbles\test\ioc\Plugin');
        $mockPlugin2 = $this->getMock('stubbles\test\ioc\Plugin');
        $mockPlugin3 = $this->getMock('stubbles\test\ioc\Plugin');
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap('stubbles\\test\\ioc\\Plugin')
               ->withEntry('tb', $mockPlugin1);
        $binder->bindMap('stubbles\\test\\ioc\\Plugin')
               ->withEntryFromProvider('dd', $this->getProviderForValue($mockPlugin2));
        $binder->bindMap('stubbles\test\ioc\Plugin')
               ->withEntryFromClosure('hf', function() use($mockPlugin3) { return $mockPlugin3; });
        $pluginHandler = $this->createPluginHandler($binder);
        $this->assertEquals(['tb' => $mockPlugin1, 'dd' => $mockPlugin2, 'hf' => $mockPlugin3],
                            $pluginHandler->getPluginMap()
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function typedListWithInvalidValueThrowsBindingException()
    {
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList('stubbles\test\ioc\Plugin')
               ->withValue(303);
        $this->createPluginHandler($binder);
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function typedMapWithInvalidValueThrowsBindingException()
    {
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap('stubbles\test\ioc\Plugin')
               ->withEntry('tb', 303);
        $this->createPluginHandler($binder);
    }

    /**
     * @test
     */
    public function mixedAnnotations()
    {
        $mockPlugin = $this->getMock('stubbles\test\ioc\Plugin');
        $binder     = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bind('stubbles\test\ioc\Plugin')
               ->named('foo')
               ->toInstance($mockPlugin);
        $binder->bindConstant('foo')
               ->to(42);
        $binder->bindList('aList')
               ->withValue(313);
        $binder->bindMap('aMap')
               ->withEntry('tb', 303);
        $this->assertEquals(['std'    => $mockPlugin,
                             'answer' => 42,
                             'list'   => [313],
                             'map'    => ['tb' => 303]
                            ],
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
        $mockProvider = $this->getMock('stubbles\ioc\InjectionProvider');
        $mockProvider->expects($this->any())
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
        return $binder->getInjector()->getInstance('stubbles\test\ioc\PluginHandler');
    }
}
