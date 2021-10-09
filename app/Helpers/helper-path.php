<?php

if (!function_exists('config_path')) {
    /**
     * Return the path to config files
     * @param null $path
     * @return string
     */
    function config_path($path = null)
    {
        return app()->getConfigurationPath(rtrim($path, ".php"));
    }
}

if (!function_exists('public_path')) {

    /**
     * Return the path to public dir
     * @param null $path
     * @return string
     */
    function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }
}

if (!function_exists('storage_path')) {

    /**
     * Return the path to storage dir
     * @param null $path
     * @return string
     */
    function storage_path($path = null)
    {
        return app()->storagePath($path);
    }
}

if (!function_exists('database_path')) {

    /**
     * Return the path to database dir
     * @param null $path
     * @return string
     */
    function database_path($path = null)
    {
        return app()->databasePath($path);
    }
}

if (!function_exists('bcrypt')) {
    /**
     * Hash the given value.
     *
     * @param  string $value
     * @param  array $options
     * @return string
     */
    function bcrypt($value, $options = array())
    {
        return app('hash')->make($value, $options);
    }
}

if (!function_exists('session')) {
    /**
     * Get / set the specified session value.
     *
     * If an array is passed as the key, we will assume you want to set an array of values.
     *
     * @param  array|string $key
     * @param  mixed $default
     * @return mixed
     */
    function session($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('session');
        }
        if (is_array($key)) {
            return app('session')->put($key);
        }
        return app('session')->get($key, $default);
    }
}


if (!function_exists('cookie')) {
    /**
     * Create a new cookie instance.
     *
     * @param  string $name
     * @param  string $value
     * @param  int $minutes
     * @param  string $path
     * @param  string $domain
     * @param  bool $secure
     * @param  bool $httpOnly
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    function cookie(
        $name = null,
        $value = null,
        $minutes = 0,
        $path = null,
        $domain = null,
        $secure = false,
        $httpOnly = true
    ) {
        $cookie = app('Illuminate\Contracts\Cookie\Factory');
        if (is_null($name)) {
            return $cookie;
        }
        return $cookie->make($name, $value, $minutes, $path, $domain, $secure, $httpOnly);
    }
}
