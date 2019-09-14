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
 * @package  Subsession\Cache
 * @author   Cristian Moraru <cristian.moraru@live.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  GIT: &Id&
 * @link     https://github.com/Subsession/Cache
 */

namespace Subsession\Cache;

use Psr\Cache\CacheItemPoolInterface;
use Subsession\Cache\Adapters\APCuCachePool;
use Subsession\Cache\Adapters\FileCachePool;
use Subsession\Cache\Adapters\MemoryCachePool;
use Subsession\Cache\Exceptions\CacheException;

/**
 * Creates a cache pool instance base on user selection or tries
 * to guess the best one.
 *
 * @category Caching
 * @package  Subsession\Cache
 * @author   Cristian Moraru <cristian.moraru@live.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  Release: 1.0.0
 * @link     https://github.com/Subsession/Cache
 */
class CacheBuilder
{
    const MEMORY = 0;
    const FILE = 1;
    const APCU = 2;

    const DEFAULT_NAME = "DEFAULT";

    private static $instances = [];

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
            return self::autoDiscovery();
        }

        if (isset(self::$instances[$type][$name])) {
            return self::$instances[$type][$name];
        }

        $instance = null;

        switch ($type) {
            case self::MEMORY:
                $instance = new MemoryCachePool();
                break;

            case self::APCU:
                if (!self::isAPCuAvailable()) {
                    throw new CacheException("APCu is not available: not installed");
                }

                $instance = new APCuCachePool();
                break;

            case self::FILE:
                if (!self::isFilesWritable()) {
                    throw new CacheException("Temp dir is not writable");
                }

                $instance = new FileCachePool();
                break;

            default:
                throw new CacheException("Invalid cache pool type");
        }

        self::$instances[$type][$name] = $instance;

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
    private static function autoDiscovery()
    {
        if (self::isFilesWritable()) {
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
    private static function isFilesWritable()
    {
        return is_writable(sys_get_temp_dir());
    }

    /**
     * Check if APCu can be used
     *
     * @return bool
     */
    private static function isAPCuAvailable()
    {
        return (function_exists('apcu_fetch') || function_exists('apc_fetch'));
    }
}
