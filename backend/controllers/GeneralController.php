<?php

namespace backend\controllers;

use common\components\Helper;
use common\controllers\MainController;
use common\models\Main;
use yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

/**
 * 通用控制器
 * @method mixed service($api, $params = [], $cache = 'yes', $lang = 'zh-CN')
 */
class GeneralController extends MainController
{
    /**
     * @cont string user info key
     */
    const USER = 'backend_user_info';

    /**
     * @cont string reference
     */
    const REFERENCE = 'backend_reference';

    /**
     * @var string 数据库
     */
    public static $modelDb = DB_KAKE;

    /**
     * @var string 模型名称
     */
    public static $modelName;

    /**
     * @var string 模型描述
     */
    public static $modelInfo;

    /**
     * @var string 列表接口名
     */
    public static $listApiName = 'main.list-for-backend';
    public static $listFunctionName;

    /**
     * @var string 获取单条接口名
     */
    public static $getApiName = 'main.get-for-backend';
    public static $getFunctionName;

    /**
     * @var string 编辑接口名
     */
    public static $editApiName = 'main.update-for-backend';
    public static $editFunctionName;

    /**
     * @var string 新增接口名
     */
    public static $addApiName = 'main.add-for-backend';
    public static $addFunctionName;

    /**
     * @var string 前置记录接口名
     */
    public static $frontApiName = 'main.front-for-backend';
    public static $frontFunctionName;

    /**
     * @var array HTML 与 UBB 转换钩子容器
     */
    public static $hookUbbAndHtml;

    /**
     * @var array 价格钩子容器
     */
    public static $hookPriceNumber;

    /**
     * @var array 日期格式钩子容器
     */
    public static $hookDateSection;

    /**
     * @var array 日期格式钩子容器 (两个都记录)
     */
    public static $hookDateSectionDouble;

    /**
     * @var array 单独的业务逻辑钩子容器
     */
    public static $hookLogic;

    // ---

    /**
     * @inheritDoc
     */
    public function init()
    {
        Helper::executeOnce(function () {
            parent::init();

            $this->enableCsrfValidation = true;

            Yii::trace('获取用户信息');
            if (!$this->user && Yii::$app->session->has(self::USER)) {
                $this->user = (object) Yii::$app->session->get(self::USER);
            }
        });
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $router = $action->controller->id . '/' . $action->id;

        if (in_array($router, ['general/error'])) {
            return true;
        }

        if (in_array($router, [
            'login/index',
            'login/ajax-login',
            'general/ajax-sms'
        ])) {
            $this->mustUnLogin();
        } else {
            $this->mustLogin();

            $rootUsers = explode(',', Yii::$app->params['private']['root_user_ids']);
            if (!in_array($this->user->id, $rootUsers)) {
                $this->authVerify($router);
            }

            // admin action log
            if (Yii::$app->request->isAjax || Yii::$app->request->isPost) {
                $log[] = '   Url: ' . $this->currentUrl();
                $log[] = '  User: ' . $this->user->phone . ' (ID: ' . $this->user->id . ')';

                $params = array_merge(Yii::$app->request->get(), Yii::$app->request->post());
                $log[] = 'Params: ' . json_encode($params);
                Yii::trace(implode(PHP_EOL, $log), 'admin-action-log');
            }
        }

        return true;
    }

    /**
     * 操作权限验证
     *
     * @access protected
     *
     * @param string $router
     *
     * @return void
     */
    protected function authVerify($router)
    {
        $_router = $router;
        $authList = $this->authList();
        $authRecord = $this->authRecord($this->user->id);

        // 根据注释完善辅助数据
        $perfectAuthData = function () use (&$authList, &$authRecord, &$router, $_router, &$perfectAuthData) {

            list($class, $method) = explode('/', $router);
            $class = $this->controller($class);
            $method = 'action' . Helper::underToCamel($method, false, '-');

            $classDoc = Yii::$app->reflection->getClassDocument($class);
            $methodDoc = Yii::$app->reflection->getMethodDocument($class, $method);

            if (isset($methodDoc['@auth-same'])) {
                $authList[$router] = $classDoc['info'] . ' > ' . $methodDoc['info'];
                $router = str_replace('{static}', $this->getControllerName(), current($methodDoc['@auth-same']));
                if (!empty($authRecord[$router])) {
                    $authRecord[$_router] = $authRecord[$router];
                }
                $perfectAuthData();
            }
        };

        $perfectAuthData();

        // 权限鉴定
        if (empty($authList[$_router]) || !empty($authRecord[$_router])) {
            return;
        }

        Yii::trace('操作权限鉴定失败: ' . $_router . ' (' . $authList[$_router] . ')');

        $info = Helper::deleteHtml('"' . $authList[$_router] . '" 操作权限不足');
        if (Yii::$app->request->isAjax) {
            $this->fail($info);
        } else {
            $this->error($info);
        }
    }

