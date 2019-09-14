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
class CacheFile
{
    /**
     * Unique file key
     *
     * @access private
     * @var    string
     */
    private $key;

    /**
     * File creation date
     *
     * @access private
     * @var    string
     */
    private $creationDate;

    /**
     * File expire date
     *
     * @access private
     * @var    string
     */
    private $expireDate;

    /**
     * File data
     *
     * @access private
     * @var    mixed|string
     */
    private $data;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->creationDate = (new \DateTime())->format("dd/MM/yyyy HH:mm:ss");
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
     * Get the unique file key
     *
     * @access public
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the unique file key
     *
     * @param string $key Key
     *
     * @access public
     * @return CacheFile
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the file creation date
     *
     * @access public
     * @return string
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set the file creation date
     *
     * @param string $creationDate Creation date
     *
     * @access public
     * @return CacheFile
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get the file's expire date
     *
     * @access public
     * @return string
     */
    public function getExpireDate()
    {
        return $this->expireDate;
    }

    /**
     * Set the file's expire date
     *
     * @param string $expireDate Expire date
     *
     * @access public
     * @return CacheFile
     */
    public function setExpireDate($expireDate)
    {
        $this->expireDate = $expireDate;

        return $this;
    }

    /**
     * Get the file's data
     *
     * @access public
     * @return mixed|string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the file's data
     *
     * @param mixed|string $data Content
     *
     * @access public
     * @return CacheFile
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
