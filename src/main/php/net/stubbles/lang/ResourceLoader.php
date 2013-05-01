<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\lang;
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
     * @type  string
     */
    private static $rootPath;
    /**
     * list of source pathes
     *
     * @type  string[]
     */
    private static $sourcePathes;

    /**
     * return all uris for a resource
     *
     * @param   string  $resourceName  the resource to retrieve the uris for
     * @return  string[]
     */
    public function getResourceUris($resourceName)
    {
        $uris = array();
        foreach ($this->getSourcePathes() as $resourcePath) {
            if (file_exists($resourcePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $resourceName)) {
                $uris[] = realpath($resourcePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . $resourceName);
            }
        }

        return $uris;
    }

    /**
     * returns list of source pathes
     *
     * @return  string[]
     */
    private function getSourcePathes()
    {
        if (null === self::$sourcePathes) {
            $pathes = array();
            foreach ($this->getVendorPathes() as $path) {
                if (substr($path, -13) === '/src/main/php') {
                    $path = str_replace('/src/main/php', '/src/main', $path);
                }

                if (!isset($pathes[$path])) {
                    $pathes[$path] = $path;
                }
            }

            self::$sourcePathes = array_values($pathes);
        }

        return self::$sourcePathes;
    }

    /**
     * return list of vendor pathes
     *
     * @return  string[]
     */
    private function getVendorPathes()
    {
        return require self::getRootPath() . '/vendor/composer/autoload_namespaces.php';
    }

    /**
     * returns root path
     *
     * @return  string
     */
    public static function getRootPath()
    {
        if (null === self::$rootPath) {
            if (\Phar::running() !== '') {
                self::$rootPath = dirname(\Phar::running(false));
            } elseif (file_exists(__DIR__ . '/../../../../../../../../composer/autoload_namespaces.php')) {
                self::$rootPath = realpath(__DIR__ . '/../../../../../../../../../');
            } else {
                self::$rootPath = realpath(__DIR__ . '/../../../../../../');
            }
        }

        return self::$rootPath;
    }

    /**
     * returns root path for non-static mockable calls
     *
     * @return  string
     */
    public function getRoot()
    {
        return self::getRootPath();
    }
}
?>