    /**
     * Get the auth list from php file
     *
     * @access public
     *
     * @param boolean $keepModule
     *
     * @return array
     */
    public function authList($keepModule = false)
    {
        return $this->cache([
            'controller.auth.list',
            $keepModule
        ], function () use ($keepModule) {
            $list = $this->reflectionAuthList();

            $_list = [];
            foreach ($list as $module => $items) {
                $_items = [];
                foreach ($items as $item) {
                    $key = $item['controller'] . '/' . $item['action'];
                    $info = ($keepModule ? null : $module . ' > ') . $item['info'];
                    $_items[$key] = $info;
                }
                $_list[$module] = $_items;
            }

            !$keepModule && $_list = array_merge(...array_values($_list));

            return $_list;
        }, YEAR);
    }

    /**
     * Get the auth list from php file
     *
     * @access public
     *
     * @param integer
     *
     * @return array
     */
    public function authRecord($userId)
    {
        return $this->cache([
            'controller.auth.record',
            $userId
        ], function () use ($userId) {
            $record = $this->service(static::$listApiName, [
                'table' => 'admin_auth',
                'size' => 'all',
                'where' => [
                    ['user_id' => $userId],
                    ['state' => 1]
                ]
            ]);

            $_record = [];
            foreach ($record as $item) {
                $key = $item['controller'] . '/' . $item['action'];
                $_record[$key] = $item['state'];
            }

            return $_record;
        }, YEAR);
    }

    /**
     * 必须登录的操作前置判断
     *
     * @access public
     * @return mixed
     */
    public function mustLogin()
    {
        if ($this->user) {
            return true;
        }

        if (Yii::$app->request->isAjax) {
            $this->fail('login first');
        } else {
            $url = Helper::unsetParamsForUrl('callback', $this->currentUrl());
            header('Location: ' . Url::to([
                    '/login/index',
                    'callback' => $url
                ]));
            exit();
        }

        return false;
    }

    /**
     * 必须非登录的操作前置判断
     *
     * @access public
     * @return mixed
     */
    public function mustUnLogin()
    {
        if (!$this->user) {
            return true;
        }

        if (Yii::$app->request->isAjax) {
            $this->fail('already login');
        } else {
            $this->error(Yii::t('common', 'already login'));
        }

        return false;
    }

    /**
     * 获取应被纳入权限控制的操作列表
     *
     * @access public
     *
     * @param array $exceptControllers
     *
     * @return array
     */
    public function reflectionAuthList($exceptControllers = [
        'GeneralController',
        'MainController'
    ])
    {
        $directory = Yii::getAlias('@backend') . DS . 'controllers';
        $controllers = Helper::readDirectory($directory, ['php'], 'IN');

        $list = [];
        foreach ($controllers as &$controller) {

            // 处理文件路径
            $controller = str_replace([
                $directory . DS,
                '.php'
            ], [
                null,
                null
            ], $controller);

            if (in_array($controller, $exceptControllers)) {
                continue;
            }

            // 获取注释
            $class = $this->controller($controller);
            $classDoc = Yii::$app->reflection->getClassDocument($class);
            $comment = Yii::$app->reflection->getMethodsDocument($class, null);

            $self = [];

            // 处理注释
            foreach ($comment as $key => $val) {

                if (0 !== strpos($key, 'action') || !preg_match('/^(action)[A-Z]/', $key)) {
                    continue;
                }

                $action = Helper::camelToUnder(preg_replace('/action/', null, $key, 1), '-');
                if (!empty($classDoc['@auth-inherit-except']) && in_array($action, $classDoc['@auth-inherit-except'])) {
                    continue;
                }

                // 无需通过后台配置即可决定权限的标示
                if (isset($val['@auth-pass-all']) || isset($val['@auth-same'])) {
                    continue;
                }

                $styleTag = '@auth-info-style';

                // Ajax 操作标题修饰
                if (strpos($action, 'ajax-') === 0) {
                    $style = '{info} (<b>Ajax</b>)';
                    if (empty($val[$styleTag])) {
                        $val[$styleTag] = [$style];
                    } else {
                        $val[$styleTag] = [str_replace('{info}', current($val[$styleTag]), $style)];
                    }
                }

                // 普通操作标题修饰
                if (!empty($val[$styleTag])) {
                    $val['info'] = str_replace('{info}', $val['info'], current($val[$styleTag]));
                }

                $self[] = [
                    'info' => $val['info'],
                    'controller' => Helper::camelToUnder(str_replace('Controller', null, $controller), '-'),
                    'action' => $action
                ];
            }

            if (!empty($self)) {
                $list[$classDoc['info'] ?: 'Unknown'] = $self;
            }
        }

        return $list;
    }

    /**
     * 设置公用参数
     *
     * @access public
     * @return void
     */
    public function commonParams()
    {
        Yii::$app->view->params['user_info'] = $this->user;
        $menu = Yii::$app->params['menu'];

        foreach ($menu as &$item) {
            $controllers = [];
            foreach ($item['sub'] as $router => &$page) {
                list($controller, $action) = explode('.', $router);
                if (!in_array($controller, $controllers)) {
                    $controllers[] = $controller;
                }
                $page = [
                    'title' => $page,
                    'controller' => $controller,
                    'action' => $action
                ];
            }
            $item['controllers'] = $controllers;
        }

        Yii::$app->view->params['menu'] = $menu;

        $hideMenu = isset($this->user->hide_menu) ? $this->user->hide_menu : false;
        Yii::$app->view->params['hidden_menu'] = $hideMenu;
    }

