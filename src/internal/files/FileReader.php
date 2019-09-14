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

use Subsession\Cache\Internal\Decoder;

/**
 * Undocumented class
 *
 * @category Caching
 * @package  Subsession\Cache
 * @author   Cristian Moraru <cristian.moraru@live.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @version  Release: 1.0.0
 * @link     https://github.com/Subsession/Cache
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
    private $path;

    /**
     * Data decoder
     *
     * @access private
     * @var    Subsession\Cache\Internal\Decoder
     */
    private $decoder;

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
            $this->checkPath();
        }

        $this->decoder = new Decoder();
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
        return $this->path;
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
        $this->path = $path;

        return $this;
    }

    /**
     * Check path to ensure it can be written to and
     * it has necessary permissions
     *
     * @access private
     * @return bool
     */
    private function checkPath()
    {
        if (!$this->isPathReadable()) {
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
    private function isPathReadable()
    {
        return is_readable($this->getPath());
    }
}
