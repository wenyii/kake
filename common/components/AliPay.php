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
    private $app;

    /**
     * @var array option
     */
    private $options = [
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
     * alipay.trade.wap.pay
     *
     * @access public
     *
     * @param array $params
     *
     * @return void
     */
    public function alipayTradeWapPay($params)
    {
        $params['product_code'] = 'QUICK_WAP_PAY';
        if (!isset($params['timeout_express'])) {
            $params['timeout_express'] = '60m';
        }

        $this->request($params);
    }

    /**
     * alipay.trade.query
     *
     * @access public
     *
     * @param string $outTradeNo
     *
     * @return mixed
     */
    public function alipayTradeQuery($outTradeNo)
    {
        $params['out_trade_no'] = $outTradeNo;

        return $this->request($params, 'GET');
    }

    /**
     * alipay.trade.close
     *
     * @access public
     *
     * @param string $outTradeNo
     *
     * @return mixed
     */
    public function alipayTradeClose($outTradeNo)
    {
        $params['out_trade_no'] = $outTradeNo;

        return $this->request($params, 'GET');
    }

    /**
     * alipay.trade.refund
     *
     * @access public
     *
     * @param string $outTradeNo
     * @param string $outRequestNo
     * @param float  $refundAmount
     *
     * @return mixed
     */
    public function alipayTradeRefund($outTradeNo, $outRequestNo, $refundAmount)
    {
        $params['out_trade_no'] = $outTradeNo;
        $params['out_request_no'] = $outRequestNo;
        $params['refund_amount'] = $refundAmount;

        return $this->request($params, 'GET');
    }

    /**
     * Handle data for sign
     *
     * @access private
     *
     * @param $params
     *
     * @return string
     */
    private function handleDataForSign($params)
    {
        $params = array_filter($params);
        unset($params['sign']);
        ksort($params);

        $paramsStr = null;
        foreach ($params as $key => $val) {
            $paramsStr .= '&' . $key . '=' . $val;
        }
        $paramsStr = ltrim($paramsStr, '&');

        return $paramsStr;
    }

    /**
     * Create sign
     *
     * @access private
     * @return void
     */
    private function createSign()
    {
        $paramsStr = $this->handleDataForSign($this->params);

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
     * Validate sign for async
     *
     * @access public
     *
     * @param array $params
     *
     * @see    https://doc.open.alipay.com/docs/doc.htm?docType=1&articleId=106120
     * @return boolean
     */
    public function validateSignAsync($params)
    {
        $sign = base64_decode($params['sign']);
        unset($params['sign_type']);
        $paramsStr = $this->handleDataForSign($params);

        return $this->validateSign($paramsStr, $sign);
    }

    /**
     * Validate sign for sync
     *
     * @access public
     *
     * @param array $params
     *
     * @see    https://doc.open.alipay.com/docs/doc.htm?docType=1&articleId=106120
     * @return mixed
     */
    public function validateSignSync($params)
    {
        $sign = base64_decode($params['sign']);
        $params = current($params);
        $paramsStr = json_encode($params, JSON_UNESCAPED_UNICODE);

        $result = $this->validateSign($paramsStr, $sign);

        return $result ? $params : false;
    }

    /**
     * Validate sign
     *
     * @param string $paramsStr
     * @param string $sign
     *
     * @return boolean
     */
    private function validateSign($paramsStr, $sign)
    {
        $publicKey = "-----BEGIN PUBLIC KEY-----\n";
        $publicKey .= wordwrap($this->options['pay_public_key'], 64, "\n", true);
        $publicKey .= "\n-----END PUBLIC KEY-----";

        if ('RSA2' == $this->sign_type) {
            $result = openssl_verify($paramsStr, $sign, $publicKey, OPENSSL_ALGO_SHA256);
        } else {
            $result = openssl_verify($paramsStr, $sign, $publicKey);
        }

        return !!$result;
    }

    /**
     * Request api
     *
     * @access private
     *
     * @param array  $params
     * @param string $method
     *
     * @return mixed
     * @throws \Exception
     */
    private function request($params, $method = 'POST')
    {
        $method = strtoupper($method);
        if (!in_array($method, [
            'POST',
            'GET'
        ])
        ) {
            return 'request method error';
        }

        $api = Helper::functionCallTrance();
        $api = Helper::camelToUnder($api, '.');

        $this->method = $api;
        $this->biz_content = json_encode($params, JSON_UNESCAPED_UNICODE);

        $this->createSign();

        $response = null;
        if ('GET' == $method) {
            $result = Helper::cURL($this->options['gateway_url'], $method, $this->params);
            $result = json_decode($result, true);

            $response = $this->validateSignSync($result);

            if (false === $response) {
                throw new \Exception('validate sign fail');
            }

            if ('10000' != $response['code']) {
                $response = $response['sub_msg'];
            }
        } else {
            $url = $this->options['gateway_url'] . '?charset=UTF-8';

            header("Content-type:text/html;charset=utf-8");
            echo Helper::postForm($url, $this->params);
        }

        return $response;
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