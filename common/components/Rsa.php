<?php

namespace common\components;

use yii\base\Object;

/**
 * Rsa components
 *
 * @author    Leon <jiangxilee@gmail.com>
 * @copyright 2015-12-9 10:39:26
 */
class Rsa extends Object
{
    /**
     * @var string public key
     */
    private $_publicKey = null;

    /**
     * @var string private key
     */
    private $_privateKey = null;

    /**
     * @var int max length of text
     */
    private $_textMaxLen = 117;

    /**
     * @var int max length of crypt text
     */
    private $_cryptTextMaxLen = 172;

    /**
     * __constructor
     *
     * @access public
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->_privateKey = file_get_contents(\Yii::getAlias($this->_privateKey));
        $this->_publicKey = file_get_contents(\Yii::getAlias($this->_publicKey));
    }

    /**
     * Get Public Key
     *
     * @access public
     * @return string
     */
    public function getPublicKey()
    {
        return openssl_pkey_get_public($this->_publicKey);
    }

    /**
     * Get Private Key
     *
     * @access public
     * @return string
     */
    public function getPrivateKey()
    {
        return openssl_pkey_get_private($this->_privateKey);
    }

    /**
     * Encrypt By Public Key
     *
     * @access public
     *
     * @param string $string
     * @param string $cryptText
     *
     * @return string
     */
    public function encryptByPublicKey($string, $cryptText = null)
    {
        $_string = substr($string, 0, $this->_textMaxLen);

        if (openssl_public_encrypt($_string, $encrypted, $this->getPublicKey())) {
            $cryptText .= base64_encode($encrypted);
        }

        $string = substr($string, $this->_textMaxLen);
        if (strlen($string) > 0) {
            return $this->encryptByPublicKey($string, $cryptText);
        }

        return $cryptText;
    }

    /**
     * Encrypt By Private Key
     *
     * @param string $string
     * @param string $cryptText
     *
     * @return string
     */
    public function encryptByPrivateKey($string, $cryptText = null)
    {
        $_string = substr($string, 0, $this->_textMaxLen);

        if (openssl_private_encrypt($_string, $encrypted, $this->getPrivateKey())) {
            $cryptText .= base64_encode($encrypted);
        }

        $string = substr($string, $this->_textMaxLen);
        if (strlen($string) > 0) {
            return $this->encryptByPrivateKey($string, $cryptText);
        }

        return $cryptText;
    }

    /**
     * Decrypt By Public Key
     *
     * @access public
     *
     * @param string  $cryptText
     * @param string  $text
     * @param boolean $fromJS
     *
     * @return string
     */
    public function decryptByPublicKey($cryptText, $text = null, $fromJS = false)
    {
        $_cryptText = substr($cryptText, 0, $this->_cryptTextMaxLen);
        $padding = $fromJS ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;

        if (openssl_public_decrypt(base64_decode($_cryptText), $decrypted, $this->getPublicKey(), $padding)) {
            $text .= $fromJS ? trim(strrev($decrypted)) : $decrypted;
        }

        $cryptText = substr($cryptText, $this->_cryptTextMaxLen);
        if (strlen($cryptText) > 0) {
            return $this->decryptByPublicKey($cryptText, $text, $fromJS);
        }

        return $text;
    }

    /**
     * Decrypt By Private Key
     *
     * @access public
     *
     * @param string  $cryptText
     * @param string  $text
     * @param boolean $fromJS
     *
     * @return string
     */
    public function decryptByPrivateKey($cryptText, $text = null, $fromJS = false)
    {
        $_cryptText = substr($cryptText, 0, $this->_cryptTextMaxLen);
        $padding = $fromJS ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;

        if (openssl_private_decrypt(base64_decode($_cryptText), $decrypted, $this->getPrivateKey(), $padding)) {
            $text .= $fromJS ? trim(strrev($decrypted)) : $decrypted;
        }

        $cryptText = substr($cryptText, $this->_cryptTextMaxLen);
        if (strlen($cryptText) > 0) {
            return $this->decryptByPrivateKey($cryptText, $text, $fromJS);
        }

        return $text;
    }

    /**
     * Decrypt From JS
     *
     * @access public
     *
     * @param string $cryptText
     *
     * @return string
     */
    public function decryptFromJS($cryptText)
    {
        $key = @base64_encode(pack('H*', $cryptText));
        $text = $this->decryptByPrivateKey($key, null, true);

        return $text;
    }

    /**
     * __setter
     *
     * @access public
     *
     * @param string $key
     * @param mixed  $val
     *
     * @return void
     */
    public function __set($key, $val)
    {
        $this->{'_' . $key} = $val;
    }
}