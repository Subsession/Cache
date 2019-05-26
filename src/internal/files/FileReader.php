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

use Comertis\Cache\Internal\Decoder;

/**
 * Undocumented class
 *
 * @category Caching
 * @package  Comertis\Cache
 * @author   Cristian Moraru <cristian@comertis.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  Release: 1.0.0
 * @link     https://github.com/Comertis/Cache
 */
class FileReader
{
    /**
     * Directory the FileReader is supposed
     * to read from
     *
     * @access private
     * @var    string
     */
    private $_path;

    /**
     * Data decoder
     *
     * @access private
     * @var    Comertis\Cache\Internal\Decoder
     */
    private $_decoder;

    /**
     * Necessary read permissions
     *
     * @access private
     * @var    integer
     */
    const PERMISSIONS = 0000;

    /**
     * Constructor
     *
     * @param string $path Directory the FileReader is supposed
     *                     to read from
     */
    public function __construct($path = null)
    {
        $this->setPath($path);

        if (!is_null($this->getPath())) {
            $this->_checkPath();
        }

        $this->_decoder = new Decoder();
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
     * Get the directory the FileWriter is supposed to
     * write to
     *
     * @access public
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Set the directory the FileWriter is supposed
     * to write to
     *
     * @param string $path Directory the FileWriter is supposed
     *                     to write to
     *
     * @access public
     * @return FileWriter
     */
    public function setPath($path)
    {
        $this->_path = $path;

        return $this;
    }

    /**
     * Check path to ensure it can be written to and
     * it has necessary permissions
     *
     * @access private
     * @return bool
     */
    private function _checkPath()
    {
        if (!$this->_isPathReadable()) {
            throw new FileWriterException("Path is not writable");
        }

        return true;
    }

    /**
     * Check of the path is writable
     *
     * @access private
     * @return bool
     */
    private function _isPathReadable()
    {
        return is_readable($this->getPath());
    }
}
