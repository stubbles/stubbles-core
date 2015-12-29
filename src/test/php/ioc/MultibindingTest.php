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
use bovigo\callmap\NewInstance;
use stubbles\ioc\binding\ListBinding;
use stubbles\ioc\binding\MapBinding;
use stubbles\test\ioc\Plugin;
use stubbles\test\ioc\PluginHandler;

use function bovigo\assert\assert;
use function bovigo\assert\predicate\equals;
use function bovigo\assert\predicate\isFalse;
use function bovigo\assert\predicate\isTrue;
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
        assert($pluginHandler->getConfigList(), equals([303, 313, 323]));
    }

    /**
     * @test
     */
    public function injectorReturnsFalseForNonAddedListOnCheck()
    {
        $binder = new Binder();
        assert(
                $binder->getInjector()
                        ->hasBinding(ListBinding::TYPE, 'listConfig'),
                isFalse()
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
        assert(
                $binder->getInjector()
                        ->hasBinding(ListBinding::TYPE, 'listConfig'),
                isTrue()
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
        assert(
                $binder->getInjector()->getInstance(ListBinding::TYPE, 'listConfig'),
                equals([303, 313, 323])
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
        assert(
                $pluginHandler->getConfigList(),
                equals([303, 313, 323])
        );
    }

    /**
     * @test
     */
    public function injectorReturnsFalseForNonAddedMapOnCheck()
    {
        $binder = new Binder();
        assert(
                $binder->getInjector()->hasBinding(MapBinding::TYPE, 'mapConfig'),
                isFalse()
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
        assert(
                $binder->getInjector()->hasBinding(MapBinding::TYPE, 'mapConfig'),
                isTrue()
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
        assert(
                $binder->getInjector()->getInstance(MapBinding::TYPE, 'mapConfig'),
                equals(['tb' => 303, 'dd' => 313, 'hf' => 323])
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
        assert(
                $pluginHandler->getConfigMap(),
                equals(['tb' => 303, 'dd' => 313, 'hf' => 323])
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
        assert(
                $pluginHandler->getConfigMap(),
                equals(['tb' => 303, 'dd' => 313, 'hf' => 323])
        );
    }

    /**
     * @test
     */
    public function createTypedList()
    {
        $plugin1 = NewInstance::of(Plugin::class);
        $plugin2 = NewInstance::of(Plugin::class);
        $plugin3 = NewInstance::of(Plugin::class);
        $binder  = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList(Plugin::class)
                ->withValue($plugin1)
                ->withValueFromProvider($this->createProviderForValue($plugin2))
                ->withValueFromClosure(
                        function() use($plugin3) { return $plugin3; }
                );
        $pluginHandler = $this->createPluginHandler($binder);
        assert(
                $pluginHandler->getPluginList(),
                equals([$plugin1, $plugin2, $plugin3])
        );
    }

    /**
     * @test
     */
    public function bindTypedListMoreThanOnceAddsToSameList()
    {
        $plugin1 = NewInstance::of(Plugin::class);
        $plugin2 = NewInstance::of(Plugin::class);
        $plugin3 = NewInstance::of(Plugin::class);
        $binder  = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindList(Plugin::class)
                ->withValue($plugin1);
        $binder->bindList(Plugin::class)
                ->withValueFromProvider($this->createProviderForValue($plugin2));
        $binder->bindList(Plugin::class)
                ->withValueFromClosure(
                        function() use($plugin3) { return $plugin3; }
                );
        $pluginHandler = $this->createPluginHandler($binder);
        assert(
                $pluginHandler->getPluginList(),
                equals([$plugin1, $plugin2, $plugin3])
        );
    }

    /**
     * @test
     */
    public function createTypedMap()
    {
        $plugin1 = NewInstance::of(Plugin::class);
        $plugin2 = NewInstance::of(Plugin::class);
        $plugin3 = NewInstance::of(Plugin::class);
        $binder  = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap(Plugin::class)
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
        assert(
                $pluginHandler->getPluginMap(),
                equals(['tb' => $plugin1, 'dd' => $plugin2, 'hf' => $plugin3])
        );
    }

    /**
     * @test
     */
    public function bindTypedMapMoreThanOnceAddsToSameList()
    {
        $plugin1 = NewInstance::of(Plugin::class);
        $plugin2 = NewInstance::of(Plugin::class);
        $plugin3 = NewInstance::of(Plugin::class);
        $binder  = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bindMap(Plugin::class)
                ->withEntry('tb', $plugin1);
        $binder->bindMap(Plugin::class)
                ->withEntryFromProvider(
                        'dd',
                        $this->createProviderForValue($plugin2)
                );
        $binder->bindMap(Plugin::class)
                ->withEntryFromClosure(
                        'hf',
                        function() use($plugin3) { return $plugin3; }
                );
        $pluginHandler = $this->createPluginHandler($binder);
        assert(
                $pluginHandler->getPluginMap(),
                equals(['tb' => $plugin1, 'dd' => $plugin2, 'hf' => $plugin3])
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
        $binder->bindList(Plugin::class)->withValue(303);
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
        $binder->bindMap(Plugin::class)->withEntry('tb', 303);
        $this->createPluginHandler($binder);
    }

    /**
     * @test
     */
    public function mixedAnnotations()
    {
        $plugin = NewInstance::of(Plugin::class);
        $binder = new Binder();
        $binder->bindList('listConfig');
        $binder->bindMap('mapConfig');
        $binder->bind(Plugin::class)
               ->named('foo')
               ->toInstance($plugin);
        $binder->bindConstant('foo')
               ->to(42);
        $binder->bindList('aList')
               ->withValue(313);
        $binder->bindMap('aMap')
               ->withEntry('tb', 303);
        assert(
                $this->createPluginHandler($binder)->getArgs(),
                equals([
                        'std'    => $plugin,
                        'answer' => 42,
                        'list'   => [313],
                        'map'    => ['tb' => 303]
                ])
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
        return NewInstance::of(InjectionProvider::class)
                ->mapCalls(['get' => $value]);
    }

    /**
     * creates plugin handler instance
     *
     * @param   Binder  $binder
     * @return  PluginHandler
     */
    private function createPluginHandler(Binder $binder)
    {
        return $binder->getInjector()->getInstance(PluginHandler::class);
    }
}
