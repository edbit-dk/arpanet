<?php

namespace Lib\Traits;

use Illuminate\Database\Eloquent\Model;

trait Cachable
{
    protected static $cacheDuration = 0; // Default duration (0 means forever)
    protected static $cachePath = 'storage/cache/'; // Default cache directory

    /**
     * Set cache duration for the query.
     * @param int $duration Duration in seconds (null for forever).
     * @return $this
     */
    public function cache($duration = null)
    {
        static::$cacheDuration = $duration ?? static::$cacheDuration;
        return $this;
    }

    /**
     * Set the cache path externally at the class level.
     * @param string $path The cache path to be set.
     * @return void
     */
    public static function setCachePath(string $path)
    {
        static::$cachePath = rtrim($path, '/') . '/'; // Ensure trailing slash
    }

    /**
     * Set the default cache duration for all models that use this trait.
     * @param int $duration Duration in seconds (0 for forever).
     * @return void
     */
    public static function setCacheDuration(int $duration)
    {
        static::$cacheDuration = $duration;
    }

    /**
     * Get the cache key for the model.
     * @return string Cache key.
     */
    public function getCacheKey(): string
    {
        return $this->getTable() . '-' . md5(serialize($this->attributes));
    }

    /**
     * Get the cache path for this model.
     * @return string Cache file path.
     */
    public function getCacheFilePath(): string
    {
        return static::$cachePath . $this->getCacheKey() . '.cache';
    }

    /**
     * Get the cache duration.
     * @return int|null Duration in seconds or null for forever.
     */
    public function getCacheDuration()
    {
        return static::$cacheDuration ?? 0; // Default to forever (0)
    }

    /**
     * Check if we have cache available.
     * @return bool True if cache file exists.
     */
    public function hasCache(): bool
    {
        return file_exists($this->getCacheFilePath());
    }

    /**
     * Retrieve the cached data from the cache file.
     * @return mixed Cached data.
     */
    public function getCache()
    {
        if ($this->hasCache()) {
            return unserialize(file_get_contents($this->getCacheFilePath()));
        }
        return null;
    }

    /**
     * Set cache data for the model.
     * @param mixed $data The data to be cached.
     */
    public function setCache($data)
    {
        file_put_contents($this->getCacheFilePath(), serialize($data));
    }

    /**
     * Handle the model's saving event (create, update, delete).
     * This will delete the cache when the model is updated.
     */
    public static function bootCachable()
    {
        static::saved(function ($model) {
            // Delete the cache when the model is created or updated.
            if ($model instanceof Model) {
                $model->deleteCache();
            }
        });

        static::deleted(function ($model) {
            // Delete the cache when the model is deleted.
            if ($model instanceof Model) {
                $model->deleteCache();
            }
        });
    }

    /**
     * Delete the model's cache.
     */
    public function deleteCache()
    {
        if ($this->hasCache()) {
            unlink($this->getCacheFilePath());
        }
    }

    /**
     * Retrieve the model, either from the cache or the database.
     * @param callable $query The query callback (e.g., User::find(1)).
     * @return mixed The result of the query, either cached or from the database.
     */
    public function getFromCacheOrDb(callable $query)
    {
        if ($this->getCacheDuration() === 0 && $this->hasCache()) {
            return $this->getCache(); // Return cached data if it exists.
        }

        // Otherwise, perform the query and cache the result.
        $data = $query();
        $this->setCache($data);

        return $data;
    }
}
