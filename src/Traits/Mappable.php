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
    public static function map(array $attributes)
    {
        $mappedAttributes = [];
        foreach ($attributes as $key => $value) {
            $mappedKey = static::field($key);
            $mappedAttributes[$mappedKey] = $value;
        }
        return $mappedAttributes;
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

        return self::$cachedMaps[static::class];
    }

     // **🔹 Handle property access dynamically**
    public function getAttribute($key)
    {
        $mappedKey = static::field($key);
        return parent::getAttribute($mappedKey);
    }
 
    public function setAttribute($key, $value)
    {
        $mappedKey = static::field($key);
        return parent::setAttribute($mappedKey, $value);
    }

    // ** Handle relation method calls dynamically**
    public function __call($method, $parameters)
    {
        // If the method is a relationship, apply alias mapping
        if (in_array($method, ['hasOne', 'hasMany', 'belongsTo', 'belongsToMany'])) {
            if (isset($parameters[1])) {
                $parameters[1] = static::field($parameters[1]); // Map foreign key alias
            }
            if (isset($parameters[2])) {
                $parameters[2] = static::field($parameters[2]); // Map local key alias
            }
        }

        return parent::__call($method, $parameters);
    }
}
