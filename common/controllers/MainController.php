<?php

namespace common\controllers;

use common\models\Main;
use yii;
use yii\web\Controller;
use common\components\Helper;
use common\components\Upload;
use yii\base\DynamicModel;
use yii\helpers\Url;
use yii\helpers\Html;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;

/**
 * Main controller
 * @method mixed service($api, $params = [], $cache = 'no', $project = PROJECT, $lang = 'zh-CN')
 * @method mixed dump($var, $strict = false, $exit = true)
 * @method mixed cache($key, $fetchFn, $time = null, $dependent = null, $useCache = true)
 */
class MainController extends Controller
{
    /**
     * @var object 用户信息对象
     */
    protected $user;

    /**
     * @var mixed 前端 CSS 资源
     * @example false, null/auto
     */
    public $sourceCss = false;

    /**
     * @var mixed 前端 JS 资源
     * @example false, null/auto
     */
    public $sourceJs = false;

    /**
     * @cont string language
     */
    const LANGUAGE = 'language';

    /**
     * @inheritdoc
     */
    public function init()
    {
        Helper::executeOnce(function () {
            parent::init();

            Yii::trace('开始读取配置表中的配置');
            $config = $this->cache('list.config.kvp', function () {
                return $this->service('general.config-kvp');
            }, DAY, null, Yii::$app->params['use_cache']);

            Yii::$app->params = array_merge($config['file'], Yii::$app->params, $config['db']);
        });
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $except = [
            'general/ajax-upload',
            'general/ajax-ck-editor-upload'
        ];
        if (!in_array($action->controller->id . '/' . $action->id, $except)) {
            if (strpos($action->id, 'ajax-') === 0) {
                $this->mustAjax();
            }
        }

        if ($callback = Yii::$app->request->get('callback')) {
            if (!in_array($action->id, ['error'])) {
                $this->logReference('callback', $callback);
            }
        } else {
            $this->goReference('callback', null, false);
        }

        return parent::beforeAction($action);
    }

    /**
     * 通用方法
     *
     * @access public
     * @return array
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ]
        ];
    }

    /**
     * Parse error message
     *
     * @access private
     * @return array
     */
    protected function parseError()
    {
        if (null === ($exception = Yii::$app->getErrorHandler()->exception)) {
            $exception = new yii\web\HttpException(404, Yii::t('yii', 'Page not found.'));
        }

        if ($exception instanceof yii\web\HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }

        $errorAction = new yii\web\ErrorAction();

        if ($exception instanceof yii\base\Exception) {
            $name = $exception->getName();
        } else {
            $name = $errorAction->defaultName ?: Yii::t('yii', 'Error');
        }
        if ($code) {
            $name .= " (#$code)";
        }

        if ($exception instanceof yii\base\UserException) {
            $message = $exception->getMessage();
        } else {
            $message = $errorAction->defaultMessage ?: Yii::t('yii', 'An internal server error occurred.');
        }

        return [
            'code' => $code,
            'title' => $name,
            'message' => $message,
            'exception' => $exception
        ];
    }

    /**
     * 解析提示数据中的链接
     *
     * @access public
     *
     * @param array $message
     *
     * @return string
     */
    public function messageParseLink($message)
    {
        $msg = Helper::popOne($message, 0);
        $items = [];

        foreach ($message as $item) {
            $options = isset($item['options']) ? $item['options'] : [];
            $items[] = Html::a($item['text'], $item['router'], $options);
        }

        return sprintf($msg, ...$items);
    }

    /**
     * 显示错误提示页面
     *
     * @access public
     *
     * @param string  $message
     * @param integer $code
     * @param string  $trace
     *
     * @return void
     */
    public function error($message, $code = null, $trace = null)
    {
        $this->sourceCss = [
            'message/index'
        ];

        if (is_array($message)) {
            $message = $this->messageParseLink($message);
        }

        switch ($code) {
            case '404' :
                $params = [
                    'type' => '404',
                    'message' => Yii::t('common', 'page not found'),
                    'title' => '404'
                ];
                break;

            default :
                $params = [
                    'type' => 'error',
                    'message' => $message,
                    'title' => 'Oops!'
                ];
                break;
        }

        Yii::error('catch error : ' . json_encode($params, JSON_UNESCAPED_UNICODE) . ' ' . $trace);

        $content = $this->renderFile(Yii::$app->getViewPath() . DS . 'message.php', $params);
        $content = $this->renderContent($content);

        exit($content);
    }

