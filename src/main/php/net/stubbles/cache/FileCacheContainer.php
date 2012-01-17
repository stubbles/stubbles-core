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
/**
 * Cache container using files.
 */
class FileCacheContainer extends AbstractCacheContainer implements CacheContainer
{
    /**
     * the directory to store the cache files in
     *
     * @type  string
     */
    protected $cacheDirectory;
    /**
     * list of keys
     *
     * @type  array
     */
    protected $keys           = null;
    /**
     * size of cache entries
     *
     * @type  array
     */
    protected $size           = null;

    /**
     * constructor
     *
     * If the directory does exist it will be created with the given file mode.
     *
     * @param  CacheStrategy  $strategy        strategy regarding caching
     * @param  string         $cacheDirectory  where to store cache files
     * @param  int            $fileMode        rights for caching directory
     */
    public function __construct(CacheStrategy $strategy, $cacheDirectory, $fileMode = 0700)
    {
        if (file_exists($cacheDirectory) === false) {
            mkdir($cacheDirectory, $fileMode, true);
        }

        $this->strategy       = $strategy;
        $this->cacheDirectory = $cacheDirectory;
    }

    /**
     * puts date into the cache
     *
     * Returns amount of cached bytes or false if caching failed.
     *
     * @param   string    $key   key under which the data should be stored
     * @param   string    $data  data that should be cached
     * @return  int|bool
     */
    protected function doPut($key, $data)
    {
        $bytes = file_put_contents($this->getCacheFileName($key), $data);
        if (false === $bytes) {
            return false;
        }

        if (null !== $this->keys) {
            $this->keys[$key] = $key;
        }

        if (null !== $this->size) {
            $this->size[$key] = $bytes;
        }

        return $bytes;
    }

    /**
     * checks whether data is cached under the given key
     *
     * @param   string  $key
     * @return  bool
     */
    protected function doHas($key)
    {
        return file_exists($this->getCacheFileName($key));
    }

    /**
     * fetches data from the cache
     *
     * Returns null if no data is cached under the given key.
     *
     * @param   string  $key
     * @return  string
     */
    protected function doGet($key)
    {
        if ($this->doHas($key) == true) {
            return file_get_contents($this->getCacheFileName($key));
        }

        return null;
    }

    /**
     * returns the time in seconds how long the data associated with $key is cached
     *
     * @param   string  $key
     * @return  int
     */
    public function getLifeTime($key)
    {
        if ($this->doHas($key) == true) {
            return (time() - filemtime($this->getCacheFileName($key)));
        }

        return 0;
    }

    /**
     * returns the timestamp when data associated with $key is cached
     *
     * @param   string  $key
     * @return  int
     */
    public function getStoreTime($key)
    {
        if ($this->doHas($key) == true) {
            return filemtime($this->getCacheFileName($key));
        }

        return 0;
    }

    /**
     * returns the allocated space of the data associated with $key in bytes
     *
     * @param   string  $key
     * @return  int
     */
    protected function doGetSize($key)
    {
        if (null !== $this->size) {
            return $this->size[$key];
        }

        return filesize($this->getCacheFileName($key));
    }

    /**
     * returns the amount of bytes the cache data requires
     *
     * @return  int
     */
    public function getUsedSpace()
    {
        if (null === $this->size) {
            $this->size = array();
            $dirIt      = new \DirectoryIterator($this->cacheDirectory);
            foreach ($dirIt as $file) {
                if ($file->isDot() == true || $file->isDir() == true) {
                    continue;
                }

                $key              = str_replace('.cache', '', $file->getFilename());
                $this->size[$key] = filesize($this->cacheDirectory . DIRECTORY_SEPARATOR . $key . '.cache');
            }
        }

        return array_sum($this->size);
    }

    /**
     * returns a list of all keys that are stored in the cache
     *
     * @return  string[]
     */
    public function getKeys()
    {
        if (null === $this->keys) {
            $this->keys  = array();
            $dirIt = new \DirectoryIterator($this->cacheDirectory);
            foreach ($dirIt as $file) {
                if ($file->isDot() == true || $file->isDir() == true) {
                    continue;
                }

                $key = str_replace('.cache', '', $file->getFilename());
                if ($this->strategy->isExpired($this, $key) == true) {
                    continue;
                }

                $this->keys[$key] = $key;
            }
        } else {
            foreach ($this->keys as $key) {
                if ($this->strategy->isExpired($this, $key) == true) {
                    unset($this->keys[$key]);
                    if (null !== $this->size) {
                        unset($this->size[$key]);
                    }
                }
            }
        }

        return $this->keys;
    }

    /**
     * runs the garbage collection
     */
    protected function doGc()
    {
        $dirIt = new \DirectoryIterator($this->cacheDirectory);
        foreach ($dirIt as $file) {
            if ($file->isDot() == true || $file->isDir() == true) {
                continue;
            }

            $key = str_replace('.cache', '', $file->getFilename());
            if ($this->strategy->isExpired($this, $key) == true) {
                unlink($this->cacheDirectory . DIRECTORY_SEPARATOR . $key . '.cache');
                if (null !== $this->size) {
                    unset($this->size[$key]);
                }
            }
        }
    }

    /**
     * returns name of the cache file for cache entry with given key
     *
     * @param   string  $key
     * @return  string
     */
    protected function getCacheFileName($key)
    {
        return $this->cacheDirectory . DIRECTORY_SEPARATOR . str_replace(DIRECTORY_SEPARATOR, '', $key) . '.cache';
    }
}
?>