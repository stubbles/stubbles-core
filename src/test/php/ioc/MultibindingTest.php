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
use stubbles\ioc\binding\ListBinding;
use stubbles\ioc\binding\MapBinding;
use stubbles\lang\reflect\NewInstance;
use stubbles\test\ioc\PluginHandler;
/**
 * Test for list and map bindings.
 *
 * @group  ioc
 */
class MultibindingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function createsList()
    {
        $binder = new Binder();
        $binder->bindList('listConfig')
                ->withValue(303)
                ->withValueFromProvider($this->createProviderForValue(313))
                ->withValueFromClosure(function() { return 323; });
        $binder->bindMap('mapConfig');
        $pluginHandler = $this->createPluginHandler($binder);
        assertEquals(
                [303, 313, 323],
                $pluginHandler->getConfigList()
        );
    }

    /**
     * @test
     */
    public function injectorReturnsFalseForNonAddedListOnCheck()
    {
        $binder = new Binder();
        assertFalse(
                $binder->getInjector()
                        ->hasBinding(ListBinding::TYPE, 'listConfig')
        );
    }

    /**
     * @test
     */
    public function injectorReturnsTrueForAddedListOnCheck()
    {
        $binder = new Binder();
        $binder->bindList('listConfig')
                ->withValue(303)
                ->withValueFromProvider($this->createProviderForValue(313))
                ->withValueFromClosure(function() { return 323; });
        assertTrue(
                $binder->getInjector()
                        ->hasBinding(ListBinding::TYPE, 'listConfig')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function injectorRetrievesNonAddedListThrowsBindingException()
    {
        $binder = new Binder();
        $binder->getInjector()->getInstance(ListBinding::TYPE, 'listConfig');
    }

    /**
     * @test
     */
    public function injectorRetrievesAddedList()
    {
        $binder = new Binder();
        $binder->bindList('listConfig')
                ->withValue(303)
                ->withValueFromProvider($this->createProviderForValue(313))
                ->withValueFromClosure(function() { return 323; });
        assertEquals(
                [303, 313, 323],
                $binder->getInjector()->getInstance(ListBinding::TYPE, 'listConfig')
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
                ->withValueFromProvider($this->createProviderForValue(313));
        $binder->bindList('listConfig')
                ->withValueFromClosure(function() { return 323; });
        $binder->bindMap('mapConfig');
        $pluginHandler = $this->createPluginHandler($binder);
        assertEquals(
                [303, 313, 323],
                $pluginHandler->getConfigList()
        );
    }

    /**
     * @test
     */
    public function injectorReturnsFalseForNonAddedMapOnCheck()
    {
        $binder = new Binder();
        assertFalse(
                $binder->getInjector()->hasBinding(MapBinding::TYPE, 'mapConfig')
        );
    }

    /**
     * @test
     */
    public function injectorReturnsTrueForAddedMapOnCheck()
    {
        $binder = new Binder();
        $binder->bindMap('mapConfig')
                ->withEntry('tb', 303)
                ->withEntryFromProvider('dd', $this->createProviderForValue(313))
                ->withEntryFromClosure('hf', function() { return 323; });
        assertTrue(
                $binder->getInjector()->hasBinding(MapBinding::TYPE, 'mapConfig')
        );
    }

    /**
     * @test
     * @expectedException  stubbles\ioc\binding\BindingException
     */
    public function injectorRetrievesNonAddedMapThrowsBindingException()
    {
        $binder = new Binder();
        $binder->getInjector()->getInstance(MapBinding::TYPE, 'mapConfig');
    }

    /**
     * @test
     */
    public function injectorRetrievesAddedMap()
    {
        $binder = new Binder();
        $binder->bindMap('mapConfig')
                ->withEntry('tb', 303)
                ->withEntryFromProvider('dd', $this->createProviderForValue(313))
                ->withEntryFromClosure('hf', function() { return 323; });
        assertEquals(
                ['tb' => 303, 'dd' => 313, 'hf' => 323],
                $binder->getInjector()->getInstance(MapBinding::TYPE, 'mapConfig')
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
                ->withEntryFromProvider('dd', $this->createProviderForValue(313))
                ->withEntryFromClosure('hf', function() { return 323; });
        $pluginHandler = $this->createPluginHandler($binder);
        assertEquals(
                ['tb' => 303, 'dd' => 313, 'hf' => 323],
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
                ->withEntryFromProvider('dd', $this->createProviderForValue(313));
        $binder->bindMap('mapConfig')
                ->withEntryFromClosure('hf', function() { return 323; });
        $pluginHandler = $this->createPluginHandler($binder);
        assertEquals(['tb' => 303, 'dd' => 313, 'hf' => 323],
                            $pluginHandler->getConfigMap()
        );
    }

    /**
     * @test
     */
    public function createTypedList()
    {
        $plugin1 = NewInstance::of('stubbles\test\ioc\Plugin');
        $plugin2 = NewInstance::of('stubbles\test\ioc\Plugin');
        $plugin3 = NewInstance::of('stubbles\test\ioc\Plugin');
        $binder  = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList('stubbles\test\ioc\Plugin')
                ->withValue($plugin1)
                ->withValueFromProvider($this->createProviderForValue($plugin2))
                ->withValueFromClosure(
                        function() use($plugin3) { return $plugin3; }
                );
        $pluginHandler = $this->createPluginHandler($binder);
        assertEquals(
                [$plugin1, $plugin2, $plugin3],
                $pluginHandler->getPluginList()
        );
    }

    /**
     * @test
     */
    public function bindTypedListMoreThanOnceAddsToSameList()
    {
        $plugin1 = NewInstance::of('stubbles\test\\ioc\Plugin');
        $plugin2 = NewInstance::of('stubbles\test\\ioc\Plugin');
        $plugin3 = NewInstance::of('stubbles\test\ioc\Plugin');
        $binder  = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList('stubbles\\test\\ioc\\Plugin')
                ->withValue($plugin1);
        $binder->bindList('stubbles\\test\\ioc\\Plugin')
                ->withValueFromProvider($this->createProviderForValue($plugin2));
        $binder->bindList('stubbles\test\ioc\Plugin')
                ->withValueFromClosure(
                        function() use($plugin3) { return $plugin3; }
                );
        $pluginHandler = $this->createPluginHandler($binder);
        assertEquals(
                [$plugin1, $plugin2, $plugin3],
                $pluginHandler->getPluginList()
        );
    }

    /**
     * @test
     */
    public function createTypedMap()
    {
        $plugin1 = NewInstance::of('stubbles\test\ioc\Plugin');
        $plugin2 = NewInstance::of('stubbles\test\ioc\Plugin');
        $plugin3 = NewInstance::of('stubbles\test\ioc\Plugin');
        $binder  = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap('stubbles\test\ioc\Plugin')
                ->withEntry('tb', $plugin1)
                ->withEntryFromProvider(
                        'dd',
                        $this->createProviderForValue($plugin2)
                )
                ->withEntryFromClosure(
                        'hf',
                        function() use($plugin3) { return $plugin3; }
                );
        $pluginHandler = $this->createPluginHandler($binder);
        assertEquals(
                ['tb' => $plugin1, 'dd' => $plugin2, 'hf' => $plugin3],
                $pluginHandler->getPluginMap()
        );
    }

    /**
     * @test
     */
    public function bindTypedMapMoreThanOnceAddsToSameList()
    {
        $plugin1 = NewInstance::of('stubbles\test\ioc\Plugin');
        $plugin2 = NewInstance::of('stubbles\test\ioc\Plugin');
        $plugin3 = NewInstance::of('stubbles\test\ioc\Plugin');
        $binder  = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap('stubbles\\test\\ioc\\Plugin')
                ->withEntry('tb', $plugin1);
        $binder->bindMap('stubbles\\test\\ioc\\Plugin')
                ->withEntryFromProvider(
                        'dd',
                        $this->createProviderForValue($plugin2)
                );
        $binder->bindMap('stubbles\test\ioc\Plugin')
                ->withEntryFromClosure(
                        'hf',
                        function() use($plugin3) { return $plugin3; }
                );
        $pluginHandler = $this->createPluginHandler($binder);
        assertEquals(
                ['tb' => $plugin1, 'dd' => $plugin2, 'hf' => $plugin3],
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
        $binder->bindList('stubbles\test\ioc\Plugin')->withValue(303);
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
        $binder->bindMap('stubbles\test\ioc\Plugin')->withEntry('tb', 303);
        $this->createPluginHandler($binder);
    }

    /**
     * @test
     */
    public function mixedAnnotations()
    {
        $plugin = NewInstance::of('stubbles\test\ioc\Plugin');
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bind('stubbles\test\ioc\Plugin')
               ->named('foo')
               ->toInstance($plugin);
        $binder->bindConstant('foo')
               ->to(42);
        $binder->bindList('aList')
               ->withValue(313);
        $binder->bindMap('aMap')
               ->withEntry('tb', 303);
        assertEquals(
                ['std'    => $plugin,
                 'answer' => 42,
                 'list'   => [313],
                 'map'    => ['tb' => 303]
                ],
                $this->createPluginHandler($binder)->getArgs()
        );
    }

    /**
     * creates mocked provider
     *
     * @param   mixed  $value
     * @return  \stubbles\ioc\InjectionProvider
     */
    private function createProviderForValue($value)
    {
        return NewInstance::of(
                'stubbles\ioc\InjectionProvider',
                ['get' => $value]
        );
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