    /**
     * @inheritDoc
     */
    public function error($message, $title = null, $code = null)
    {
        $this->commonParams();
        parent::error($message, $title, $code);
    }

    /**
     * @inheritDoc
     */
    public function display($view, $params = [])
    {
        $this->commonParams();

        $fetchTpl = false;
        foreach (Helper::functionCallTrance('all') as $index => $fn) {
            if (strpos($fn, 'actionAjaxModal') === 0) {
                $fetchTpl = $index;
                break;
            }
        }

        if ($fetchTpl) {
            if (empty($params['view'])) {
                $titleKey = $this->getCaller($fetchTpl) . 'Title';
                $title = isset(static::${$titleKey}) ? static::${$titleKey} : null;
            } else {
                $title = empty($params['view']['title_info']) ? null : $params['view']['title_info'];
            }
            $content = $this->renderFile(Yii::$app->getViewPath() . DS . ltrim($view, '/') . '.php', $params);
            $this->success([
                'title' => $title,
                'message' => $content
            ]);
        }

        return $this->render($view, $params);
    }

    // ---

    /**
     * 获取列表页对记录的操作
     *
     * @access public
     * @return array
     */
    public static function indexOperation()
    {
        return [
            [
                'text' => '编辑',
                'value' => 'edit',
                'icon' => 'pencil'
            ]
        ];
    }

    /**
     * 表单操作页面所需的文档
     *
     * @access public
     *
     * @param string $key
     *
     * @return mixed
     */
    public function pageDocuments($key = null)
    {
        $document = $this->callMethod('pageDocument', []);
        $document = array_merge([
            'add' => [
                'title_icon' => 'plus',
                'title_info' => '新增',
                'button_info' => '新增',
                'action' => 'add-form'
            ],
            'edit' => [
                'title_icon' => 'pencil',
                'title_info' => '编辑',
                'button_info' => '编辑',
                'action' => 'edit-form'
            ]
        ], $document);

        if (!$key) {
            return $document;
        }

        $key = Helper::camelToUnder($key, '-');

        return isset($document[$key]) ? $document[$key] : [];
    }

    /**
     * 获取 model 中的枚举属性
     *
     * @access public
     *
     * @param object $model
     * @param string $enumName
     * @param array  $default
     *
     * @throws \Exception
     * @return mixed
     */
    public function getEnumerate($model, $enumName, $default = [])
    {
        $key = '_' . $enumName;

        $except = function ($data) use ($key) {
            if (isset(static::${$key . '_except'})) {
                $data = static::${$key . '_except'} + $data;
                ksort($data);
            }

            return $data;
        };

        if (isset(static::${$key})) {
            return $except(static::${$key});
        }

        try {
            $enum = $model->$key;
        } catch (\Exception $e) {
            if (!empty($default)) {
                return $except($default);
            }
            throw new \Exception('This enumeration property ' . $key . ' don\'t exist in model');
        }

        return $except($enum);
    }

    /**
     * 编辑页面所需的辅助参数
     *
     * @access public
     * @return array
     */
    public static function editAssist()
    {
        return [];
    }

    /**
     * 新增页面所需的辅助参数
     *
     * @access public
     * @return array
     */
    public static function addAssist()
    {
        return static::editAssist();
    }

    /**
     * 获取列表需要的辅助信息
     *
     * @access public
     *
     * @param array $assist
     *
     * @return array
     */
    public function handleAssistForList($assist)
    {
        $labels = (new Main(static::$modelName))->attributeLabels();
        $_assist = [];

        foreach ($assist as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
                $value = null;
            }

            $value = is_array($value) ? $value : (array) $value;
            $_value = [];
            foreach ($value as $k => $v) {
                if (is_numeric($k)) {
                    $_value[$v] = ++$k;
                } else {
                    $_value[$k] = $v;
                }
            }

            empty($_value['color']) && $_value['color'] = 'info';
            if ($_value['color'] == 'auto') {
                $_value['color'] = [
                    0 => 'danger',
                    1 => 'success'
                ];
            }

            if (empty($_value['title'])) {
                $_key = isset($_value['field']) ? $_value['field'] : $key;
                $_labels = $labels;
                if (isset($_value['table'])) {
                    $_labels = Helper::singleton('model.' . $_value['table'], function () use ($_value) {
                        return new Main($_value['table']);
                    })->attributeLabels();
                }
                $_value['title'] = isset($_labels[$_key]) ? $_labels[$_key] : Yii::t('common', $key);
            }

            if (empty($_value['url_info'])) {
                $_value['url_info'] = 'Link';
            }

            if (empty($_value['not_set_info'])) {
                $_value['not_set_info'] = '<span class="not-set">(Nil)</span>';
            }

            if (strpos($key, 'price') !== false) {
                $_value['tpl'] = '￥%s';
            }

            $title = Helper::popOne($_value, 'title');

            $_assist[$key] = [
                'adorn' => $_value,
                'title' => $title
            ];
        }

