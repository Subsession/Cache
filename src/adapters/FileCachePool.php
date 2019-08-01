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

namespace Comertis\Cache\Adapters;

use Comertis\Cache\Adapters\BaseCachePool;
use Comertis\Cache\CacheItem;
use Psr\Cache\CacheItemInterface;

/**
 * CacheItemPoolInterface generates CacheItemInterface objects.
 *
 * The primary purpose of Cache\CacheItemPoolInterface is to accept a key from
 * the Calling Library and return the associated Cache\CacheItemInterface object.
 * It is also the primary point of interaction with the entire cache collection.
 * All configuration and initialization of the Pool is left up to an
 * Implementing Library.
 *
 * @category Caching
 * @package  Comertis\Cache
 * @author   Cristian Moraru <cristian@comertis.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  Release: 1.0.0
 * @link     https://github.com/Comertis/Cache
 */
class FileCachePool extends BaseCachePool
{
    /**
     * Deferred items stack
     *
     * @var CacheItemInterface[]
     */
    private $_dStack;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_dStack = [];
    }

    /**
     * Destructor
     *
     * Commits any pending changes before destructing
     */
    public function __destruct()
    {
        $this->commit();
    }

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a
     *   \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem($key)
    {
        $this->assertValidKey($key);

        if (isset($this->_dStack[$key])) {
            return clone $this->_dStack[$key];
        }

        $file = @file_get_contents($this->_filenameFor($key));

        if (false !== $file) {
            return unserialize($file);
        }

        return new CacheItem($key);
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys An indexed array of keys of items to retrieve.
     *
     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a
     *   \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return array|\Traversable
     *   A traversable collection of Cache Items keyed by the cache keys of
     *   each item. A Cache item will be returned for each key, even if that
     *   key is not found. However, if no keys are specified then an empty
     *   traversable MUST be returned instead.
     *
     * @todo performance tune to fetch all keys at once from driver
     */
    public function getItems(array $keys = [])
    {
        $items = [];

        foreach ($keys as $key) {
            $this->assertValidKey($key);
            $items[$key] = $this->getItem($key);
        }

        return $items;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: Method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key The key for which to check existence.
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a
     *   \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return bool
     *   True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        $this->assertValidKey($key);

        $itemInDeferredNotExpired = isset($this->_dStack[$key]) &&
        $this->_dStack[$key]->isHit();

        return $itemInDeferredNotExpired || file_exists($this->_filenameFor($key));
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        $this->_dStack = [];

        $result = true;
        foreach (glob($this->getFolder() . '/' . '*') as $filename) {
            $result = $result && unlink($filename);
        }

        return $result;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key The key for which to delete
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a
     *   \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return bool
     *   True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        $this->assertValidKey($key);

        if (isset($this->_dStack[$key])) {
            unset($this->_dStack[$key]);
        }

        @unlink($this->_filenameFor($key));

        return true;
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys An array of keys that should be removed from the pool.
     *
     * @throws InvalidArgumentException
     *   If any of the keys in $keys are not a legal value a
     *   \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return bool
     *   True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys)
    {
        $result = true;

        foreach ($keys as $key) {
            $this->assertValidKey($key);
            $result = $result && $this->deleteItem($key);
        }

        return $result;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @return bool
     *   True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item)
    {
        if (!$item->isHit()) {
            return false;
        }

        $bytes = file_put_contents(
            $this->_filenameFor($item->getKey()),
            serialize($item)
        );

        return (false !== $bytes);
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or
     *   if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->_dStack[$item->getKey()] = $item;

        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool
     *   True if all not-yet-saved items were successfully
     *   saved or there were none. False otherwise.
     */
    public function commit()
    {
        $result = true;

        foreach ($this->_dStack as $key => $item) {
            $result = $result && $this->save($item);
            unset($this->_dStack[$key]);
        }

        return $result;
    }

    /**
     * Get filename for a given key
     *
     * @param string $key Cache item key
     *
     * @return string
     */
    private function _filenameFor($key)
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . $key;
    }
}