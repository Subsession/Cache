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

namespace Subsession\Cache\Adapters;

use Subsession\Cache\Adapters\BaseCachePool;
use Subsession\Cache\CacheItem;
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
 * @package  Subsession\Cache
 * @author   Cristian Moraru <cristian.moraru@live.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  Release: 1.0.0
 * @link     https://github.com/Subsession/Cache
 */
class MemoryCachePool extends BaseCachePool
{
    /**
     * Storage for items
     *
     * @var CacheItemInterface[]
     */
    private $stack;

    /**
     * Deferred storage for items
     *
     * @var CacheItemInterface[]
     */
    private $dStack;

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

        if (isset($this->stack[$key])) {
            return clone $this->stack[$key];
        }

        if (isset($this->dStack[$key])) {
            return clone $this->dStack[$key];
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
     */
    public function getItems(array $keys = [])
    {
        $items = [];

        foreach ($keys as $key => $value) {
            $this->assertValidKey($value);
            $items[$value] = $this->getItem($value);
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
     *  True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        $this->assertValidKey($key);

        $itemInDeferredNotExpired = isset($this->dStack[$key]) &&
        $this->dStack[$key]->isHit();

        return (
            $itemInDeferredNotExpired ||
            (isset($this->stack[$key]) && $this->stack[$key]->isHit())
        );
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool
     *   True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        $this->stack = [];
        $this->dStack = [];

        return true;
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

        if (isset($this->stack[$key])) {
            unset($this->stack[$key]);
        }

        if (isset($this->dStack[$key])) {
            unset($this->dStack[$key]);
        }

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

        foreach ($keys as $key => $value) {
            $this->assertValidKey($value);
            $result = $result && $this->deleteItem($value);
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

        $this->stack[$item->getKey()] = $item;

        return true;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item The cache item to save.
     *
     * @return bool
     *   False if the item could not be queued or if a commit
     *   was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->dStack[$item->getKey()] = $item;

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
        foreach ($this->dStack as $key => $item) {
            $this->save($item);
            unset($this->dStack[$key]);
        }

        return true;
    }
}
