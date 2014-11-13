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
     * @List{list2}(stubbles\test\ioc\Plugin.class)
     * @Map{map2}(stubbles\test\ioc\Plugin.class)
     * @Named{std}('foo')
     * @Named{answer}('foo')
     * @List{list}('aList')
     * @Map{map}('aMap')
     */
    public function __construct(
            array $list1,
            array $map1,
            array $list2 = null,
            array $map2 = null,
            Plugin $std = null, $answer = null, array $list = null, array $map = null)
    {
        $this->list1 = $list1;
        $this->map1  = $map1;
        $this->list2 = $list2;
        $this->map2  = $map2;
        $this->args  = ['std'    => $std,
                        'answer' => $answer,
                        'list'   => $list,
                        'map'    => $map
                       ];
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
