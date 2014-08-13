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
use stubbles\lang\reflect\BaseReflectionClass;
/**
 * Default injection provider.
 *
 * Creates objects and injects all dependencies via the default injector.
 *
 * @internal
 */
class DefaultInjectionProvider implements InjectionProvider
{
    /**
     * injector to use for dependencies
     *
     * @type  \stubbles\ioc\Injector
     */
    protected $injector;
    /**
     * concrete implementation to use
     *
     * @type  \stubbles\lang\reflect\BaseReflectionClass
     */
    protected $impl;

    /**
     * constructor
     *
     * @param  \stubbles\ioc\Injector                      $injector
     * @param  \stubbles\lang\reflect\BaseReflectionClass  $impl
     */
    public function __construct(Injector $injector, BaseReflectionClass $impl)
    {
        $this->injector = $injector;
        $this->impl     = $impl;
    }

    /**
     * returns the value to provide
     *
     * @param   string  $name
     * @return  mixed
     */
    public function get($name = null)
    {
        $instance = $this->createInstance();
        $this->injector->handleInjections($instance, $this->impl);
        return $instance;
    }

    /**
     * creates instance
     *
     * @return  mixed
     */
    private function createInstance()
    {
        $constructor = $this->impl->getConstructor();
        if (null === $constructor || !$constructor->hasAnnotation('Inject')) {
            return $this->impl->newInstance();
        }

        $params = $this->injector->getInjectionValuesForMethod($constructor, $this->impl);
        if (false === $params && $constructor->annotation('Inject')->isOptional()) {
            return $this->impl->newInstance();
        }

        return $this->impl->newInstanceArgs($params);
    }
}
