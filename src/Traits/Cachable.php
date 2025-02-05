<?php

namespace Lib\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Cachable
{
    // Protected properties to be used by models and BaseModel
    protected static string $cachePath = __DIR__ . '/cache'; // Default cache path
    protected static int $cacheDuration = 3600; // Default cache duration (1 hour)

    public static function bootCachable()
    {
        // Hook into the Eloquent query builder to intercept and cache all queries
        static::addGlobalScope('cachable', function (Builder $builder) {
            // Apply cache for any query executed
            $builder->macro('applyCache', function (Builder $query) {
                return self::cacheQuery($query, fn() => $query->get());
            });
        });

        // Clear cache when model is created, updated, or deleted
        static::created(fn ($model) => $model->clearAllCache());
        static::updated(fn ($model) => $model->clearAllCache());
        static::deleted(fn ($model) => $model->clearAllCache());
    }

    // Cache query results
    protected static function cacheQuery(Builder $query, callable $callback)
    {
        $cacheKey = self::getCacheKey($query);
        $cachedData = self::getFromCache($cacheKey);

        // Return cached data if available
        if ($cachedData !== null) {
            return $cachedData;
        }

        // Cache the result
        $result = $callback();
        self::putInCache($cacheKey, $result);

        return $result;
    }

    // Getter & Setter for cachePath
    public static function setCachePath(string $path): void
    {
        static::$cachePath = $path;
    }

    public static function getCachePath(): string
    {
        return static::$cachePath;
    }

    // Getter & Setter for cacheDuration
    public static function setCacheDuration(int $duration): void
    {
        static::$cacheDuration = $duration;
    }

    public static function getCacheDuration(): int
    {
        return static::$cacheDuration;
    }

    // Generate the cache file path for each model
    protected static function getCacheFilePath(): string
    {
        $modelName = strtolower(class_basename(get_called_class()));
        $modelCachePath = self::getCachePath() . "/{$modelName}.cache";

        // Create cache directory if it doesn't exist
        if (!is_dir(self::getCachePath())) {
            mkdir(self::getCachePath(), 0777, true);
        }

        return $modelCachePath;
    }

    // Get all cached data from the cache file
    protected static function getAllFromCache(): array
    {
        $cacheFilePath = self::getCacheFilePath();
        return file_exists($cacheFilePath) ? unserialize(file_get_contents($cacheFilePath)) ?? [] : [];
    }

    // Get specific cache data by key
    protected static function getFromCache(string $cacheKey)
    {
        $allCacheData = self::getAllFromCache();
        return $allCacheData[$cacheKey] ?? null;
    }

    // Store data in the cache
    protected static function putInCache(string $cacheKey, $data): void
    {
        $allCacheData = self::getAllFromCache();
        $allCacheData[$cacheKey] = $data;
        file_put_contents(self::getCacheFilePath(), serialize($allCacheData));
    }

    // Generate a unique cache key based on the full query (including where() and bindings)
    protected static function getCacheKey(Builder $query): string
    {
        $sql = $query->toSql(); // The raw SQL query
        $bindings = json_encode($query->getBindings()); // The query bindings (parameters)

        // Generate a unique cache key based on the SQL query and its bindings
        return md5($sql . $bindings);
    }

    // Clear all cache for the model
    public static function clearAllCache(): void
    {
        file_put_contents(self::getCacheFilePath(), serialize([]));
    }
}