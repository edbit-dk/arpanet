<?php

namespace Lib;

class Cache
{
    protected static $path = 'cache';
    protected static $ttl = 3600; // Default TTL in seconds (1 hour)

    /**
     * Set the default TTL for the cache.
     *
     * @param  int  $ttl
     * @return void
     */
    public static function ttl($ttl)
    {
        self::$ttl = $ttl;
    }

    /**
     * Initialize the cache directory.
     *
     * @param  string  $cacheDirectory
     * @return void
     */
    public static function path($cacheDirectory)
    {
        self::$path = $cacheDirectory;

        // Ensure the cache directory exists
        if (!is_dir(self::$path)) {
            mkdir(self::$path, 0755, true);
        }
    }

    /**
     * Retrieve an item from the cache, or store it if it doesn't exist.
     *
     * @param  string  $key
     * @param  int|\Closure|null  $ttl
     * @param  \Closure|null  $callback
     * @return mixed
     */
    public static function remember($key, $ttl = null, \Closure $callback = null)
    {
        // If the second argument is a closure, it's the callback
        if ($callback === null && $ttl instanceof \Closure) {
            $callback = $ttl;
            $ttl = null; // Reset TTL to null
        }

        // Use the default TTL if none is provided
        $ttl = $ttl ?? self::$ttl;

        $cacheFile = self::getCacheFile($key);

        // Check if the cache file exists and is not expired
        if (self::cacheExists($cacheFile, $ttl)) {
            return self::getCache($cacheFile);
        }

        // Cache does not exist or is expired, run the callback
        $result = $callback();

        // Save the result to cache
        self::setCache($cacheFile, $result);

        return $result;
    }

    /**
     * Get the cache file path for the given key.
     *
     * @param  string  $key
     * @return string
     */
    protected static function getCacheFile($key)
    {
        return self::$path . '/' . md5($key) . '.cache';
    }

    /**
     * Check if the cache file exists and is not expired.
     *
     * @param  string  $cacheFile
     * @param  int  $ttl
     * @return bool
     */
    protected static function cacheExists($cacheFile, $ttl)
    {
        if (!file_exists($cacheFile)) {
            return false;
        }
    
        // Check if cache has expired
        $fileTime = filemtime($cacheFile);
        if ((time() - $fileTime) > $ttl) {
            // Cache has expired, delete it
            unlink($cacheFile); // Deletes the expired cache file
            return false; // Return false as the cache is expired
        }
    
        // Cache is valid
        return true;
    }

    /**
     * Get the cached data from a file.
     *
     * @param  string  $cacheFile
     * @return mixed
     */
    protected static function getCache($cacheFile)
    {
        $data = file_get_contents($cacheFile);
        return unserialize($data);
    }

    /**
     * Save data to the cache file.
     *
     * @param  string  $cacheFile
     * @param  mixed  $data
     * @return void
     */
    protected static function setCache($cacheFile, $data)
    {
        $serializedData = serialize($data);
        file_put_contents($cacheFile, $serializedData);
    }

    /**
     * Clear the cache file for the given key.
     *
     * @param  string  $key
     * @return void
     */
    public static function forget($key)
    {
        $cacheFile = self::getCacheFile($key);

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

     /**
     * Flush all cache files in the cache directory.
     *
     * @return void
     */
    public static function flush()
    {
        // Get all cache files in the cache directory
        $files = glob(self::$path . '/*.cache');

        // Delete each cache file
        foreach ($files as $file) {
            unlink($file);
        }
    }
}
