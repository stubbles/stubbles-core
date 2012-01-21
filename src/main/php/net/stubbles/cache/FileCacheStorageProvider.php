<?php
/**
 * This file is part of stubbles.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package  net\stubbles
 */
namespace net\stubbles\cache;
use net\stubbles\lang\BaseObject;
/**
 * Provider for file cache storages.
 */
class FileCacheStorageProvider extends BaseObject implements CacheStorageProvider
{
    /**
     * default name
     */
    const DEFAULT_NAME        = '__default';
    /**
     * directory to store cache files
     *
     * @type  string
     */
    private $cachePath;
    /**
     * list of available storages
     *
     * @type  CacheStorage[]
     */
    private $fileCacheStorage = array();
    /**
     * mode for new files and directories
     *
     * @type  int
     */
    private $fileMode         = 0700;

    /**
     * constructor
     *
     * Please make sure that the given directory does exist.
     *
     * @param  string  $cachePath  where to store cache files
     * @Inject
     * @Named('net.stubbles.cache.path')
     */
    public function __construct($cachePath)
    {
        $this->cachePath = $cachePath;
    }

    /**
     * sets the mode for new files and directories
     *
     * @param   int  $fileMode
     * @return  CacheStorageProvider
     * @Inject(optional=true)
     * @Named('net.stubbles.util.cache.filemode')
     */
    public function setFileMode($fileMode)
    {
        $this->fileMode = $fileMode;
        return $this;
    }

    /**
     * returns the requested cache container
     *
     * If no special cache container is requested or the cache container with
     * the requested name does not exist it will try to return the default
     * cache container.
     *
     * @param   string  $name  name of requested cache container
     * @return  CacheStorage
     */
    public function get($name = null)
    {
        $containerName = $this->getContainerName($name);
        if (!isset($this->fileCacheStorage[$containerName])) {
            $this->fileCacheStorage[$containerName] = new FileCacheStorage($this->getCachePath($name),
                                                                           $this->fileMode
                                                    );
        }

        return $this->fileCacheStorage[$containerName];
    }

    /**
     * returns name for container
     *
     * @param   string  $name
     * @return  string
     */
    private function getContainerName($name)
    {
        if (null == $name) {
            return self::DEFAULT_NAME;
        }

        return $name;
    }

    /**
     * returns cache path
     *
     * @param   string  $name
     * @return  string
     */
    private function getCachePath($name)
    {
        if (null == $name) {
            return $this->cachePath;
        }

        return $this->cachePath . DIRECTORY_SEPARATOR . $name;
    }
}
?>