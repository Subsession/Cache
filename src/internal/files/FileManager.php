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

namespace Comertis\Cache\Internal\Files;

use Comertis\Cache\Exceptions\CacheException;
use Comertis\Cache\Internal\Files\CacheFile;
use Comertis\Cache\Internal\Files\FileReader;
use Comertis\Cache\Internal\Files\FileWriter;

/**
 * Responsible for handling CacheFile writes/reads/creation
 *
 * @category Caching
 * @package  Comertis\Cache
 * @author   Cristian Moraru <cristian@comertis.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  Release: 1.0.0
 * @link     https://github.com/Comertis/Cache
 */
class FileManager
{
    /**
     * Disk path for saving/reading cache files to/from
     *
     * @access private
     * @var    string
     */
    private $_path;

    /**
     * FileReader instante
     *
     * @access private
     * @var    FileReader
     */
    private $_fileReader;

    /**
     * FileWriter instance
     *
     * @access private
     * @var    FileWriter
     */
    private $_fileWriter;

    /**
     * Necessary permissions for path
     *
     * @var int
     */
    const PERMISSIONS = 0775;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_fileReader = new FileReader();
        $this->_fileWriter = new FileWriter();
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
     * Get the folder path where cache files are being saved to
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Set the default folder path for saving CacheFiles
     *
     * @param string $path Folder path
     *
     * @access public
     * @return FileManager
     */
    public function setPath($path)
    {
        $this->_path = $path;
        $this->_fileReader->setPath($path);
        $this->_fileWriter->setPath($path);

        return $this;
    }

    /**
     * Save a cache file to disk
     *
     * @param CacheFile $cacheFile CacheFile to save to disk
     *
     * @access public
     * @return boolean
     */
    public function save(CacheFile $cacheFile)
    {

    }

    /**
     * Checks that the configured cache directory can be used
     *
     * @access private
     * @return boolean
     */
    private function _checkCachePath()
    {
        if (!is_dir($this->getPath())) {
            $this->_createCachePath();
        }

        if (!$this->_checkCachePathPermission()) {
            return false;
        }

        return true;
    }

    /**
     * Created the cache directory if it doesn't exist
     *
     * @access private
     * @throws CacheException
     * @return boolean
     */
    private function _createCachePath()
    {
        $result = mkdir($this->getPath(), self::PERMISSIONS, true);

        if (!$result) {
            throw new CacheException("Failed to create cache path");
        }

        return $result;
    }
}
