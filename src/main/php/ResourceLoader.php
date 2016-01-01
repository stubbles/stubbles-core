<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  stubbles
 */
namespace stubbles;
use stubbles\lang\exception\FileNotFoundException;
use stubbles\streams\file\FileInputStream;
/**
 * Class to load resources from arbitrary locations.
 *
 * @since  1.6.0
 * @Singleton
 */
class ResourceLoader
{
    /**
     * root path of application
     *
     * @type  \stubbles\Rootpath
     */
    private $rootpath;

    /**
     * constructor
     *
     * If no root path is given it tries to detect it automatically.
     *
     * @param  string|\stubbles\Rootpath  $rootpath  optional
     */
    public function __construct($rootpath = null)
    {
        $this->rootpath = Rootpath::castFrom($rootpath);
    }

    /**
     * opens an input stream to read resource contents
     *
     * Resource can either be a complete path to a resource or a local path. In
     * case it is a local path it is searched within the src/main/resources
     * of the current project.
     * It is not possible to open resources outside of the root path by
     * providing a complete path, a complete path must always lead to a resource
     * located within the root path.
     *
     * @param   string  $resource
     * @return  \stubbles\streams\InputStream
     * @since   4.0.0
     */
    public function open($resource)
    {
        return new FileInputStream($this->checkedPathFor($resource));
    }

    /**
     * loads resource contents
     *
     * Resource can either be a complete path to a resource or a local path. In
     * case it is a local path it is searched within the src/main/resources
     * of the current project.
     * It is not possible to load resources outside of the root path by
     * providing a complete path, a complete path must always lead to a resource
     * located within the root path.
     * In case no $loader is given the resource will be loaded with
     * file_get_contents(). The given loader must accept a path and return the
     * result from the load operation.
     *
     * @param   string    $resource
     * @param   callable  $loader    optional  code to load resource with, defaults to file_get_contents()
     * @return  mixed     result of call to $loader, or file contents if no loader specified
     * @since   4.0.0
     */
    public function load($resource, callable $loader = null)
    {
        $checkedPath = $this->checkedPathFor($resource);
        if (null == $loader) {
            return file_get_contents($checkedPath);
        }

        return $loader($checkedPath);
    }

    /**
     * completes path for given resource
     *
     * In case the complete path is outside of the root path an
     * IllegalArgumentException is thrown.
     *
     * @param   string  $resource
     * @return  string
     * @throws  \stubbles\lang\exception\FileNotFoundException
     * @throws  \InvalidArgumentException
     */
    private function checkedPathFor($resource)
    {
        $completePath = $this->completePath($resource);
        if (!file_exists($completePath)) {
            throw new FileNotFoundException($completePath);
        }

        if (!$this->rootpath->contains($completePath)) {
            throw new \InvalidArgumentException(
                    'Given resource "' . $resource
                    . '" located at "' . $completePath
                    . '" is not inside root path ' . $this->rootpath
            );
        }

        return $completePath;
    }

    /**
     * returns complete path for given resource
     *
     * @param   string  $resource
     * @return  string
     */
    private function completePath($resource)
    {
        if (substr($resource, 0, strlen($this->rootpath)) == $this->rootpath) {
            return $resource;
        }

        return $this->rootpath
                . DIRECTORY_SEPARATOR . 'src'
                . DIRECTORY_SEPARATOR . 'main'
                . DIRECTORY_SEPARATOR . 'resources'
                . DIRECTORY_SEPARATOR . $resource;
    }

    /**
     * returns a list of all available uris for a resource
     *
     * The returned list is sorted alphabetically, meaning that local resources
     * of the current project are always returned as first entry if they exist,
     * and all vendor resources after. Order of vendor resources is also in
     * alphabetical order of vendor/package names.
     *
     * @param   string  $resourceName  the resource to retrieve the uris for
     * @return  string[]
     * @since   4.0.0
     */
    public function availableResourceUris($resourceName)
    {
        $resourceUris = array_values(
                array_filter(
                        array_map(
                              function($sourcePath) use($resourceName)
                              {
                                  return str_replace('/src/main/php', '/src/main/resources', $sourcePath) . DIRECTORY_SEPARATOR . $resourceName;
                              },
                              $this->rootpath->sourcePathes()
                        ),
                        function($resourcePath)
                        {
                            return file_exists($resourcePath);
                        }
                )
        );
        sort($resourceUris);
        return $resourceUris;
    }
}
