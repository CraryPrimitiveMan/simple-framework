<?php

namespace sf\cache;

use sf\base\Component;

/**
 * CacheInterface.
 *
 * @author Harry Sun <sunguangjun@126.com>
 */
class FileCache extends Component implements CacheInterface
{
    /**
     * @var string the directory to store cache files.
     *             If not set, it will use the "cache" subdirectory under the application runtime path.
     */
    public $cachePath;

    /**
     * Builds a normalized cache key from a given key.
     *
     * @param mixed $key the key to be normalized
     *
     * @return string the generated cache key
     */
    public function buildKey($key)
    {
        if (!is_string($key)) {
            $key = json_encode($key);
        }

        return md5($key);
    }

    /**
     * Retrieves a value from cache with a specified key.
     *
     * @param mixed $key a key identifying the cached value.
     *
     * @return mixed the value stored in cache, false if the value is not in the cache, expired,
     *               or the dependency associated with the cached data has changed.
     */
    public function get($key)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath.$key;
        if (@filemtime($cacheFile) > time()) {
            return unserialize(@file_get_contents($cacheFile));
        } else {
            return false;
        }
    }

    /**
     * Checks whether a specified key exists in the cache.
     * This can be faster than getting the value from the cache if the data is big.
     * In case a cache does not support this feature natively, this method will try to simulate it
     * but has no performance improvement over getting it.
     * Note that this method does not check whether the dependency associated
     * with the cached data, if there is any, has changed. So a call to [[get]]
     * may return false while exists returns true.
     *
     * @param mixed $key a key identifying the cached value. This can be a simple string or
     *                   a complex data structure consisting of factors representing the key.
     *
     * @return bool true if a value exists in cache, false if the value is not in the cache or expired.
     */
    public function exists($key)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath.$key;

        return @filemtime($cacheFile) > time();
    }

    /**
     * Retrieves multiple values from cache with the specified keys.
     * Some caches (such as memcache, apc) allow retrieving multiple cached values at the same time,
     * which may improve the performance. In case a cache does not support this feature natively,
     * this method will try to simulate it.
     *
     * @param string[] $keys list of string keys identifying the cached values
     *
     * @return array list of cached values corresponding to the specified keys. The array
     *               is returned in terms of (key, value) pairs.
     *               If a value is not cached or expired, the corresponding array value will be false.
     */
    public function mget($keys)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }

        return $results;
    }

    /**
     * Stores a value identified by a key into cache.
     * If the cache already contains such a key, the existing value and
     * expiration time will be replaced with the new ones, respectively.
     *
     * @param mixed $key      a key identifying the value to be cached. This can be a simple string or
     *                        a complex data structure consisting of factors representing the key.
     * @param mixed $value    the value to be cached
     * @param int   $duration the number of seconds in which the cached value will expire. 0 means never expire.
     *
     * @return bool whether the value is successfully stored into cache
     */
    public function set($key, $value, $duration = 0)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath.$key;
        $value = serialize($value);
        if (@file_put_contents($cacheFile, $value, LOCK_EX) !== false) {
            if ($duration <= 0) {
                $duration = 31536000; // 1 year
            }

            return touch($cacheFile, $duration + time());
        } else {
            return false;
        }
    }

    /**
     * Stores multiple items in cache. Each item contains a value identified by a key.
     * If the cache already contains such a key, the existing value and
     * expiration time will be replaced with the new ones, respectively.
     *
     * @param array $items    the items to be cached, as key-value pairs.
     * @param int   $duration default number of seconds in which the cached values will expire. 0 means never expire.
     *
     * @return bool whether the items are successfully stored into cache
     */
    public function mset($items, $duration = 0)
    {
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if ($this->set($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * Stores a value identified by a key into cache if the cache does not contain this key.
     * Nothing will be done if the cache already contains the key.
     *
     * @param mixed $key      a key identifying the value to be cached. This can be a simple string or
     *                        a complex data structure consisting of factors representing the key.
     * @param mixed $value    the value to be cached
     * @param int   $duration the number of seconds in which the cached value will expire. 0 means never expire.
     *
     * @return bool whether the value is successfully stored into cache
     */
    public function add($key, $value, $duration = 0)
    {
        if (!$this->exists($key)) {
            return $this->set($key, $value, $duration);
        } else {
            return false;
        }
    }

    /**
     * Stores multiple items in cache. Each item contains a value identified by a key.
     * If the cache already contains such a key, the existing value and expiration time will be preserved.
     *
     * @param array $items    the items to be cached, as key-value pairs.
     * @param int   $duration default number of seconds in which the cached values will expire. 0 means never expire.
     *
     * @return bool whether the items are successfully stored into cache
     */
    public function madd($items, $duration = 0)
    {
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if ($this->add($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * Deletes a value with the specified key from cache.
     *
     * @param mixed $key a key identifying the value to be deleted from cache. This can be a simple string or
     *                   a complex data structure consisting of factors representing the key.
     *
     * @return bool if no error happens during deletion
     */
    public function delete($key)
    {
        $key = $this->buildKey($key);
        $cacheFile = $this->cachePath.$key;

        return unlink($cacheFile);
    }

    /**
     * Deletes all values from cache.
     * Be careful of performing this operation if the cache is shared among multiple applications.
     *
     * @return bool whether the flush operation was successful.
     */
    public function flush()
    {
        $dir = @dir($this->cachePath);

        while (($file = $dir->read()) !== false) {
            if ($file !== '.' && $file !== '..') {
                unlink($this->cachePath.$file);
            }
        }

        $dir->close();
    }
}
