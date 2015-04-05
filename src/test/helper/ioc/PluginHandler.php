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
    private $configList;
    /**
     * map of config values
     *
     * @type  scalar[]
     */
    private $pluginList;
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
    private $configMap;
    /**
     * map of plugins
     *
     * @type  Plugin[]
     */
    private $pluginMap;

    /**
     * constructor
     *
     * @param  scalar[]                    $list1       list of config values
     * @param  scalar[]                    $configMap   map of config values
     * @param  stubbles\test\ioc\Plugin[]  $pluginList  list of plugins
     * @param  stubbles\test\ioc\Plugin[]  $pluginMap   map of plugins
     * @List{configList}('listConfig')
     * @Map{configMap}('mapConfig')
     * @List{pluginList}(stubbles\test\ioc\Plugin.class)
     * @Map{pluginMap}(stubbles\test\ioc\Plugin.class)
     * @Named{std}('foo')
     * @Named{answer}('foo')
     * @List{list}('aList')
     * @Map{map}('aMap')
     */
    public function __construct(
            array $configList,
            array $configMap,
            array $pluginList = null,
            array $pluginMap = null,
            Plugin $std = null,
            $answer = null,
            array $list = null,
            array $map = null)
    {
        $this->configList = $configList;
        $this->configMap  = $configMap;
        $this->pluginList = $pluginList;
        $this->pluginMap  = $pluginMap;
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
        return $this->configList;
    }

    /**
     * returns list of plugins
     *
     * @return  Plugin[]
     */
    public function getPluginList()
    {
        return $this->pluginList;
    }

    /**
     * returns map of config values
     *
     * @return  scalar[]
     */
    public function getConfigMap()
    {
        return $this->configMap;
    }

    /**
     * returns map of plugins
     *
     * @return  Plugin[]
     */
    public function getPluginMap()
    {
        return $this->pluginMap;
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