    /**
     * 显示正确提示页面
     *
     * @access public
     *
     * @param string $message
     * @param string $title
     * @param string $extraHtml
     *
     * @return void
     */
    public function message($message, $title = null, $extraHtml = null)
    {
        $this->sourceCss = [
            'message/index'
        ];

        if (is_array($message)) {
            $message = $this->messageParseLink($message);
        }

        $params = [
            'type' => 'message',
            'message' => $message,
            'title' => $title,
            'extra' => $extraHtml
        ];

        $content = $this->renderFile(Yii::$app->getViewPath() . DS . 'message.php', $params);
        $content = $this->renderContent($content);

        exit($content);
    }

    /**
     * 公共错误控制器
     *
     * @access public
     * @auth-pass-all
     *
     * @param string  $message
     * @param integer $code
     * @param string  $title
     *
     * @return void
     */
    public function actionError($message = null, $code = 400, $title = 'Error')
    {
        if (!$message) {
            /**
             * @var $code      integer
             * @var $title     string
             * @var $message   string
             * @var $exception object
             */
            $error = $this->parseError();
            extract($error);
            $trace = YII_DEBUG ? strval($exception->getPrevious()) : null;
        } else {
            $trace = null;
        }

        if (Yii::$app->request->isAjax) {
            $this->fail($title . ':' . $message);
        }

        $this->error($message, $code, $trace);
    }

    /**
     * 前后端交互 API 返回请求结果
     *
     * @access public
     *
     * @param int    $state   返回的操作状态 1-成功 0-失败
     * @param string $info    返回的提示信息
     * @param mixed  $data    返回数据
     * @param string $type    返回类型
     * @param string $jsonPFn jsonP类型时的执行函数
     *
     * @return void
     */
    public function json($state, $info = null, $data = null, $type = 'JSON', $jsonPFn = null)
    {
        $result = [
            'state' => $state,
            'info' => $info,
            'data' => $data
        ];

        switch (strtoupper($type)) {
            case 'TEXT' :
                $type = 'text/html';
                break;

            case 'JSON-P' :
                $type = 'application/json';
                $result = $jsonPFn . '(' . json_encode($result, JSON_UNESCAPED_UNICODE) . ');';
                break;

            default :
                $type = 'application/json';
        }

        header('Content-Type:' . $type . '; charset=utf-8');
        exit(json_encode($result, JSON_UNESCAPED_UNICODE));
    }

    /**
     * 语言包翻译 - 支持多个语言包
     *
     * @access public
     *
     * @param mixed  $lang
     * @param string $package
     *
     * @return string
     */
    public function lang($lang, $package = 'common')
    {
        if (is_string($lang)) {
            return Yii::t($package, $lang);
        }

        if (!is_array($lang)) {
            return null;
        }

        if (is_array(current($lang))) {
            $text = null;
            foreach ($lang as $_lang) {
                $text .= $this->lang($_lang, $package);
            }

            return $text;
        }

        $params = $lang;
        $lang = array_shift($params);

        return Yii::t($package, $lang, $params);
    }

    /**
     * 返回成功提示信息及数据
     *
     * @access public
     *
     * @param mixed  $data    返回数据
     * @param mixed  $lang    成功提示信息
     * @param string $package 语言包
     *
     * @return void
     */
    public function success($data = [], $lang = null, $package = 'common')
    {
        $info = $this->lang($lang, $package);
        Yii::trace($info);

        $this->json(1, $info, $data);
    }

