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
 * Cache container using files.
 *
 * @ProvidedBy(net\stubbles\cache\FileCacheStorageProvider.class)
 */
class FileCacheStorage extends BaseObject implements CacheStorage
{
    /**
     * directory to store the cache files in
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
     * switch whether directory contents have been read
     *
     * @type  bool
     */
    private $dataRead         = false;

    /**
     * constructor
     *
     * If the directory does exist it will be created with the given file mode.
     *
     * @param  string         $cacheDirectory  where to store cache files
     * @param  int            $fileMode        rights for caching directory
     */
    public function __construct($cacheDirectory, $fileMode = 0700)
    {
        if (!file_exists($cacheDirectory)) {
            mkdir($cacheDirectory, $fileMode, true);
        }

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
    public function put($key, $data)
    {
        $bytes = @file_put_contents($this->getCacheFileName($key), $data);
        if (false === $bytes) {
            return false;
        }

        $this->size[$key] = strlen($data);
        $this->keys[$key] = $key;
        return $bytes;
    }

    /**
     * checks whether data is cached under the given key
     *
     * @param   string  $key
     * @return  bool
     */
    public function has($key)
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
    public function get($key)
    {
        if ($this->has($key)) {
            return file_get_contents($this->getCacheFileName($key));
        }

        return null;
    }

    /**
     * removes data with that key from storage
     *
     * @param  string  $key
     */
    public function remove($key)
    {
        if ($this->has($key)) {
            unlink($this->getCacheFileName($key));
            unset($this->size[$key]);
            unset($this->keys[$key]);
        }
    }

    /**
     * returns the time in seconds how long the data associated with $key is cached
     *
     * @param   string  $key
     * @return  int
     */
    public function getLifeTime($key)
    {
        if ($this->has($key)) {
            return (time() - filemtime($this->getCacheFileName($key)));
        }

        return 0;
    }

    /**
     * returns the allocated space of the data associated with $key in bytes
     *
     * @param   string  $key
     * @return  int
     */
    public function getSize($key)
    {
        if ($this->has($key)) {
            return filesize($this->getCacheFileName($key));
        }

        return 0;
    }

    /**
     * returns the amount of bytes the cache data requires
     *
     * @return  int
     */
    public function getUsedSpace()
    {
        $this->readDirectoryData();
        return array_sum($this->size);
    }

    /**
     * returns a list of all keys that are stored in the cache
     *
     * @return  string[]
     */
    public function getKeys()
    {
        return $this->readDirectoryData()->keys;
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

    /**
     * reads directory data to retrieve all keys and size of entries
     *
     * @return  FileCacheStorage
     */
    protected function readDirectoryData()
    {
        if (true === $this->dataRead) {
            return $this;
        }

        $this->keys = array();
        $this->size = array();
        $dirIt = new \DirectoryIterator($this->cacheDirectory);
        foreach ($dirIt as $file) {
            if ($file->isDot() || $file->isDir()) {
                continue;
            }

            $key = str_replace('.cache', '', $file->getFilename());
            $this->size[$key] = $file->getSize();
            $this->keys[$key] = $key;
        }

        $this->dataRead = true;
        return $this;
    }
}
?>