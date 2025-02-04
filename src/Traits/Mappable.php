<?php

namespace Lib\Traits;

use Lib\Traits\AliasScope;

use Illuminate\Database\Eloquent\Builder;

trait Mappable
{
    /**
     * Cached field aliases.
     *
     * @var array
     */
    protected static $cachedMaps = [];

    /**
     * Get the field aliases (must be defined in the model).
     *
     * @return array
     */
    protected function maps()
    {
        return property_exists($this, 'maps') ? $this->maps : [];
    }

    public static function bootMappable()
    {
        static::addGlobalScope(new AliasScope());
    }

    /**
     * Handle dynamic property access (getter).
     *
     * @param string $key
     * @return mixed|null
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->maps)) {
            $actualField = $this->maps[$key];
            return $this->attributes[$actualField] ?? null;
        }

        return parent::__get($key);
    }

    /**
     * Handle dynamic property setting (mutator).
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (array_key_exists($key, $this->maps)) {
            $actualField = $this->maps[$key];
            $this->attributes[$actualField] = $value;
        } else {
            parent::__set($key, $value);
        }
    }

    /**
     * Resolve alias to actual field name.
     *
     * @param string $alias
     * @return string
     */
    public function map($alias)
    {
        return $this->maps()[$alias] ?? $alias;
    }

    /**
     * Get the actual field name for a given alias (Static Access).
     *
     * @param string $alias
     * @return string
     */
    public static function field($alias)
    {
        // Cache aliases in static property to avoid multiple model instantiations
        if (!isset(self::$cachedMaps[static::class])) {
            $instance = new static();
            self::$cachedMaps[static::class] = $instance->maps();
        }

        return self::$cachedMaps[static::class][$alias] ?? $alias;
        
    }

    public static function fields()
    {
        if (!isset(self::$cachedFieldAliases[static::class])) {
            $instance = new static();
            self::$cachedMaps[static::class] = $instance->maps();
        }

        return (object) self::$cachedMaps[static::class];
    }
}
