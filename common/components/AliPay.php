<?php

namespace common\components;

use yii\base\Object;
use Yii;

/**
 * AliPay components
 *
 * @author    Leon <jiangxilee@gmail.com>
 * @copyright 2017-04-11 15:47:57
 */
class AliPay extends Object
{
    /**
     * @var object SDK instance
     */
    public $app;

    /**
     * @var array option
     */
    public $options = [
        'callback' => null,
        'gateway_url' => 'https://openapi.alipay.com/gateway.do',
        'rsa_private_key' => '@alipay/key/rsa_app.private',
        'pay_public_key' => '@alipay/key/rsa_alipay.public',
    ];

    /**
     * @var array params
     */
    public $params = [
        'app_id' => null,
        'method' => null,
        'format' => 'json',
        'charset' => 'UTF-8',
        'sign_type' => 'RSA2',
        'sign' => null,
        'timestamp' => null,
        'version' => '1.0',
        'notify_url' => null,
        'biz_content' => null,
    ];

    /**
     * __constructor
     *
     * @access public
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->options = array_merge($this->options, $config['options']);
        $this->params = array_merge($this->params, $config['params']);

        $this->options['callback'] = Yii::$app->params['alipay_callback'];

        $key = Yii::getAlias($this->options['rsa_private_key']);
        $this->options['rsa_private_key'] = file_get_contents($key);

        $key = Yii::getAlias($this->options['pay_public_key']);
        $this->options['pay_public_key'] = file_get_contents($key);

        $this->timestamp = date('Y-m-d H:i:s', TIME);

        parent::__construct();
    }

    /**
     * Create sign
     *
     * @access public
     * @return void
     */
    public function sign()
    {
        $params = array_filter($this->params);
        unset($params['sign']);
        ksort($params);

        $paramsStr = null;
        foreach ($params as $key => $val) {
            $paramsStr .= '&' . $key . '=' . $val;
        }
        $paramsStr = ltrim($paramsStr, '&');

        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n";
        $privateKey .= wordwrap($this->options['rsa_private_key'], 64, "\n", true);
        $privateKey .= "\n-----END RSA PRIVATE KEY-----";

        if ('RSA2' == $this->sign_type) {
            openssl_sign($paramsStr, $sign, $privateKey, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($paramsStr, $sign, $privateKey);
        }

        $this->sign = base64_encode($sign);
    }

    /**
     * Create order
     *
     * @access public
     *
     * @param array $params
     *
     * @return void
     */
    public function order($params)
    {
        $params['product_code'] = 'QUICK_WAP_PAY';
        if (!isset($params['timeout_express'])) {
            $params['timeout_express'] = '60m';
        }

        $this->method = 'alipay.trade.wap.pay';
        $this->biz_content = json_encode($params, JSON_UNESCAPED_UNICODE);
        Helper::dump($this->params,1);

        $this->sign();
        $url = $this->options['gateway_url'] . '?charset=UTF-8';

        header("Content-type:text/html;charset=utf-8");
        echo Helper::postForm($url, $this->params);
    }

    /**
     * __setter
     *
     * @access public
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return void
     */
    public function __set($name, $value)
    {
        $this->params[$name] = $value;
    }

    /**
     * __getter
     *
     * @access public
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return isset($this->params[$name]) ? $this->params[$name] : null;
    }
}