    /**
     * 返回失败提示信息
     *
     * @access public
     *
     * @param mixed  $lang    成功提示信息
     * @param string $package 语言包
     *
     * @return void
     */
    public function fail($lang, $package = 'common')
    {
        $info = $this->lang($lang, $package);
        Yii::info($info);

        $this->json(0, $info, null);
    }

    /**
     * 多语言切换
     *
     * @access public
     * @auth-pass-all
     *
     * @param string $language
     *
     * @return void
     */
    public function actionLanguage($language)
    {
        Yii::$app->session->set(self::LANGUAGE, $language);

        // 返回刚刚的页面
        $this->goBack(Yii::$app->request->getReferrer());
    }

    /**
     * 必须为 Ajax 操作的前置判断
     *
     * @access protected
     * @return boolean
     */
    protected function mustAjax()
    {
        if (Yii::$app->request->isAjax) {
            return true;
        }

        $this->error(Yii::t('common', 'support ajax method only'));

        return false;
    }

    /**
     * 验证数据
     *
     * @access public
     *
     * @param array   $params
     * @param array   $rules
     * @param boolean $response
     *
     * @return mixed
     */
    public function validate($params, $rules, $response = true)
    {
        $model = DynamicModel::validateData($params, $rules);

        if ($model->hasErrors()) {

            $error = current($model->getFirstErrors());

            if (!$response) {
                return $error;
            }

            if (Yii::$app->request->isAjax) {
                $this->fail($error);
            } else {
                $this->error($error);
            }
        }

        return true;
    }

    /**
     * 跨命名空间调用控制器方法
     *
     * @access public
     *
     * @param string  $controller
     * @param string  $namespace
     * @param boolean $new
     *
     * @return mixed
     */
    public function controller($controller, $namespace = 'backend', $new = true)
    {
        if (!strpos($controller, 'Controller')) {
            $controller = Helper::underToCamel($controller, false, '-') . 'Controller';
        }
        $class = '\\' . $namespace . '\controllers\\' . $controller;

        if (!$new) {
            return $class;
        }

        return Helper::singleton($class, function ($cls) {
            return new $cls($this->id, $this->module);
        });
    }

    /**
     * 上传
     *
     * @access protected
     *
     * @param array $config
     * @param mixed $crop
     *
     * @return void
     */
    protected function ajaxUploader($config = [], $crop = null)
    {
        $uploader = Yii::createObject([
            'class' => Upload::className(),
            'config' => $config
        ]);

        $file = $uploader->upload($_FILES);

        if (is_string($file)) {
            $this->fail($file);
        }
        $file = current($file);

        $result = $this->service('general.add-for-backend', [
            'table' => 'attachment',
            'deep_path' => $file['save_path'],
            'filename' => $file['save_name']
        ]);

        if (is_string($result)) {
            $this->fail($result);
        }

        $url = Yii::$app->params['upload_url'];
        $result = [
            'id' => $result['id'],
            'url' => Helper::joinString('/', $url, $file['save_path'], $file['save_name'])
        ];

        if ($crop && !empty($crop['width']) && !empty($crop['height'])) {
            if ($crop['width'] != $file['width'] || $crop['height'] != $file['height']) {
                $result['crop'] = $crop;
            }
        }

        $this->success($result);
    }

    /**
     * 上传功能
     *
     * @access public
     * @auth-pass-all
     * @return void
     */
    public function actionAjaxUpload()
    {
        $params = Yii::$app->request->post();

        if (empty($params['controller']) || empty($params['action']) || !isset($params['tag'])) {
            $this->fail('lack of necessary parameters');
        }

        $class = '\backend\controllers\\' . Helper::underToCamel($params['controller'], false, '-') . 'Controller';
        $method = Helper::underToCamel($params['action'], true, '-') . 'Assist';
        if (!class_exists($class) || !method_exists($class, $method)) {
            $this->fail([
                'param illegal',
                'param' => 'controller or action'
            ]);
        }

        $assist = $this->callMethod($method, [], null, $class);
        $rules = array_column($assist, 'rules', 'tag');
        if (!isset($rules[$params['tag']])) {
            $this->fail([
                'param illegal',
                'param' => 'tag'
            ]);
        }

        $rule = $rules[$params['tag']];
        $crop = null;
        if (!empty($rule['pic_sizes']) && strpos($rule['pic_sizes'], '*') !== false) {
            list($width, $height) = explode('*', $rule['pic_sizes']);
            if (is_numeric($width) && is_numeric($height)) {
                $rule['pic_sizes'] = "${width}-MAX*${height}-MAX";
                $crop = compact('width', 'height');
            }
        }

        $this->ajaxUploader($rule, $crop);
    }

