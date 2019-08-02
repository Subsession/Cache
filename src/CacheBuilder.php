<?php
/**
 * PHP Version 7
 *
 * LICENSE:
 * Permission is hereby granted, free of charge, to any
 * person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall
 * be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @category Caching
 * @package  Comertis\Cache
 * @author   Cristian Moraru <cristian@comertis.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  GIT: &Id&
 * @link     https://github.com/Comertis/Cache
 */

namespace Comertis\Cache;

use Comertis\Cache\Adapters\APCuCachePool;
use Comertis\Cache\Adapters\FileCachePool;
use Comertis\Cache\Adapters\MemoryCachePool;
use Comertis\Cache\Exceptions\CacheException;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Creates a cache pool instance base on user selection or tries
 * to guess the best one.
 *
 * @category Caching
 * @package  Comertis\Cache
 * @author   Cristian Moraru <cristian@comertis.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  Release: 1.0.0
 * @link     https://github.com/Comertis/Cache
 */
class CacheBuilder
{
    const MEMORY = 0;
    const FILE = 1;
    const APCU = 2;

    const DEFAULT_NAME = "DEFAULT";

    private static $_instances = [];

    /**
     * Build a CacheItemPoolInterface
     *
     * @param null $type Cache type
     *
     * @static
     * @access public
     * @throws CacheException
     * @return CacheItemPoolInterface
     */
    public static function build($type = null, $name = self::DEFAULT_NAME)
    {
        if (null === $type) {
            return self::_autoDiscovery();
        }

        if (isset(self::$_instances[$type][$name])) {
            return self::$_instances[$type][$name];
        }

        $instance = null;

        switch ($type) {
            case self::MEMORY:
                $instance = new MemoryCachePool();

            case self::APCU:
                if (!self::_isAPCuAvailable()) {
                    throw new CacheException("APCu is not available: not installed");
                }

                $instance = new APCuCachePool();

            case self::FILE:
                if (!self::_isFilesWritable()) {
                    throw new CacheException("Temp dir is not writable");
                }

                $instance = new FileCachePool();

            default:
                throw new CacheException("Invalid cache pool type");
        }

        self::$_instances[$type][$name] = $instance;

        return $instance;
    }

    /**
     * Auto discover the best type of cache to use
     *
     * @throws CacheException
     *
     * @static
     * @access private
     * @return CacheItemPoolInterface
     */
    private static function _autoDiscovery()
    {
        if (self::_isFilesWritable()) {
            return new FileCachePool();
        }

        return new MemoryCachePool();
    }

    /**
     * Determine if the system temp directory is writable
     *
     * @static
     * @access private
     * @return bool
     */
    private static function _isFilesWritable()
    {
        return is_writable(sys_get_temp_dir());
    }

    /**
     * Check if APCu can be used
     *
     * @return bool
     */
    private static function _isAPCuAvailable()
    {
        return (function_exists('apcu_fetch') || function_exists('apc_fetch'));
    }
}
