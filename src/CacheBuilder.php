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

use Subsession\Cache\Internal\Files\FileManager;

/**
 * Internal class responsible for handling cache files
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
    /**
     * FileManager instance
     *
     * @access private
     * @var    FileManager
     */
    private $fileManager;

    /**
     * Directory for cache files
     *
     * @access private
     * @var    string
     */
    private static $path;

    /**
     * Internal array of Cache keys and their CacheConfiguration
     *
     * @static
     * @access private
     * @var    array
     */
    private static $instances = [];

    /**
     * Default directory for cache files
     *
     * @access public
     * @var    string
     */
    const DEFAULT_PATH = "cache/";

    /**
     * DateTime format used for expire time of cache files
     *
     * @access public
     * @var    string
     */
    const DATETIME_FORMAT = "Y-m-d\TH:i:s.u";

    /**
     * Constructor defaults:
     *      $this->fileManager = new FileManager();
     *      $this->path = self::DEFAULT_PATH;
     */
    public function __construct()
    {
        $this->fileManager = new FileManager();
        $this->path = self::DEFAULT_PATH;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        foreach ($this as $key => $value) {
            unset($this->$key);
        }
    }

    /**
     * Get the cache directory
     *
     * @access public
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the cache directory
     *
     * @param string $path Cache directory
     *
     * @access public
     * @return CacheBuilder
     */
    public function setPath($path)
    {
        $this->path = $path;
        $this->fileManager->setPath($path);

        return $this;
    }

    /**
     * Create a new Cache instance
     *
     * @param string             $name          Name of the new Cache instance
     * @param CacheConfiguration $configuration Cache configuration
     *
     * @static
     * @access public
     * @return Cache
     */
    public static function create($name, CacheConfiguration $configuration)
    {
        $instance = new Cache($name);

        self::$instances[$name] = $configuration;
    }
}