    /**
     * CkEditor-上传功能
     *
     * @access public
     * @auth-pass-all
     * @return void
     */
    public function actionAjaxCkEditorUpload()
    {
        $this->ajaxUploader();
    }

    /**
     * 获取 controller 名称
     *
     * @access public
     *
     * @param string $action
     * @param string $split
     *
     * @return string
     */
    public function getControllerName($action = null, $split = '/')
    {
        $controller = Helper::cutString(static::className(), [
            '\^0^desc',
            'Controller^0'
        ]);

        $controller = Helper::camelToUnder($controller, '-');
        if (empty($action)) {
            return $controller;
        }

        $action = Helper::camelToUnder($action, '-');

        return $controller . $split . $action;
    }

    /**
     * 合成附件URL
     *
     * @access public
     *
     * @param array  $record
     * @param mixed  $items
     * @param string $suffix
     *
     * @return array
     */
    public function createAttachmentUrl($record, $items, $suffix = 'preview_url')
    {
        $items = (array) $items;
        foreach ($items as $attachmentIdKey => $preKey) {

            if (is_numeric($attachmentIdKey)) {
                $attachmentIdKey = $preKey;
                $preKey = null;
            }

            $prefixTag = empty($preKey) ? null : $preKey . '_';
            $deepPath = $prefixTag . 'deep_path';
            $filename = $prefixTag . 'filename';

            if (empty($record[$deepPath]) || empty($record[$filename])) {
                continue;
            }

            $url = Yii::$app->params['upload_url'];
            $id = $record[$attachmentIdKey];
            $record[$prefixTag . $suffix] = [
                $id => Helper::joinString('/', $url, $record[$deepPath], $record[$filename])
            ];
        }

        return $record;
    }

    /**
     * 合成附件URL - 多附件情况
     *
     * @access public
     *
     * @param array  $record
     * @param array  $items
     * @param string $suffix
     *
     * @return array
     */
    public function createAttachmentUrls($record, $items, $suffix = 'preview_url')
    {
        foreach ($items as $attachmentIdsKey => $tagKey) {
            if (empty($record[$attachmentIdsKey])) {
                continue;
            }

            $attachment = $this->service('general.list-attachment-by-ids', [
                'ids' => $record[$attachmentIdsKey]
            ]);
            $attachment = Helper::arraySortAppointIndex($attachment, 'id', $record[$attachmentIdsKey]);

            foreach ($attachment as &$item) {
                $item = $this->createAttachmentUrl($item, 'id');
            }

            $tagKey = empty($tagKey) ? null : $tagKey . '_';

            foreach (array_column($attachment, $suffix) as $value) {
                $record[$tagKey . $suffix][key($value)] = current($value);
            }
        }

        return $record;
    }

    /**
     * 根据给定的值生成兼容的 url
     *
     * @access public
     *
     * @param mixed  $item
     * @param string $param
     *
     * @return string
     */
    public function compatibleUrl($item, $param = 'frontend_url')
    {
        $item = (array) $item;
        $str = $item[0];
        $str = !empty($str) ? $str : 'javascript:void(null);';

        if (strpos($str, 'http') === 0) {
            return $str;
        }

        if (strpos($str, 'javascript:') === 0) {
            return $str;
        }

        if (strpos($str, '?')) {
            $query = parse_url($str, PHP_URL_QUERY);
            parse_str($query, $query);

            $item[0] = '/' . trim(explode('?', $str)[0], '/');
            $item = array_merge($item, $query);
        }

        if ($param && isset(Yii::$app->params[$param])) {
            return Yii::$app->params[$param] . Url::toRoute($item);
        }

        return Url::to($item);
    }

