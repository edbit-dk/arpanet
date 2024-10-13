<?php

namespace App\Providers;

class Session
{

    public $cacheExpire = null;
    public $cacheLimiter = null;

    public function start()
    {
        if (session_status() === PHP_SESSION_NONE) {

            if ($this->cacheLimiter !== null) {
                session_cache_limiter($this->cacheLimiter);
            }

            if ($this->cacheExpire !== null) {
                session_cache_expire($this->cacheExpire);
            }

            session_start();
        }
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function find(string $key)
    {
        if ($this->has($key)) {
            return $_SESSION[$key];
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Session
     */
    public function set(string $key, $value): Session
    {
        $_SESSION[$key] = $value;
        return $this;
    }

    public function remove(string $key): void
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }
    }

    public function clear(): void
    {
        session_unset();
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

}