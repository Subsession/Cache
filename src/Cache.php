<?php

class Cache
{
    const TTL_DEFAULT = 3600;

    private $_path = null;
    private $_ext = '.cache';
    private $_name = 'default';
    private $_blacklist = array();
    private $_key = '';
    public static $items = [];
    public $file;
    public $fileExists;

    public function __construct($params = null)
    {
        $this->_key = 'ATE31D7609CN4SP19GPCNCBNN8IP00XI';
        $this->_path = __DIR__ . '/cache/';

        if (is_array($params)) {
            if (isset($params['path'])) {
                $this->setPath($params['path']);
            }
            if (isset($params['name'])) {
                $this->setName($params['name']);
            }
            if (isset($params['ext'])) {
                $this->setExtension($params['ext']);
            }
            if (isset($params['blist'])) {
                $this->setBlacklist($params['blist']);
            }
            if (isset($params['key'])) {
                $this->setKey($params['key']);
            }
        } else if (is_string($params)) {
            $this->setName($params);
        }

        if ($this->_checkCacheDir()) {
            $this->file = $this->getFilePath();
            $this->fileExists = file_exists($this->file);
        };
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getExtension()
    {
        return $this->_ext;
    }

    public function getBlacklist()
    {
        return $this->_blacklist;
    }

    public function getKey()
    {
        return $this->_key;
    }

    public function setPath($path)
    {
        $this->_path = $path;

        return $this;
    }

    public function setName($name)
    {
        $this->_name = $name;

        return $this;
    }

    public function setExtension($ext)
    {
        $this->_ext = $ext;

        return $this;
    }

    public function setBlacklist($blist)
    {
        $this->_blacklist = $blist;

        return $this;
    }

    public function setKey($key)
    {
        $this->_key = $key;

        return $this;
    }

    public function exists($key)
    {
        if ($cache = $this->_loadFile()) {
            return isset($cache[$key]['data']) && !$this->expired($cache[$key]);
        }

        return false;
    }

    public function set($key, $data, $expires = self::TTL_DEFAULT)
    {
        $info = array(
            'data' => $this->_encrypt(serialize($data), $this->getKey()),
            'timestamp' => time(),
            'expires' => $expires,
        );

        $cache = $this->_loadFile();
        $cache[$key] = $info;
        if (file_put_contents($this->file, json_encode($cache))) {
            $this->fileExists = true;
            return true;
        }

        return false;
    }

    public function get($key, $timestamp = false)
    {
        $type = 'data';

        if ($timestamp) {
            $type = 'timestamp';
        }

        if ($type == 'data') {
            // si la información que requerimos es del tipo "data" y el $key existe en memoria, recogemos la info de memoria
            if ($data = $this->_getFromMemory($key, $type)) {
                return $data;
            }
        }

        $cache = $this->_loadFile();
        if (!isset($cache[$key][$type])) {
            return null;
        }

        if ($type == 'data') {
            $data = unserialize($this->_decrypt($cache[$key][$type], $this->getKey()));
            $cache[$key][$type] = $data; // en memoria guardaremos el campo "data" ya desencriptado
            $this->_setInMemory($key, $cache[$key]); // guardamos la info de $key en memoria

            return $data;
        }

        return $cache[$key][$type];
    }

    public function getAll($unserialize = true)
    {
        $cache = $this->_loadFile();

        if ($unserialize) {
            $results = array();
            foreach ($cache as $k => $v) {
                $results[$k] = unserialize($this->_decrypt($v['data'], $this->getKey()));
            }
            return $results;
        }

        return $cache;
    }

    public function delete($key)
    {
        $cache = $this->_loadFile();

        if (isset($cache[$key])) {
            unset($cache[$key]);
            $cache = json_encode($cache);
            if (file_put_contents($this->file, $cache)) {
                return true;
            }
        }

        return false;
    }

    public function deleteExpired()
    {
        $deleted = 0;

        $cache = $this->_loadFile();
        foreach ($cache as $key => $data) {
            if ($this->expired($data)) {
                unset($cache[$key]);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $cache = json_encode($cache);
            if (!file_put_contents($this->file, $cache)) {
                return false;
            }
        }

        return $deleted;
    }

    public function expired($data)
    {
        $expired = false;

        if (!is_array($data)) {
            $key = $data;
            if ($this->exists($key)) {
                $data = $this->get($key);
            } else {
                return $expired;
            }
        }

        if ($data['expires'] > 0) {
            $elapsedTime = time() - $data['timestamp'];
            if ($elapsedTime > $data['expires']) {
                $expired = true;
            }
        }

        return $expired;
    }

    public function getFilePath()
    {
        $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($this->getName()));

        return $this->getPath() . $this->_getHash($filename) . $this->getExtension();
    }

    public function generateKey($id, $params = '')
    {
        if (is_array($params)) {
            foreach ($this->getBlacklist() as $bl) {
                if (isset($params[$bl])) {
                    unset($params[$bl]);
                }
            }
            $params = serialize($params);
        }

        return sha1($id . '_' . $params);
    }

    private function _deleteFile()
    {
        if ($this->fileExists && unlink($this->file)) {
            return true;
        }

        return false;
    }

    private function _emptyFile()
    {
        if ($this->fileExists) {
            $handle = fopen($this->file, 'w');
            fclose($handle);
            return true;
        }

        return false;
    }

    private function _loadFile()
    {
        if ($this->fileExists) {
            if ($content = file_get_contents($this->file)) {
                return json_decode($content, true);
            }
        }

        return [];
    }

    private function _getHash($filename)
    {
        return sha1($filename);
    }

    private function _checkCacheDir()
    {
        if (!is_dir($this->getPath()) && !mkdir($this->getPath(), 0775, true)) {
            throw new Exception('Unable to create cache directory ' . $this->getPath());
        } elseif (!is_readable($this->getPath()) || !is_writable($this->getPath())) {
            if (!chmod($this->getPath(), 0775)) {
                throw new Exception($this->getPath() . ' must be readable and writeable');
            }
        }

        return true;
    }

    private function _existsInMemory($key)
    {
        return array_key_exists($key, self::$items);
    }

    private function _setInMemory($key, $data)
    {
        try {
            self::$items[$key] = $data;
        } catch (\Throwable $th) {
            // do nothing
        }
    }

    private function _getFromMemory($key, $type)
    {
        if ($this->_existsInMemory($key)) {
            if (!$this->expired(self::$items[$key])) {
                return self::$items[$key][$type];
            } else {
                unset(self::$items[$key]);
            }
        }

        return false;
    }

    private function _encrypt($content, $key)
    {
        if (!$content) {
            return false;
        }

        if (function_exists('openssl_encrypt') && version_compare(phpversion(), '5.3.3', '>=')) {
            $iv_size = openssl_cipher_iv_length('AES-128-CBC');
            $iv = substr(hash('sha256', MCRYPT_RAND), 0, $iv_size);
            $crypt_content = openssl_encrypt($content, 'AES-128-CBC', $key, 0, $iv);
        } elseif (function_exists('mcrypt_encrypt')) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $crypt_content = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $content, MCRYPT_MODE_ECB, $iv);
        } else {
            throw new Exception('Either Mcrypt or OpenSSL extension is required');
        }

        return trim(base64_encode($crypt_content)); //encode for cookie
    }

    private function _decrypt($content, $key)
    {
        if (!$content) {
            return false;
        }

        if (function_exists('openssl_decrypt') && version_compare(phpversion(), '5.3.3', '>=')) {
            $crypt_content = base64_decode($content); //decode cookie
            $iv_size = openssl_cipher_iv_length('AES-128-CBC');
            $iv = substr(hash('sha256', MCRYPT_RAND), 0, $iv_size);
            $content = openssl_decrypt($crypt_content, 'AES-128-CBC', $key, 0, $iv);
        } elseif (function_exists('mcrypt_decrypt')) {
            $crypt_content = base64_decode($content); //decode cookie
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $content = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, $crypt_content, MCRYPT_MODE_ECB, $iv);
        } else {
            throw new Exception('Either Mcrypt or OpenSSL extension is required');
        }

        return trim($content);
    }

    function cronControl($method, $caches = array())
    {
        foreach ($caches as $cache_name) {
            $cache = new CacheAdvanced($cache_name);

            switch ($method)
            {
                case 'reset':
                    $cache->_deleteFile();
                    break;

                case 'clean':
                    $cache->deleteExpired();
                    break;
            }
        }
    }

}
