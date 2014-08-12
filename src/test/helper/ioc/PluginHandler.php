<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles\test\ioc;
/**
 * Helper class for the test.
 */
class PluginHandler
{
    /**
     * list of config values
     *
     * @type  scalar[]
     */
    private $list1;
    /**
     * map of config values
     *
     * @type  scalar[]
     */
    private $list2;
    /**
     * some passed arguments
     *
     * @type  array
     */
    private $args;
    /**
     * list of plugins
     *
     * @type  Plugin[]
     */
    private $map1;
    /**
     * map of plugins
     *
     * @type  Plugin[]
     */
    private $map2;

    /**
     * constructor
     *
     * @param  scalar[]  $list1  list of config values
     * @param  scalar[]  $map1   map of config values
     * @Inject
     * @List{list1}('listConfig')
     * @Map{map1}('mapConfig')
     */
    public function __construct(array $list1, array $map1)
    {
        $this->list1 = $list1;
        $this->map1  = $map1;
    }

    /**
     * sets list of plugins
     *
     * @param  Plugin[]  $list2
     * @Inject(optional=true)
     * @List(stubbles\test\ioc\Plugin.class)
     */
    public function setPluginList(array $list2)
    {
        $this->list2 = $list2;
    }

    /**
     * sets map of plugins
     *
     * @param  Plugin[]  $map2
     * @Inject(optional=true)
     * @Map(stubbles\test\ioc\Plugin.class)
     */
    public function setPluginMap(array $map2)
    {
        $this->map2 = $map2;
    }

    /**
     * sets a bunch of values
     *
     * @param  Plugin[]  $map2
     * @Inject(optional=true)
     * @Named('foo')
     * @List{list}('aList')
     * @Map{map}('aMap')
     */
    public function setMoreStuff(Plugin $std, $answer, array $list, array $map)
    {
        $this->args = array('std'    => $std,
                            'answer' => $answer,
                            'list'   => $list,
                            'map'    => $map
                      );
    }

    /**
     * returns list of config values
     *
     * @return  scalar[]
     */
    public function getConfigList()
    {
        return $this->list1;
    }

    /**
     * returns list of plugins
     *
     * @return  Plugin[]
     */
    public function getPluginList()
    {
        return $this->list2;
    }

    /**
     * returns map of config values
     *
     * @return  scalar[]
     */
    public function getConfigMap()
    {
        return $this->map1;
    }

    /**
     * returns map of plugins
     *
     * @return  Plugin[]
     */
    public function getPluginMap()
    {
        return $this->map2;
    }

    /**
     * returns bunch of values
     *
     * @return  array
     */
    public function getArgs()
    {
        return $this->args;
    }
}
