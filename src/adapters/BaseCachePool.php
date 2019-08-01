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

use Comertis\Cache\Exceptions\InvalidArgumentException;
use Psr\Cache\CacheItemPoolInterface;

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
abstract class BaseCachePool implements CacheItemPoolInterface
{
    const INVALID_KEY_PATTERN = "{}()/\@:";

    /**
     * Checks if a key is valid for APCu cache storage
     *
     * @param string $key Cache item key
     *
     * @throws InvalidArgumentException
     *   If the $key string is not a legal value a
     *   \Psr\Cache\InvalidArgumentException MUST be thrown.
     *
     * @return bool
     */
    protected function assertValidKey($key)
    {
        if (!is_string($key)) {
            throw new InvalidArgumentException("Invalid key");
        }

        if (preg_match(
            '/[' . preg_quote(self::INVALID_KEY_PATTERN, '/') . ']/',
            $key
        )
        ) {
            throw new InvalidArgumentException("Invalid key");
        }

        return true;
    }
}
