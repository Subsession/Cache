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

use DateTime;
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
class CacheItem implements CacheItemInterface
{
    /**
     * Cache item key
     *
     * @var string
     */
    private $key;

    /**
     * Cache item content
     *
     * @var mixed
     */
    private $value = null;

    /**
     * Content expire DateTime
     *
     * @var DateTimeInterface
     */
    private $expiresAt;

    /**
     * Flag indicating if the content is valid
     *
     * @var bool
     */
    private $isHit = false;

    /**
     * Item constructor.
     *
     * @param string $key Cache item key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string
     *   The key string for this cache item.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Retrieves the value of the item from the cache associated with
     * this object's key.
     *
     * The value returned must be identical to the value originally stored by set().
     *
     * If isHit() returns false, this method MUST return null. Note that null
     * is a legitimate cached value, so the isHit() method SHOULD be used to
     * differentiate between "null value was found" and "no value was found."
     *
     * @return mixed
     *   The value corresponding to this cache item's key, or null if not found.
     */
    public function get()
    {
        if ($this->isHit()) {
            return $this->value;
        }

        // expired
        return null;
    }

    /**
     * Confirms if the cache item lookup resulted in a cache hit.
     *
     * Note: This method MUST NOT have a race condition between calling isHit()
     * and calling get().
     *
     * @return bool
     *   True if the request resulted in a cache hit. False otherwise.
     */
    public function isHit()
    {
        if (!$this->isHit) {
            return false;
        }

        if (null === $this->expiresAt) {
            return true;
        }

        $now = new DateTime();

        return $now < $this->expiresAt;
    }

    /**
     * Sets the value represented by this cache item.
     *
     * The $value argument may be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * @param mixed $value The serializable value to be stored.
     *
     * @return static
     *   The invoked object.
     */
    public function set($value)
    {
        $this->isHit = true;
        $this->value = $value;

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param \DateTimeInterface $expiration The point in time after which the item
     *                                       MUST be considered expired.
     *
     * @return static
     *   The called object.
     */
    public function expiresAt($expiration)
    {
        $this->expiresAt = $expiration;

        return $this;
    }

    /**
     * Sets the expiration time for this cache item.
     *
     * @param int|\DateInterval $time The period of time from the present after
     *                                which the item MUST be considered expired.
     *
     * @return static
     *   The called object.
     */
    public function expiresAfter($time)
    {
        if ($time instanceof \DateInterval) {
            $this->expiresAt = (new DateTime())->add($time);
        } elseif (is_numeric($time)) {
            $this->expiresAt = (new DateTime())->add(
                new \DateInterval('PT' . $time . 'S')
            );
        } else {
            $this->expiresAt = null;
        }

        return $this;
    }
}
