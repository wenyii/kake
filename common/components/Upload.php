<?php

namespace common\components;

use yii;
use yii\base\Object;

/**
 * File upload
 *
 * @author    Leon <jiangxilee@gmail.com>
 * @copyright 2016-3-2 10:50:00
 */
class Upload extends Object
{

    /**
     * @var array Default config
     */
    private $_config = [
        'mimes' => [
            'text/xml',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/octet-stream',
            'application/vnd.ms-office',
            'application/vnd.ms-excel',
            'application/excel',
            'application/msexcel',
            'text/plain',
            'application/pdf',
            'image/png',
            'image/gif',
            'image/jpeg',
            'image/svg+xml',
            'application/zip',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/html'
        ],
        'suffix' => 'xml,xls,xlsx,txt,pdf,png,gif,jpeg,jpg,zip,rar,doc,docx,ppt,pptx',

        // 100-200*200-MAX
        'pic_sizes' => null,

        // KB
        'max_size' => 10240,
        'auto_sub' => true,

        // for create subdirectory, [0]-function name, method use array, [1]-param, params use array
        'sub_path' => [
            [
                'common\components\Helper',
                'createDeepPath'
            ]
        ],
        'root_path' => '/upload/',
        'keep_name' => false,

        // for rename file, [0]-function name, method use array, [1]-param, params use array
        'save_name' => [
            'uniqid'
        ],
        'save_suffix' => null,
        'replace' => false,
        'hash' => false
    ];

    /**
     * @var array Image default suffix
     */
    private $_picSuffix = [
        'gif',
        'jpg',
        'jpeg',
        'bmp',
        'png'
    ];

    /**
     * @var string Error message
     */
    private $_error = null;

    /**
     * @var array Error messages
     */
    private $_errors = [];

    /**
     * __constructor
     *
     * @access public
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config['config'])) {
            $this->_config = array_merge($this->_config, $config['config']);
        }

        $items = [
            'mimes',
            'suffix'
        ];
        foreach ($items as $val) {
            if (!empty($this->_config[$val]) && is_string($this->_config[$val])) {
                $this->_config[$val] = explode(',', $this->_config[$val]);
            }
        }

        if (!empty($config['picSuffix'])) {
            $this->_picSuffix = $config['picSuffix'];
        }

        $this->_config['root_path'] = Yii::$app->params['upload_path'];

        parent::__construct();
    }

    /**
     * Upload File
     *
     * @access public
     *
     * @param $files
     *
     * @return mixed - boolean or string
     */
    public function upload($files = [])
    {
        // no file
        if (empty($files)) {
            return 'no file upload';
        }

        // check root path
        if (!$this->_checkRootPath($this->_config['root_path'])) {
            return $this->_error;
        }

        // check file one by one
        $success = [];
        if (function_exists('finfo_open')) {
            $fInfo = finfo_open(FILEINFO_MIME_TYPE);
        }

        foreach ($files as $key => $file) {

            if ($file['error']) {
                $this->_error = 'upload error';
                continue;
            }

            $file['name'] = strip_tags($file['name']);

            if (!isset($file['key'])) {
                $file['key'] = $key;
            }

            // get suffix
            $file['suffix'] = strtolower(Helper::getSuffix($file['name']));

            // Get suffix by extend for FLASH upload
            if (isset($fInfo)) {
                $file['type'] = strtolower(finfo_file($fInfo, $file['tmp_name']));
            }

            // check file
            if (!$this->_check($file)) {
                $this->_errors[$key] = $this->_error;
                continue;
            }

            // Create hash
            if (!empty($this->_config['hash'])) {
                $file['md5'] = md5_file($file['tmp_name']);
                $file['sha1'] = sha1_file($file['tmp_name']);
            }

            // Create save name
            $saveName = $this->_getSaveName($file);
            if (false === $saveName) {
                $this->_errors[$key] = $this->_error;
                continue;
            }
            $file['save_name'] = $saveName;

            // Create sub directory
            $subPath = $this->_getSubPath();
            if (false === $subPath) {
                $this->_errors[$key] = $this->_error;
                continue;
            }
            $file['save_path'] = $subPath;

            // Check image
            if (in_array($file['suffix'], $this->_picSuffix)) {

                // Check sizes
                if ($this->_config['pic_sizes'] && !$this->_checkSizes($file['tmp_name'])) {
                    $this->_error = 'image sizes error';
                    $this->_errors[$key] = $this->_error;
                    continue;
                }

                // Check core
                $imgInfo = getimagesize($file['tmp_name']);
                if (empty($imgInfo) || ('gif' === $file['suffix'] && empty($imgInfo['bits']))) {
                    $this->_error = 'image illegal';
                    $this->_errors[$key] = $this->_error;
                    continue;
                }

                // Get width and height
                list($file['width'], $file['height']) = $imgInfo;
            }

            // Save file
            $file['file'] = $this->_save($file, $this->_config['replace']);
            if ($file['file']) {
                unset($file['error'], $file['tmp_name']);
                $success[$key] = $file;
            }
        }
        if (isset($fInfo)) {
            finfo_close($fInfo);
        }

        return empty($success) ? $this->_error : $success;
    }