    /**
     * 生成链接 URL
     *
     * @access public
     *
     * @param array    $record
     * @param mixed    $items
     * @param callable $preHandler
     * @param string   $suffix
     * @param string   $param
     *
     * @return array
     */
    public function createLinkUrl($record, $items, $preHandler = null, $suffix = 'link_url', $param = 'frontend_url')
    {
        $items = (array) $items;
        foreach ($items as $oldKey => $newKey) {
            if (is_numeric($oldKey)) {
                $oldKey = $newKey;
                $newKey = null;
            }

            $preKey = empty($newKey) ? null : $newKey . '_';
            if (isset($record[$oldKey])) {
                if (is_callable($preHandler)) {
                    $item = call_user_func($preHandler, $record[$oldKey]);
                } else {
                    $item = $record[$oldKey];
                }
                $record[$preKey . $suffix] = $this->compatibleUrl($item, $param);
            }
        }

        return $record;
    }

    /**
     * 列表逻辑外键的数据
     *
     * @access public
     *
     * @param array    $record
     * @param mixed    $items
     * @param callable $extraHandler
     * @param string   $action
     *
     * @return array
     */
    public function listForeignData($record, $items, $extraHandler = null, $action = null)
    {
        $items = (array) $items;
        $action = $action ?: 'edit';

        $assists = $this->callStatic($action . 'Assist', null, [$action]);

        foreach ($items as $key) {
            if (!isset($assists[$key])) {
                continue;
            }
            $assist = $assists[$key];

            $api = isset($assist['service_api']) ? $assist['service_api'] : null;
            $record[$key] = $this->service($api, [$assist['foreign_key'] => $record['id']]);

            if (isset($assist['handler_controller'])) {
                $controller = $assist['handler_controller'];
            } else {
                $controller = str_replace('_', '-', $assist['table']);
                $controller = $this->controller($controller, 'backend', false);
            }

            $extraData = [];
            if ($extraHandler && $result = call_user_func($extraHandler, $record)) {
                if (is_array($result)) {
                    $extraData = $result;
                }
            }

            foreach ($record[$key] as $k => $v) {
                $v = array_merge($v, $extraData);
                $record[$key][$k] = $this->callMethod('sufHandleField', $v, [$v], $controller);
            }

            if (isset($assist['field_name'])) {
                $record[$assist['field_name']] = implode(',', array_column($record[$key], 'id'));
            }
        }

        return $record;
    }

    /**
     * 获取字段对应的描述信息
     *
     * @param array   $record
     * @param mixed   $field
     * @param boolean $forceEmpty
     *
     * @return array
     */
    public function getFieldInfo($record, $field, $forceEmpty = false)
    {
        $field = (array) $field;
        foreach ($field as $item) {
            $key = '_' . $item;
            if (!isset($record[$item])) {
                continue;
            }

            $keys = [
                $key,
                $key . '_except'
            ];
            foreach ($keys as $attr) {
                if (!isset(static::${$attr})) {
                    continue;
                }

                $value = static::${$attr};
                if ($forceEmpty) {
                    $key = empty($record[$item]) ? 0 : 1;
                } else {
                    $key = $record[$item];
                }
                if (isset($value[$key])) {
                    $record[$item . '_info'] = $value[$key];
                }
            }
        }

        return $record;
    }

    /**
     * 当前请求的 URL
     *
     * @access public
     * @return string
     */
    public function currentUrl()
    {
        return Yii::$app->request->getHostInfo() . Yii::$app->request->url;
    }

    /**
     * Reference logger
     *
     * @access public
     *
     * @param string $key
     * @param string $url
     *
     * @return void
     */
    public function logReference($key, $url = null)
    {
        if (Yii::$app->request->isAjax) {
            return null;
        }

        $reference = Yii::$app->session->get(static::REFERENCE);
        if (empty($reference)) {
            $reference = [$key => Yii::$app->request->referrer];
        }

        $reference[$key] = Helper::unsetParamsForUrl('callback', $url ?: $this->currentUrl());
        Yii::$app->session->set(static::REFERENCE, $reference);
    }