        return $_assist;
    }

    /**
     * 获取表单相关需要的辅助信息
     *
     * @access public
     *
     * @param array  $assist
     * @param array  $default
     * @param string $action
     *
     * @return array
     */
    public function handleAssistForForm($assist, $default = [], $action = null)
    {
        $model = new Main(static::$modelName);

        $info = $model->attributeLabels();
        $_assist = [];

        $default = $this->callMethod('sufHandleField', $default, [
            $default,
            $action
        ]);

        foreach ($assist as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
                $value = 'input';
            }

            if (is_string($value)) {
                $value = [
                    'elem' => $value
                ];
            }

            if (empty($value['elem'])) {
                $value['elem'] = 'input';
            }

            if (empty($value['name'])) {
                $value['name'] = $key;
            } else {
                $key = $value['name'];
            }

            !isset($value['title']) && $value['title'] = isset($info[$key]) ? $info[$key] : Yii::t('common', $key);

            if (!empty($value['tip'])) {
                $tip = (array) $value['tip'];
                $tipStr = null;
                foreach ($tip as $k => $v) {
                    if (is_numeric($k)) {
                        $tipStr .= $v;
                    } else {
                        $tipStr .= $k . ': ' . $v;
                    }
                    $tipStr .= '<br>';
                }
                $value['tip'] = $tipStr;
            }

            switch ($value['elem']) {
                case 'select' :
                    $valued = isset($value['value']) ? $value['value'] : null;
                    $list = empty($value['list']) ? null : $value['list'];
                    $value['value'] = [
                        'list' => $this->getEnumerate($model, $key, $list),
                        'name' => $key,
                        'selected' => Helper::issetDefault($default, $key, $valued)
                    ];
                    break;

                case 'input' :
                default:
                    empty($value['type']) && $value['type'] = 'text';

                    if ($value['type'] == 'file') {
                        $value['rules_info'] = $model->_upload_rules;
                    }

                    if (!empty($value['value_field'])) {
                        if (isset($default[$value['value_field']])) {
                            $value['value'] = $default[$value['value_field']];
                        }
                    } else if (!empty($default[$key])) {
                        $value['value'] = $default[$key];
                    }

                    break;
            }

            $_assist[$key] = $value;
        }

        return $_assist;
    }

    /**
     * 获取过滤所需数据
     *
     * @access public
     *
     * @param array  $get
     * @param string $caller
     *
     * @return array
     */
    public function getFilter($get, $caller)
    {
        $filter = $this->callStatic($caller . 'Filter');
        if (!$filter) {
            return [];
        }

        $model = new Main(static::$modelName);
        $labels = $model->attributeLabels();
        $_filter = [];

        $get = $this->callMethod('sufHandleField', $get, [
            $get,
            'filter'
        ]);

        foreach ($filter as $key => $value) {

            if (is_numeric($key)) {
                $key = $value;
                $value = 'select';
            }

            if (is_string($value)) {
                $value = [
                    'elem' => $value
                ];
            }

            empty($value['elem']) && $value['elem'] = 'select';

            $_key = isset($value['field']) ? $value['field'] : $key;
            if (empty($value['title'])) {
                $_labels = $labels;
                if (isset($value['table'])) {
                    $_labels = Helper::singleton('model.' . $value['table'], function () use ($value) {
                        return new Main($value['table']);
                    })->attributeLabels();
                }
                $value['title'] = isset($_labels[$_key]) ? $_labels[$_key] : Yii::t('common', $key);
            }

            empty($value['name']) && $value['name'] = $key;

            $table = isset($value['table']) ? $value['table'] : $model->tableName;
            $value['field'] = $table . '.' . $_key;

            switch ($value['elem']) {
                case 'select' :
                    $list = $this->getEnumerate($model, $key);
                    $list['all'] = '全部';

                    $selected = isset($value['value']) ? $value['value'] : null;
                    $value['value'] = [
                        'list' => $list,
                        'name' => $key,
                        'selected' => Helper::issetDefault($get, $key, $selected)
                    ];
                    break;

                case 'input' :
                default:
                    empty($value['type']) && $value['type'] = 'text';

                    if (!empty($get[$key])) {
                        $value['value'] = $get[$key];
                    }

                    $from = '_from';
                    $to = '_to';

                    if (!empty($get[$key . $from])) {
                        $value['value' . $from] = $get[$key . $from];
                    }

                    if (!empty($get[$key . $to])) {
                        $value['value' . $to] = $get[$key . $to];
                    }

                    if (!empty($value['equal'])) {
                        $value['placeholder'] = '精确搜索';
                    }

                    break;
            }

            $_filter[$key] = $value;
        }

        return $_filter;
    }

    /**
     * 通过过滤条件获取 where 数组
     *
     * @access public
     *
     * @param array $filter
     * @param array $default
     *
     * @return array
     */
    public function getWhereByFilter($filter, $default)
    {
        $default = $this->callMethod('preHandleField', $default, [
            $default,
            'filter'
        ]);

        $where = [];
        foreach ($filter as $name => $item) {

            $field = $item['field'];
            switch ($item['elem']) {

                case 'select' :
                    if (isset($default[$name]) && $default[$name] != 'all') {
                        $where[] = [$field => $default[$name]];
                    }
                    break;

                case 'input' :
                default:

                    $from = $name . '_from';
                    $to = $name . '_to';

                    if (!empty($item['between']) && !empty($default[$from]) && !empty($default[$to])) {
                        $where[] = [
                            'between',
                            $field,
                            $default[$from],
                            $default[$to]
                        ];
                    } elseif (!empty($item['equal']) && !empty($default[$name])) {
                        $where[] = [$field => $default[$name]];
                    } elseif (!empty($default[$name])) {
                        $where[] = [
                            'like',
                            $field,
                            $default[$name]
                        ];
                    }
            }
        }

        return $where;
    }

    /**
     * 预处理字段数据 - 用于数据库交互
     *
     * @access public
     *
     * @param array  $record
     * @param string $action
     *
     * @return array
     */
    public function preHandleField($record, $action = null)
    {
        $record = $this->preHookDateSection($record);
        $record = $this->preHookOrderAttachment($record, $action);
        $record = $this->preHookOrderTag($record, $action);
        $record = $this->preHookDateSectionDouble($record);
        $record = $this->preHookPriceNumber($record);
        $record = $this->preHookUbbAndHtml($record);

        return $record;
    }

    /**
     * 日期转换钩子
     *
     * @access protected
     *
     * @param array $record
     *
     * @return array
     */
    protected function preHookDateSection($record)
    {
        $container = static::$hookDateSection ?: [];
        $container = array_merge([
            'add_time',
            'update_time'
        ], $container);

        $_container = [];
        foreach ($container as $field => $type) {
            if (is_numeric($field)) {
                $field = $type;
                $type = 'date';
            }

            $_container[$field . '_from'] = $type;
            $_container[$field . '_to'] = $type;
        }

        foreach ($_container as $field => $type) {
            if (empty($record[$field])) {
                continue;
            }
            $hour = (strpos($field, '_from') !== false ? '00:00:00' : '23:59:59');
            $date = $record[$field] . ' ' . $hour;

            switch ($type) {
                case 'stamp' :
                    $date = strtotime($date);
                    break;

                case 'date' :
                default :
                    break;
            }

            $record[$field] = $date;
        }

        return $record;
    }

    /**
     * 秩序化附件钩子
     *
     * @access protected
     *
     * @param array  $record
     * @param string $action
     *
     * @return array
     * @throws \Exception
     */
    protected function preHookOrderAttachment($record, $action)
    {
        if (empty($action)) {
            return $record;
        }

        // 附件秩序化
        $caller = ucfirst($action) . 'Assist';
        $assist = $this->callStatic($caller, []);

        foreach ($assist as $item) {
            if (!isset($item['type']) || $item['type'] != 'file') {
                continue;
            }

            if (empty($item['field_name'])) {
                throw new \Exception('Key field_name is required.');
            }

            $key = $item['field_name'];
            $oldKey = 'old_' . $key;

            if (isset($record[$key])) {

                $old = array_filter(explode(',', empty($record[$oldKey]) ? null : $record[$oldKey]));
                $now = array_filter(explode(',', $record[$key]));

                Helper::getDiffWithAction($old, $now, function ($add, $del) use (&$record) {
                    $_add = 'attachment_add';
                    $_del = 'attachment_del';
                    $record[$_add] = empty($record[$_add]) ? $add : Helper::joinString(',', $record[$_add], $add);
                    $record[$_del] = empty($record[$_del]) ? $del : Helper::joinString(',', $record[$_del], $del);
                });
            }
            unset($record[$oldKey]);
        }

        return $record;
    }

    /**
     * 秩序化标签钩子
     *
     * @access protected
     *
     * @param array  $record
     * @param string $action
     *
     * @return array
     * @throws \Exception
     */
    protected function preHookOrderTag($record, $action)
    {
        if (empty($action)) {
            return $record;
        }

        // 附件秩序化
        $caller = ucfirst($action) . 'Assist';
        $assist = $this->callStatic($caller, []);

        foreach ($assist as $item) {

            if (!isset($item['elem']) || $item['elem'] != 'tag') {
                continue;
            }

            if (empty($item['field_name'])) {
                throw new \Exception('Key field_name is required.');
            }

            if (empty($item['table'])) {
                throw new \Exception('Key table is required.');
            }

            if (empty($item['foreign_key'])) {
                throw new \Exception('Key foreign_key is required.');
            }

            $key = $item['field_name'];
            $oldKey = 'old_' . $key;
            $newKey = 'new_' . $key;

            if (isset($record[$key])) {

                if (!isset($record['tags_record'])) {
                    $record['tags_record'] = [];
                }

                $old = array_filter(explode(',', empty($record[$oldKey]) ? null : $record[$oldKey]));
                $now = array_filter(explode(',', $record[$key]));

                $tagRecord = [];
                Helper::getDiffWithAction($old, $now, function ($add, $del) use (&$tagRecord) {
                    $tagRecord['del'] = $del;
                });

                $tagRecord['db'] = empty($item['db']) ? DB_KAKE : $item['db'];
                $tagRecord['table'] = $item['table'];
                $tagRecord['foreign_key'] = $item['foreign_key'];

                if (isset($item['handler_controller'])) {
                    $controller = $item['handler_controller'];
                } else {
                    $controller = str_replace('_', '-', $item['table']);
                    $controller = $this->controller($controller, 'backend', false);
                }

                if (empty($record[$newKey])) {
                    $tagRecord['add'] = [];
                } else {
                    foreach ($record[$newKey] as $query) {
                        parse_str($query, $tagData);
                        $tagData = $this->callMethod('preHandleField', $tagData, [$tagData], $controller);
                        $tagRecord['add'][] = $tagData;
                    }
                }
                $record['tags_record'][$key] = $tagRecord;
            }
            unset($record[$key], $record[$oldKey], $record[$newKey]);
        }

        return $record;
    }

    /**
     * 日期转换钩子
     *
     * @access protected
     *
     * @param array $record
     *
     * @return array
     */
    protected function preHookDateSectionDouble($record)
    {
        $container = static::$hookDateSectionDouble;
        if (empty($container)) {
            return $record;
        }

        foreach ($container as $field => $type) {
            if (is_numeric($field)) {
                $field = $type;
                $type = 'date';
            }

            $_from = empty($field) ? 'from' : $field . '_from';
            $_to = empty($field) ? 'to' : $field . '_to';

            if (!empty($record[$_from]) || !empty($record[$_to])) {

                $from = $record[$_from];
                $to = $record[$_to];

                switch ($type) {
                    case 'date' :
                        $from = strtotime($from);
                        $to = strtotime($to);
                        break;

                    case 'stamp' :
                    default :
                        break;
                }

                if ($from === false || $to === false || $from >= $to) {
                    unset($record[$_from], $record[$_to]);
                } else {
                    $record[$_from] = date('Y-m-d H:i:s', $from);
                    $record[$_to] = date('Y-m-d H:i:s', $to);
                }
            }
        }

        return $record;
    }

    /**
     * 金钱数值钩子
     *
     * @access protected
     *
     * @param array   $record
     * @param integer $multiple
     *
     * @return array
     */
    protected function preHookPriceNumber($record, $multiple = 100)
    {
        $container = static::$hookPriceNumber;
        if (empty($container)) {
            return $record;
        }

        foreach ($container as $hook) {
            !empty($record[$hook]) && $record[$hook] = intval($record[$hook] * $multiple);
        }

        return $record;
    }

    /**
     * UBB&HTML钩子
     *
     * @access protected
     *
     * @param array $record
     *
     * @return array
     */
    protected function preHookUbbAndHtml($record)
    {
        $container = static::$hookUbbAndHtml;
        if (empty($container)) {
            return $record;
        }

        foreach ($container as $hook) {
            if (empty($record[$hook])) {
                continue;
            }
            $record[$hook] = Yii::$app->ubb->htmlToUbb($record[$hook], true);
        }

        return $record;
    }

    /**
     * 逻辑钩子
     *
     * @access protected
     *
     * @param array $where
     *
     * @return array
     */
    protected function preHookLogicForWhere($where)
    {
        $container = static::$hookLogic;
        if (empty($container) || empty($where)) {
            return $where;
        }

        $_container = [];
        foreach ($container as $key => $tag) {
            if (is_numeric($key)) {
                $_container[$tag] = 'id';
            } else {
                $_container[$key] = $tag;
            }
        }

        $_where = [];
        foreach ($where as $key => $item) {
            $_key = key($item);
            if (!isset($_container[$_key])) {
                $_where[$key] = $item;
                continue;
            }
            $logicHandler = Helper::underToCamel($_key) . 'ReverseWhereLogic';
            $_where = array_merge($_where, $this->callStatic($logicHandler, [], [current($item)]));
        }

        return $_where;
    }

    /**
     * 后处理字段数据 - 用于用户交互
     *
     * @access public
     *
     * @param array    $record
     * @param string   $action
     * @param callable $callback
     *
     * @return array
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        $record = $this->sufHookLogic($record, $action);

        if (is_callable($callback)) {
            $record = call_user_func($callback, $record, $action);
        }

        $record = $this->sufHookDateSection($record);
        $record = $this->sufHookDateSectionDouble($record, $action);
        $record = $this->sufHookPriceNumber($record);
        $record = $this->sufHookUbbAndHtml($record);

        return $record;
    }

    /**
     * 日期转换钩子
     *
     * @access protected
     *
     * @param array $record
     *
     * @return array
     */
    protected function sufHookDateSection($record)
    {
        $container = static::$hookDateSection ?: [];
        $container = array_merge([
            'add_time',
            'update_time'
        ], $container);

        foreach ($container as $field => $type) {
            if (empty($record[$field])) {
                continue;
            }

            switch ($type) {
                case 'stamp' :
                    $date = date('Y-m-d H:i:s', $record[$field]);
                    if (strpos($record[$field], '.') !== false) {
                        $date .= ':' . explode('.', $record[$field])[1];
                    }
                    $record[$field] = $date;
                    break;

                case 'date' :
                default :
                    break;
            }
        }

        return $record;
    }

    /**
     * 日期转换钩子
     *
     * @access protected
     *
     * @param array  $record
     * @param string $action
     *
     * @return array
     */
    protected function sufHookDateSectionDouble($record, $action)
    {
        if (!in_array($action, ['edit'])) {
            return $record;
        }

        $container = static::$hookDateSectionDouble;
        if (empty($container)) {
            return $record;
        }

        foreach ($container as $field => $type) {
            if (is_numeric($field)) {
                $field = $type;
                $type = 'date';
            }

            $_from = empty($field) ? 'from' : $field . '_from';
            $_to = empty($field) ? 'to' : $field . '_to';

            if (!empty($record[$_from]) && !empty($record[$_to])) {

                $from = $record[$_from];
                $to = $record[$_to];

                switch ($type) {
                    case 'date' :
                        $from = strtotime($from);
                        $to = strtotime($to);
                        break;

                    case 'stamp' :
                    default :
                        break;
                }

                $record[$_from] = date('Y-m-d\TH:i', $from);
                $record[$_to] = date('Y-m-d\TH:i', $to);
            }
        }

        return $record;
    }

    /**
     * 金钱数值钩子
     *
     * @access protected
     *
     * @param array   $record
     * @param integer $multiple
     *C
     *
     * @return array
     */
    protected function sufHookPriceNumber($record, $multiple = 100)
    {
        $container = static::$hookPriceNumber;
        if (empty($container)) {
            return $record;
        }

        foreach ($container as $hook) {
            !empty($record[$hook]) && $record[$hook] = $record[$hook] / $multiple;
        }

        return $record;
    }

    /**
     * UBB&HTML钩子
     *
     * @access protected
     *
     * @param array $record
     *
     * @return array
     */
    protected function sufHookUbbAndHtml($record)
    {
        $container = static::$hookUbbAndHtml;
        if (empty($container)) {
            return $record;
        }

        foreach ($container as $hook) {
            if (empty($record[$hook])) {
                continue;
            }
            $record[$hook] = Yii::$app->ubb->ubbToHtml($record[$hook]);
        }

        return $record;
    }

    /**
     * 逻辑钩子
     *
     * @access protected
     *
     * @param array  $record
     * @param string $action
     *
     * @return array
     */
    protected function sufHookLogic($record, $action)
    {
        if (in_array($action, ['filter'])) {
            return $record;
        }

        $container = static::$hookLogic;
        if (empty($container)) {
            return $record;
        }

        foreach ($container as $key => $tag) {
            if (is_numeric($key)) {
                $key = $tag;
                $tag = 'id';
            }

            if (!isset($record[$tag])) {
                return $record;
            }

            $logicHandler = Helper::underToCamel($key) . 'Logic';

            $record[$key] = $this->callStatic($logicHandler, null, [$record]);
            $record = $this->getFieldInfo($record, $key);
        }

        return $record;
    }

    /**
     * 展示列表页
     *
     * @access protected
     * @return object
     */
    protected function showList()
    {
        $this->logReference($this->getControllerName());

        $model = new Main(static::$modelName);
        $get = Yii::$app->request->get();

        $caller = $this->getCaller(2);
        $filter = $this->getFilter($get, $caller);

        $params = [
            'table' => $model->tableName,
            'db' => static::$modelDb
        ];
        $where = $this->getWhereByFilter($filter, $get);
        $where = $this->preHookLogicForWhere($where);

        $condition = $this->callMethod($caller . 'Condition', []);
        if (!Helper::arrayEmpty($where)) {
            $condition['where'] = $where;
        }

        if (empty($condition['size'])) {
            $condition['size'] = Yii::$app->params['pagenum'];
        }

        if (!empty(static::$listFunctionName)) {
            $result = $this->callMethod(static::$listFunctionName, 'function non-exists');
        } else {
            $result = $this->service(static::$listApiName, array_merge($params, $condition, $get));
        }
        if (is_string($result)) {
            $this->error(Yii::t('common', $result));
        }
        list($list, $page) = $result;

        // 分页
        $pagination = new yii\data\Pagination(['totalCount' => $page['totalCount']]);
        $pagination->setPageSize($condition['size']);
        $page = $pagination;

        $assist = $this->handleAssistForList($this->callStatic($caller . 'Assist', []));

        array_walk($list, function (&$value) {
            $value = $this->callMethod('sufHandleField', $value, [
                $value,
                'list'
            ]);
        });

        // 是否为模态框
        $modal = strpos($caller, 'ajaxModal') !== false;

        // 宏操作与单记录操作
        $operation = $this->callStatic($caller . 'Operation');
        $operations = $this->callStatic($caller . 'Operations');

        // 针对记录展示单选框/复选框/无
        $recordFilter = $caller . 'RecordFilter';
        $recordFilter = isset(static::${$recordFilter}) ? static::${$recordFilter} : ($modal ? 'radio' : false);
        $recordFilter = in_array($recordFilter, [
            'checkbox',
            'radio'
        ]) ? $recordFilter : false;

        $recordFilterName = $caller . 'RecordFilterName';
        $recordFilterName = isset(static::${$recordFilterName}) ? static::${$recordFilterName} : $recordFilter;

        // 是否 ajax 分页、ajax 筛选
        $ajaxPage = $ajaxFilter = $modal;

        // 宏操作显示的方位
        $operationsPosition = $caller . 'OperationsPosition';
        $operationsPosition = isset(static::${$operationsPosition}) ? static::${$operationsPosition} : ($modal ? 'bottom' : false);
        $operationsPosition = in_array($operationsPosition, [
            'top',
            'bottom'
        ]) ? $operationsPosition : 'top';

        $params = [
            'page',
            'list',
            'assist',
            'filter',
            'operation',
            'operations',
            'recordFilter',
            'recordFilterName',
            'ajaxPage',
            'ajaxFilter',
            'operationsPosition'
        ];

        return $this->display('//general/list', compact(...$params));
    }

    /**
     * 预览列表
     */
    public function actionIndex()
    {
        return $this->showList();
    }

    /**
     * 展示空表单
     *
     * @access protected
     *
     * @param string $tag
     *
     * @return object
     */
    protected function showForm($tag)
    {
        $this->logReference($this->getControllerName($tag));

        $modelInfo = static::$modelInfo;

        $caller = $this->getCaller(2);
        $assist = $this->callStatic($caller . 'Assist', []);
        $list = $this->handleAssistForForm($assist, [], $tag);
        $view = $this->pageDocuments($tag);

        return $this->display('//general/action', compact('list', 'modelInfo', 'view'));
    }

    /**
     * 新增
     */
    public function actionAdd()
    {
        return $this->showForm('add');
    }

    /**
     * 新增动作
     * @auth-same {static}/add
     */
    public function actionAddForm()
    {
        $model = new Main(static::$modelName);
        $modelInfo = static::$modelInfo;

        $params = array_merge(['table' => $model->tableName], Yii::$app->request->post());
        $params = $this->callMethod('preHandleField', [], [
            $params,
            'add'
        ]);

        if (!empty(static::$addFunctionName)) {
            $result = $this->callMethod(static::$addFunctionName, 'function non-exists');
        } else {
            $result = $this->service(static::$addApiName, $params);
        }

        $key = $this->getControllerName();
        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
            Yii::$app->session->setFlash('list', Yii::$app->request->post());
            $this->goReference($key . '/add');
        }

        Yii::$app->session->setFlash('success', '新增' . $modelInfo . '成功');
        $this->goReference($key);
    }

    /**
     * 展示指定记录表单
     *
     * @access protected
     *
     * @param string $tag
     *
     * @return object
     */
    protected function showFormWithRecord($tag)
    {
        $this->logReference($this->getControllerName($tag));

        $id = Yii::$app->request->get('id');
        $model = new Main(static::$modelName);
        $params = [
            'table' => $model->tableName,
            'where' => [[$model->tableName . '.id' => $id]]
        ];

        $caller = $this->getCaller(2);
        $condition = $this->callMethod($caller . 'Condition', []);

        if (!empty(static::$getFunctionName)) {
            $result = $this->callMethod(static::$getFunctionName, 'function non-exists');
        } else {
            $result = $this->service(static::$getApiName, ArrayHelper::merge($params, $condition));
        }
        if (is_string($result)) {
            $this->error(Yii::t('common', $result));
        }

        $modelInfo = static::$modelInfo;
        $assist = $this->callStatic($caller . 'Assist', []);
        $list = $this->handleAssistForForm($assist, $result, $tag);
        $view = $this->pageDocuments($tag);

        return $this->display('//general/action', compact('id', 'list', 'modelInfo', 'view'));
    }

    /**
     * 编辑
     */
    public function actionEdit()
    {
        return $this->showFormWithRecord('edit');
    }

    /**
     * 编辑动作
     * @auth-same {static}/edit
     */
    public function actionEditForm()
    {
        $model = new Main(static::$modelName);
        $modelInfo = static::$modelInfo;

        $params = array_merge([
            'table' => $model->tableName,
            'where' => [$model->tableName . '.id' => Yii::$app->request->post('id')]
        ], Yii::$app->request->post());
        $params = $this->callMethod('preHandleField', [], [
            $params,
            'edit'
        ]);

        if (!empty(static::$editFunctionName)) {
            $result = $this->callMethod(static::$editFunctionName, 'function non-exists');
        } else {
            $result = $this->service(static::$editApiName, $params);
        }

        $key = $this->getControllerName();
        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
            Yii::$app->session->setFlash('list', Yii::$app->request->post());
            $this->goReference($key . '/edit');
        }

        Yii::$app->session->setFlash('success', '更新' . $modelInfo . '成功');
        $this->goReference($key);
    }

    /**
     * 记录前置
     */
    public function actionFront()
    {
        $model = new Main(static::$modelName);

        if (!empty(static::$frontFunctionName)) {
            $result = $this->callMethod(static::$frontFunctionName, 'function non-exists');
        } else {
            $result = $this->service(static::$frontApiName, [
                'table' => $model->tableName,
                'id' => Yii::$app->request->get('id')
            ]);
        }

        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
        } else {
            Yii::$app->session->setFlash('success', '记录前置成功');
        }

        $this->goReference($this->getControllerName());
    }

    /**
     * 显示/隐藏菜单
     *
     * @auth-pass-all
     */
    public function actionAjaxHideMenu()
    {
        $hide = Yii::$app->request->get('hide');

        $user = Yii::$app->session->get(static::USER);
        $user['hide_menu'] = !!$hide;
        Yii::$app->session->set(static::USER, $user);

        $this->success();
    }
}