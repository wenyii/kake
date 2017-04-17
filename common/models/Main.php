<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii;
use common\components\Helper;

/**
 * Main model
 *
 * @author    Leon <jiangxilee@gmail.com>
 * @copyright 2016-11-18 09:18:45
 */
class Main extends ActiveRecord
{
    /**
     * @var array 对应 upload rules key 的描述
     */
    public $_upload_rules = [
        'mimes' => 'MIME类型',
        'suffix' => '文件后缀',
        'pic_sizes' => '图片尺寸(PX)',
        'max_size' => '文件不超过(KB)',
    ];

    /**
     * @var string table name
     */
    public $tableName;

    /**
     * @var array model instance
     */
    public static $model;

    /**
     * Constructor
     *
     * @param string $name
     * @param array  $config
     */
    public function __construct($name = null, $config = [])
    {
        $this->tableName = Helper::camelToUnder($name);
        parent::__construct($config);

        $model = ($this->tableName ? Helper::underToCamel($this->tableName, false) : 'Main');
        Yii::trace('实例化模型: ' . $model . 'Model');
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        $meta = $this->meta();

        return empty($meta['fnRules']) ? [] : $meta['fnRules'];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        $meta = $this->meta();

        return empty($meta['fnAttributeLabels']) ? [] : $meta['fnAttributeLabels'];
    }

    /**
     * Call service
     *
     * @access public
     *
     * @param string $api
     * @param array  $params
     * @param string $cache
     * @param string $lang
     *
     * @return mixed
     * @throws \Exception
     */
    public function service($api, $params = [], $cache = 'true', $lang = 'zh-CN')
    {
        $conf = Yii::$app->params;

        // array to string
        array_walk($params, function (&$value) {
            if (is_array($value)) {
                $value = json_encode($value);
            } else if (is_numeric($value)) {
                $value = (string) $value;
            } else if (is_bool($value)) {
                $value = (string) ($value ? 1 : 0);
            } else if (!is_string($value)) {
                $value = null;
            }
        });

        // merge params
        $params = array_merge($params, [
            'app_api' => $api,
            'app_id' => $conf['service_app_id'],
            'app_secret' => $conf['service_app_secret'],
            'app_lang' => $lang,
            'app_cache' => $cache
        ]);

        // create sign
        unset($params['r']);
        $params = Helper::createSign($params);
        $params = '"' . http_build_query($params) . '"';

        // call client
        $client = realpath(Yii::getAlias('@thrift/client.php'));
        Yii::trace('服务请求开始: ' . $api . ' with ' . json_encode($params));
        $cmd = Helper::joinString(' ', 'php', $client, $params, $conf['thrift_ip'], $conf['thrift_port']);
        exec($cmd, $result);
        Yii::trace('服务请求结束');

        $result = Helper::handleCliResult($result);

        if ($result['state'] == -1) {
            if (empty($result['info'])) {
                $result['info'] = '接口未返回任何数据';
            }
            Yii::error($result['info']);
            if (strpos($result['info'], '<!doctype html>') === false) {
                throw new \Exception($result['info']);
            }
            exit($result['info']);
        }

        if ($result['info'] == 'DEBUG') {
            $this->dump($result['data']);
        }

        return $result['state'] ? $result['data'] : $result['info'];
    }

    /**
     * Dump variable
     *
     * @param mixed $var
     * @param bool  $strict
     * @param bool  $exit
     *
     * @return void
     */
    public function dump($var, $strict = false, $exit = true)
    {
        Helper::dump($var, $exit, $strict);
    }

    /**
     * 获取缓存
     *
     * @param mixed                   $key
     * @param callable                $fetchFn
     * @param int                     $time
     * @param \yii\caching\Dependency $dependent
     *
     * @return mixed
     */
    public function cache($key, $fetchFn, $time = null, $dependent = null)
    {
        if (!Yii::$app->params['use_cache'] || Yii::$app->session->getFlash('no_cache')) {
            return call_user_func($fetchFn);
        }

        if (!(is_string($key) && strpos($key, '.') !== false)) {
            $key = static::className() . '-' . md5(json_encode($key));
        }

        $data = Yii::$app->cache->get($key);

        if (false === $data) {
            Yii::trace('缓存命中失败并重新获取写入: ' . $key);
            $data = call_user_func($fetchFn);
            $time = $time ?: Yii::$app->params['cache_time'];
            $result = Yii::$app->cache->set($key, $data, $time, $dependent);

            if ($result === false) {
                Yii::error('写入缓存失败: ' . $key);
            }
        } else {
            Yii::trace('缓存命中成功: ' . $key);
        }

        return $data;
    }

    /**
     * Get meta data of model
     *
     * @access public
     * @return array
     */
    public function meta()
    {
        if (empty($this->tableName)) {
            return [];
        }

        if (isset(self::$model[$this->tableName]) && !empty(self::$model[$this->tableName])) {
            return self::$model[$this->tableName];
        }

        self::$model[$this->tableName] = $this->cache('main.model-meta.' . $this->tableName, function () {
            Yii::trace('获取模型表原数据: ' . $this->tableName);

            return $this->service('main.model-meta', [
                'table' => $this->tableName
            ], 'no');
        }, YEAR);

        return self::$model[$this->tableName];
    }

    /**
     * @inheritDoc
     */
    public function __get($name)
    {
        $meta = $this->meta();
        if (isset($meta[$name])) {
            return $meta[$name];
        }

        return parent::__get($name);
    }
}
