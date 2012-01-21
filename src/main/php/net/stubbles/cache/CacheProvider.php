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
use net\stubbles\ioc\InjectionProvider;
use net\stubbles\lang\BaseObject;
/**
 * Provider for cache containers.
 */
class CacheProvider extends BaseObject implements InjectionProvider
{
    /**
     * default name
     */
    const DEFAULT_NAME        = '__default';
    /**
     * default cache strategy to be used
     *
     * @type  CacheStrategy
     */
    private $cacheStrategy;
    /**
     * directory to store cache files
     *
     * @type  string
     */
    private $cachePath;
    /**
     * list of available cache containers
     *
     * @type  CacheContainer[]
     */
    private $cacheContainer = array();
    /**
     * mode for new files and directories
     *
     * @type  int
     */
    private $fileMode       = 0700;

    /**
     * constructor
     *
     * Please make sure that the given directory does exist.
     *
     * @param  CacheStrategy  $strategy   strategy regarding caching
     * @param  string         $cachePath  where to store cache files
     * @Inject
     * @Named{cachePath}('net.stubbles.cache.path')
     */
    public function __construct(CacheStrategy $strategy, $cachePath)
    {
        $this->strategy  = $strategy;
        $this->cachePath = $cachePath;
    }

    /**
     * sets the mode for new files and directories
     *
     * @param   int  $fileMode
     * @return  CacheProvider
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
     * @return  CacheContainer
     */
    public function get($name = null)
    {
        $containerName = $this->getContainerName($name);
        if (!isset($this->cacheContainer[$containerName])) {
            $this->cacheContainer[$containerName] = new FileCacheContainer($this->strategy,
                                                                           $this->getCachePath($name),
                                                                           $this->fileMode
                                                    );
        }

        return $this->cacheContainer[$containerName]->gc();
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