    /**
     * Check The File
     *
     * @access private
     *
     * @param $file
     *
     * @return boolean
     */
    private function _check($file)
    {
        if (empty($file['name'])) {
            $this->_error = 'unknown upload error';

            return false;
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            $this->_error = 'file illegal';

            return false;
        }

        // Check size
        if (!$this->_checkSize($file['size'])) {
            $this->_error = 'file size error';

            return false;
        }

        // Check mime
        // All file mime is application/octet-stream by FALSE upload
        if (isset($file['type']) && !$this->_checkMime($file['type'])) {
            $this->_error = 'file mime un allow';

            return false;
        }

        // Check suffix
        if (!$this->_checkSuffix($file['suffix'])) {
            $this->_error = 'file suffix un allow';

            return false;
        }

        return true;
    }

    /**
     * Check size
     *
     * @access private
     *
     * @param $size
     *
     * @return boolean
     */
    private function _checkSize($size)
    {
        $size /= 1024; // B2KB

        return !($size > $this->_config['max_size']) || (0 == $this->_config['max_size']);
    }

    /**
     * Check mime
     *
     * @access private
     *
     * @param $mime
     *
     * @return boolean
     */
    private function _checkMime($mime)
    {
        return empty($this->_config['mimes']) ? true : in_array($mime, $this->_config['mimes']);
    }

    /**
     * Check suffix
     *
     * @access private
     *
     * @param $suffix
     *
     * @return boolean
     */
    private function _checkSuffix($suffix)
    {
        return empty($this->_config['suffix']) ? true : in_array($suffix, $this->_config['suffix']);
    }

    /**
     * Check sizes
     *
     * @access private
     *
     * @param $filePath
     *
     * @return boolean
     */
    private function _checkSizes($filePath)
    {
        list($width, $height) = getimagesize($filePath);
        list($ruleSizes['width'], $ruleSizes['height']) = explode('*', $this->_config['pic_sizes']);

        /**
         * Check pic width and height
         *
         * @param array  $ruleSizes
         * @param string $type
         *
         * @return boolean
         */
        $checkWidthAndHeight = function ($ruleSizes, $type) use ($width, $height) {

            if (false !== strpos($ruleSizes[$type], '-')) {

                list($tmp['min'], $tmp['max']) = explode('-', $ruleSizes[$type]);

                if ($tmp['max'] === 'MAX') {
                    $tmp['max'] = $$type;
                }

                if ($$type < $tmp['min'] || $$type > $tmp['max']) {
                    return false;
                }
            } else {
                if ($ruleSizes[$type] != $$type) {
                    return false;
                }
            }

            return true;
        };

        if (!$checkWidthAndHeight($ruleSizes, 'width')) {
            return false;
        }
        if (!$checkWidthAndHeight($ruleSizes, 'height')) {
            return false;
        }

        return true;
    }

    /**
     * Get save name
     *
     * @access private
     *
     * @param $file
     *
     * @return mixed
     */
    private function _getSaveName($file)
    {
        if ($this->_config['keep_name']) {
            return $file['name'];
        }

        $rule = $this->_config['save_name'];

        if (empty($rule)) {
            $saveName = substr(pathinfo('_' . $file['name'], PATHINFO_FILENAME), 1);
        } else {
            $saveName = $this->_createName($rule);
            if (empty($saveName)) {
                $this->_error = 'rename rule error';

                return false;
            }
        }

        $suffix = empty($this->_config['save_suffix']) ? $file['suffix'] : $this->_config['save_suffix'];
        if (empty($suffix)) {
            return $saveName;
        }

        return $saveName . '.' . $suffix;
    }

    /**
     * Get sub directory name
     *
     * @access private
     * @return string
     */
    private function _getSubPath()
    {
        $subPath = null;
        $rule = $this->_config['sub_path'];

        if (!$this->_config['auto_sub'] || empty($rule)) {
            return $subPath;
        }

        $subPath = $this->_createName($rule);
        if (!empty($subPath) && !Helper::createDirectory($this->_config['root_path'] . $subPath)) {
            $this->_error = 'create directory fail';

            return false;
        }

        return $subPath;
    }

    /**
     * Create name by rule
     *
     * @access private
     *
     * @param $rule
     *
     * @return string
     */
    private function _createName($rule)
    {
        $name = null;

        if (is_array($rule)) {
            if (!isset($rule[1])) {
                $rule[] = null;
            }
            list($fn, $params) = $rule;
            $params = (array) $params;
        } else {
            $fn = $rule;
            $params = [];
        }

        // fn is function
        if (is_array($fn) || function_exists($fn)) {
            $name = call_user_func_array($fn, $params);
        } else {
            $name = call_user_func_array([
                $this,
                $fn
            ], $params);
        }

        return $name;
    }

    /**
     * Check directory
     *
     * @access private
     *
     * @param $rootPath
     *
     * @return boolean
     */
    private function _checkRootPath($rootPath)
    {
        if (!is_dir($rootPath) && !Helper::createDirectory($rootPath)) {
            $this->_error = 'create directory fail';

            return false;
        } else {
            $this->_config['root_path'] = $rootPath . DIRECTORY_SEPARATOR;

            return true;
        }
    }

    /**
     * Save file
     *
     * @access private
     *
     * @param $file
     * @param $replace
     *
     * @return mixed - boolean or string
     */
    private function _save($file, $replace = true)
    {
        $savePath = rtrim($this->_config['root_path'] . $file['save_path'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $fileName = $savePath . $file['save_name'];

        // replace file
        if (!$replace && is_file($fileName)) {
            $this->_error = 'file move fail, file exists';

            return false;
        }

        // move file
        if (!move_uploaded_file($file['tmp_name'], $fileName)) {
            $this->_error = 'file move error';

            return false;
        }

        return $fileName;
    }
}