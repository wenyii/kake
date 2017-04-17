<?php

namespace common\components;

use yii\base\Object;

/**
 * Download components
 *
 * @author    Leon <jiangxilee@gmail.com>
 * @copyright 2017-02-14 15:45:27
 */
class Download extends Object
{
    /**
     * @license optional init
     * @var integer control the speed
     */
    private $_speed = 128;

    /**
     * __constructor
     *
     * @access public
     *
     * @param integer $speed
     */
    public function __construct($speed = null)
    {
        $speed && $this->setSpeed($speed);

        parent::__construct();
    }

    /**
     * Download location file
     *
     * @access public
     *
     * @param string  $file
     * @param string  $name
     * @param boolean $reload Enabled reload
     *
     * @return mixed
     */
    public function download($file, $name = '', $reload = false)
    {
        if (!file_exists($file)) {
            return 'file not found';
        }
        if ($name == '') {
            $name = basename($file);
        }
        $fp = fopen($file, 'rb');
        $fileSize = filesize($file);
        $ranges = $this->_getRange($fileSize);
        header('cache-control:public');
        header('content-type:application/octet-stream');
        header('content-disposition:attachment; filename=' . $name);
        if ($reload && $ranges != null) { // use reload
            header('HTTP/1.1 206 Partial Content');
            header('Accept-Ranges:bytes');
            // surplus length
            header(sprintf('content-length:%u', $ranges['end'] - $ranges['start']));
            // range
            header(sprintf('content-range:bytes %s-%s/%s', $ranges['start'], $ranges['end'], $fileSize));
            // let the fp goto the prev reload address
            fseek($fp, sprintf('%u', $ranges['start']));
        } else {
            header('HTTP/1.1 200 OK');
            header('content-length:' . $fileSize);
        }
        while (!feof($fp)) {
            sleep(1);
            echo fread($fp, round($this->_speed * 1024, 0));
            ob_flush();
        }
        ($fp != null) && fclose($fp);

        return true;
    }

    /**
     * Download remote file
     *
     * @access public
     *
     * @param string $file Url of remote file
     * @param string $name Rename of file
     *
     * @return void
     */
    public function remoteDownload($file, $name = '')
    {
        if ($name == '') {
            $name = basename($file);
        }
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment; filename=' . $name);
        readfile($file);
    }

    /**
     * Set the speed of download
     *
     * @access public
     *
     * @param integer $speed KB
     *
     * @return void
     */
    public function setSpeed($speed)
    {
        if (is_int($speed) && $speed > 16 && $speed < 4096) {
            $this->_speed = $speed;
        }
    }

    /**
     * Get header range info
     *
     * @access private
     *
     * @param integer $fileSize
     *
     * @return mixed - array or NULL
     */
    private function _getRange($fileSize)
    {
        if (isset($_SERVER['HTTP_RANGE']) && !empty($_SERVER['HTTP_RANGE'])) {
            $range = $_SERVER['HTTP_RANGE'];
            $range = preg_replace('/[\s|,].*/', '', $range);
            $range = explode('-', substr($range, 6));
            if (count($range) < 2) {
                $range[1] = $fileSize;
            }
            $range = array_combine([
                'start',
                'end'
            ], $range);
            if (empty($range['start'])) {
                $range['start'] = 0;
            }
            if (empty($range['end'])) {
                $range['end'] = $fileSize;
            }

            return $range;
        }

        return null;
    }

    /**
     * Force download - support download the string
     *
     * @access public
     *
     * @param string $fileName File name
     * @param string $data     File content
     *
     * @return void
     */
    public function forceDownload($fileName = '', $data = '')
    {
        if ($fileName === '' or $data === '') {
            return;
        } elseif ($data === null) {
            if (@is_file($fileName) && ($fileSize = @filesize($fileName)) !== false) {
                $filePath = $fileName;
                $fileName = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $fileName));
                $fileName = end($fileName);
            } else {
                return;
            }
        } else {
            $fileSize = strlen($data);
        }
        // Set the default MIME type to send
        $mime = 'application/octet-stream';
        $x = explode('.', $fileName);
        $extension = end($x);
        /*
         * It was reported that browsers on Android 2.1 (and possibly older as well)
         * need to have the filename extension upper-cased in order to be able to
         * download it.
         *
         * Reference: http://digiblog.de/2011/04/19/android-and-the-download-file-headers/
         */
        if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT'])) {
            $x[count($x) - 1] = strtoupper($extension);
            $filename = implode('.', $x);
        }
        if ($data === null && ($fp = @fopen($filePath, 'rb')) === false) {
            return;
        }
        // Clean output buffer
        if (ob_get_level() !== 0 && @ob_end_clean() === false) {
            @ob_clean();
        }
        // Generate the server headers
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . $fileSize);
        // Internet Explorer-specific headers
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) {
            header('Cache-Control: no-cache, no-store, must-revalidate');
        }
        header('Pragma: no-cache');
        // If we have raw data - just dump it
        if ($data !== null) {
            exit($data);
        }
        // Flush 1MB chunks of data
        while (!feof($fp) && ($data = fread($fp, round($this->_speed * 1024, 0))) !== false) {
            echo $data;
            sleep(1);
        }
        fclose($fp);
        exit();
    }
}