<?php

namespace Comertis\Cache;

use Comertis\Cache\CacheException;

/**
 * Undocumented class
 */
class Cache
{
    /**
     * Default save location for generated cache files
     *
     * @var string
     */
    private $_path;

    /**
     * Default file extension for generated cache files
     *
     * @var string
     */
    private $_extension;

    /**
     * Default expiration time for cache files in seconds
     *
     * @var int
     */
    private $_expireTime;

    private $_name;

    /**
     * Undocumented variable
     *
     * @var array
     */
    private $_blacklist;

    public function __construct()
    {
        $this->_path = "";
        $this->_extension = ".cache";
        $this->_name = "Cache";
        $this->_blacklist = [];
        $this->_expireTime = 6000;
    }

    /**
     * Get the default cache files path
     *
     * @access public
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Set the default path for generated cache files
     *
     * @param string $path
     * @access public
     * @throws CacheException
     * @return Cache
     */
    public function setPath($path)
    {
        if (empty($path)) {
            throw new CacheException("Path cannot be empty");
        }

        $this->_path = $path;

        return $this;
    }

    /**
     * Get the default extension for generated cache files
     *
     * @access public
     * @return string
     */
    public function getExtension()
    {
        return $this->_extension;
    }

    /**
     * Set the default extension for generated cache files
     *
     * @param string $extension
     * @access public
     * @throws CacheException
     * @return Cache
     */
    public function setExtension($extension)
    {
        if (empty($extension)) {
            throw new CacheException("Extension cannot be empty");
        }

        $this->_extension = $extension;

        return $this;
    }

    /**
     * Get the default expire time set for generated cache files in seconds
     *
     * @access public
     * @return int
     */
    public function getExpireTime()
    {
        return $this->_expireTime;
    }

    /**
     * Set the default expire time for generated cache files in seconds
     *
     * @param int $expireTime
     * @access public
     * @return Cache
     */
    public function setExpireTime($expireTime)
    {
        $this->_expireTime = $expireTime;

        return $this;
    }
}
