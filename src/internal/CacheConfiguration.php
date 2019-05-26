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

namespace Comertis\Cache\Internal;

/**
 * Internal class responsible for handling cache configuration
 *
 * @category Caching
 * @package  Comertis\Cache
 * @author   Cristian Moraru <cristian@comertis.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  Release: 1.0.0
 * @link     https://github.com/Comertis/Cache
 */
class CacheConfiguration
{
    /**
     * Cache name
     *
     * @access private
     * @var    string
     */
    private $_name;

    /**
     * Default files extension for cache files
     *
     * @access private
     * @var    string
     */
    private $_extension;

    /**
     * Default time for cache files to expire
     *
     * @access private
     * @var    string
     */
    private $_expireTime;

    /**
     * Default extension for cache files
     *
     * @access public
     * @var    string
     */
    const DEFAULT_EXTENSION = ".cache";

    /**
     * Default name for cache
     *
     * @access public
     * @var    string
     */
    const DEFAULT_NAME = "default";

    /**
     * DateInterval value for 10 minutes
     *
     * @access public
     * @var    string
     */
    const DATEINTERVAL_10M = "PT10M";

    /**
     * Constructor defaults:
     *      $this->_extension = self::DEFAULT_EXTENSION;
     *      $this->_name = self::DEFAULT_NAME;
     *      $this->_expiteTime = (new \DateTime())
     *          ->add(new \DateInterval(self::DATEINTERVAL_10M))
     *          ->format(self::DATETIME_FORMAT);
     */
    public function __construct()
    {
        $this->_extension = self::DEFAULT_EXTENSION;
        $this->_name = self::DEFAULT_NAME;

        // Expire time is set to current DateTime + 10 minutes
        $this->_expireTime = (new \DateTime())
            ->add(new \DateInterval(self::DATEINTERVAL_10M))
            ->format(self::DATETIME_FORMAT);
    }

    /**
     * Get default cache files extensions
     *
     * @access public
     * @return string
     */
    public function getExtension()
    {
        return $this->_extension;
    }

    /**
     * Set default cache files extensions
     *
     * @param string $extension File extension
     *
     * @access public
     * @return CacheConfiguration
     */
    public function setExtension($extension)
    {
        $this->_extension = $extension;

        return $this;
    }

    /**
     * Get the cache name
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set the cache name
     *
     * @param string $name Name of the cache
     *
     * @access public
     * @return CacheConfiguration
     */
    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    /**
     * Get the cache expire time
     *
     * @access public
     * @return string|null null for infinite lifetime
     */
    public function getExpireTime()
    {
        return $this->_expireTime;
    }

    /**
     * Set the default cache expire time.
     * If null, cache never expres
     *
     * @param string|null $expireTime Expire time | null for infinite
     *
     * @access public
     * @return CacheConfiguration
     */
    public function setExpireTime($expireTime = null)
    {
        $this->_expireTime = $expireTime;

        return $this;
    }
}