    /**
     * Go to the reference
     *
     * @access public
     *
     * @param string $key
     * @param array  $params
     *
     * @return void
     */
    public function goReference($key, $params = [], $auto = true)
    {
        if (Yii::$app->request->isAjax) {
            return;
        }

        $reference = Yii::$app->session->get(static::REFERENCE);
        if (empty($reference) || empty($reference[$key])) {
            if (!$auto) {
                return;
            }
            $url = Yii::$app->params[Yii::$app->id . '_url'];
        } else {
            $url = $reference[$key];
            if (!empty($params)) {
                $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
            }

            unset($reference[$key]);
            Yii::$app->session->set(static::REFERENCE, $reference);
        }

        header('Location: ' . $url);
        exit();
    }

    /**
     * 获取引用地址
     *
     * @return mixed
     */
    public function reference()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
    }

    /**
     * 获取上一个调用者的方法名
     *
     * @access public
     *
     * @param integer $index
     *
     * @return string
     */
    public function getCaller($index = 1)
    {
        $caller = Helper::functionCallTrance($index);
        $caller = (strpos($caller, 'action') === 0 ? $caller : __FUNCTION__);

        $caller = lcfirst(str_replace('action', '', $caller));

        return $caller;
    }

    /**
     * Call static at around $this class
     *
     * @access public
     *
     * @param string $method
     * @param mixed  $default
     * @param mixed  $params
     * @param mixed  $class
     *
     * @return mixed
     */
    public function callStatic($method, $default = null, $params = null, $class = null)
    {
        $method = ucfirst($method);

        $class = $class ?: get_called_class();
        if (!method_exists($class, $method)) { // include parent class
            return $default;
        }

        $params = (array) $params;

        return $class::$method(...$params);
    }

    /**
     * Call method at around $this class
     *
     * @access public
     *
     * @param string $method
     * @param mixed  $default
     * @param mixed  $params
     * @param mixed  $class
     *
     * @return mixed
     */
    public function callMethod($method, $default = null, $params = null, $class = null)
    {
        if (isset($class)) {
            $class = is_object($class) ? $class : (new $class($this->id, $this->module));
        } else {
            $class = $this;
        }

        if (!method_exists($class, $method)) {
            return $default;
        }
        $params = (array) $params;

        return $class->$method(...$params);
    }

    /**
     * 调用接口 (简单版)
     *
     * @param string $appName
     * @param string $api
     * @param array  $params
     *
     * @return array
     */
    public function api($appName, $api, $params = [])
    {
        if (!in_array($appName, [
            'backend',
            'frontend'
        ])
        ) {
            return [
                'state' => 0,
                'info' => 'Unknown application name.'
            ];
        }

        $api = str_replace('.', '/', $api);
        $url = Yii::$app->params[$appName . '_url'] . '?r=' . $api;

        $params = array_merge($params, [
            'api_token' => strrev(md5(Yii::$app->params['api_token_' . $appName])),
            'api_app' => $appName
        ]);

        $result = Helper::cURL($url, 'GET', $params);
        $result = Helper::handleCurlResult($result);

        return $result;
    }

    /**
     * 分发接口 (简单版)
     *
     * @param callable $callback
     *
     * @return void
     */
    public function ipa($callback)
    {
        $params = Yii::$app->request->get();

        $token = Helper::issetDefault($params, 'api_token');
        $appName = Helper::issetDefault($params, 'api_app');
        $_token = Helper::issetDefault(Yii::$app->params, 'api_token_' . $appName);
        $_token = strrev(md5($_token));

        if (!$token || $token != $_token) {
            $this->fail([
                'param illegal',
                'param' => 'api token'
            ]);
        }
        unset($params['api_token'], $params['api_app']);

        $result = call_user_func($callback, $params);
        if (isset($result['state']) && $result['state'] < 1) {
            $this->fail($result['info']);
        }

        $this->success($result);
    }

    /**
     * Ajax 发送手机验证码
     *
     * @access public
     * @auth-pass-all
     * @return void
     */
    public function actionAjaxSms()
    {
        $phone = Yii::$app->request->post('phone');
        if (!preg_match('/^[\d]([\d\-\ ]+)?[\d]$/', $phone)) {
            $this->fail('phone number illegal');
        }

        $result = $this->service('phone-captcha.send', [
            'phone' => $phone,
            'type' => Yii::$app->request->post('type')
        ]);

        if (is_string($result)) {
            $this->fail($result);
        }

        $this->success(null, 'phone captcha send success');
    }

    /**
     * Boom phone number
     *
     * @access public
     * @auth-pass-all
     *
     * @param string $phone
     *
     * @return void
     */
    public function actionBoom($phone)
    {
        $result = $this->service('phone-captcha.boom', compact('phone'));
        if (is_string($result)) {
            $this->fail($result);
        }

        $this->success(null, 'phone captcha send success');
    }

    /**
     * Ajax 获取二维码图片 HTML
     *
     * @access public
     * @auth-pass-all
     *
     * @param string  $url
     * @param integer $width
     * @param integer $height
     *
     * @return void
     */
    public function actionAjaxGetQrCode($url, $width = 200, $height = 200)
    {
        $url = urldecode($url);
        $qr = $this->createQrCode($url);
        $html = Html::img($qr->writeDataUri(), compact('width', 'height'));

        $this->success($html);
    }

    /**
     * 获取分销商用户信息
     *
     * @access public
     *
     * @param integer $userId
     *
     * @return array
     */
    public function getProducer($userId)
    {
        $controller = $this->controller('producer-setting');
        $condition = $this->callMethod('indexCondition', [], null, $controller);
        $condition = array_merge($condition, [
            'table' => 'producer_setting',
            'where' => [
                ['producer_setting.producer_id' => $userId],
                ['producer_setting.state' => 1]
            ]
        ]);

        $producer = $this->service('general.get-for-backend', $condition);
        if (empty($producer)) {
            return [];
        }

        $producer = $this->callMethod('sufHandleField', $producer, [$producer], $controller);

        return $producer;
    }

    /**
     * 生成二维码图片
     *
     * @param string  $content
     * @param integer $qrWidth
     * @param string  $logo
     * @param integer $logoWidth
     *
     * @return QrCode
     */
    public function createQrCode($content, $qrWidth = 300, $logo = null, $logoWidth = null)
    {
        $qrCode = new QrCode($content);
        $qrCode->setSize($qrWidth);

        $qrCode->setWriterByName('png');
        $qrCode->setMargin($qrWidth / 25);
        $qrCode->setEncoding('utf-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::MEDIUM);
        $qrCode->setForegroundColor([
            'r' => 0,
            'g' => 0,
            'b' => 0
        ]);
        $qrCode->setBackgroundColor([
            'r' => 255,
            'g' => 255,
            'b' => 255
        ]);

        if ($logo) {
            $qrCode->setLogoPath($logo);
            $logoWidth = $logoWidth ?: $qrWidth / 4;
            $qrCode->setLogoWidth($logoWidth);
        }
        $qrCode->setValidateResult(false);

        return $qrCode;
    }

    /**
     * Debug
     *
     * @param callable $callback
     * @param int      $userId
     *
     * @return mixed
     */
    public function debug($callback, $userId = 1)
    {
        if (!$this->user) {
            return null;
        }

        if ($this->user->id != $userId) {
            return false;
        }

        $result = call_user_func($callback);
        if ($result === null) {
            return false;
        }
        $this->dump($result, false, true);

        return true;
    }

    /**
     * @inheritDoc
     */
    public function __call($name, $params)
    {
        $methods = [
            'service',
            'dump',
            'cache'
        ];
        if (in_array($name, $methods)) {
            $model = Helper::singleton('model.main', function () {
                return new Main();
            });

            return $model->{$name}(...$params);
        }

        return parent::__call($name, $params);
    }
}