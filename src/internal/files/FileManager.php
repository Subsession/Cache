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

namespace Subsession\Cache\Internal\Files;

use Subsession\Cache\Exceptions\CacheException;
use Subsession\Cache\Internal\Files\CacheFile;
use Subsession\Cache\Internal\Files\FileReader;
use Subsession\Cache\Internal\Files\FileWriter;

/**
 * Responsible for handling CacheFile writes/reads/creation
 *
 * @category Caching
 * @package  Subsession\Cache
 * @author   Cristian Moraru <cristian.moraru@live.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  Release: 1.0.0
 * @link     https://github.com/Subsession/Cache
 */
class FileManager
{
    /**
     * Disk path for saving/reading cache files to/from
     *
     * @access private
     * @var    string
     */
    private $path;

    /**
     * FileReader instance
     *
     * @access private
     * @var    FileReader
     */
    private $fileReader;

    /**
     * FileWriter instance
     *
     * @access private
     * @var    FileWriter
     */
    private $fileWriter;

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
        $this->fileReader = new FileReader();
        $this->fileWriter = new FileWriter();
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
        return $this->path;
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
        $this->path = $path;
        $this->fileReader->setPath($path);
        $this->fileWriter->setPath($path);

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
        //
    }

    /**
     * Checks that the configured cache directory can be used
     *
     * @access private
     * @return boolean
     */
    private function checkCachePath()
    {
        if (!is_dir($this->getPath())) {
            $this->createCachePath();
        }

        if (!$this->checkCachePathPermission()) {
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
    private function createCachePath()
    {
        $result = mkdir($this->getPath(), self::PERMISSIONS, true);

        if (!$result) {
            throw new CacheException("Failed to create cache path");
        }

        return $result;
    }
}
