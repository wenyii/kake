<?php

namespace backend\controllers;

use common\components\Helper;
use common\controllers\MainController;
use common\models\Main;
use yii;
use yii\helpers\Url;

/**
 * 通用控制器
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
    public static $listApiName = 'general.list-for-backend';
    public static $listFunctionName;

    /**
     * @var string 获取单条接口名
     */
    public static $getApiName = 'general.get-for-backend';
    public static $getFunctionName;

    /**
     * @var string 编辑接口名
     */
    public static $editApiName = 'general.update-for-backend';
    public static $editFunctionName;

    /**
     * @var string 新增接口名
     */
    public static $addApiName = 'general.add-for-backend';
    public static $addFunctionName;

    /**
     * @var string 前置记录接口名
     */
    public static $frontApiName = 'general.front-for-backend';
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

    /**
     * @var array 无需验证权限的控制器
     */
    public static $passAuthCtrl = [
        'MainController',
        'GeneralController'
    ];

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
            'login/ajax-login'
        ])) {
            $this->mustUnLogin();
        } else if (!in_array($router, [
            'general/ajax-sms'
        ])
        ) {
            $this->mustLogin();

            $rootUsers = explode(',', Yii::$app->params['private']['root_user_ids']);
            if (!in_array($this->user->id, $rootUsers)) {
                $auth = $this->authVerify($router);
                if (is_string($auth)) {
                    if (Yii::$app->request->isAjax) {
                        $this->fail($auth);
                    } else {
                        $this->error($auth);
                    }
                }
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
     * @return mixed
     */
    protected function authVerify($router)
    {
        $router = str_replace('.', '/', $router);
        $_router = $router;

        // 首次鉴权
        list($ctrl) = explode('/', $_router);
        $ctrl = Helper::underToCamel($ctrl, false, '-') . 'Controller';
        if (in_array($ctrl, self::$passAuthCtrl)) {
            return true;
        }

        // 二次鉴权
        $authRecord = $this->authRecord($this->user->id);
        if (!empty($authRecord[$_router])) {
            return true;
        }

        // 根据注释完善辅助数据
        $authList = $this->authList();
        $perfectAuthData = function () use (&$authList, &$authRecord, &$router, $_router, &$perfectAuthData) {

            list($class, $method) = explode('/', $router);
            $class = $this->controller($class);
            $method = 'action' . Helper::underToCamel($method, false, '-');

            $classDoc = Yii::$app->reflection->getClassDocument($class);
            $methodDoc = Yii::$app->reflection->getMethodDocument($class, $method);

            if (isset($methodDoc[UserController::$keySame])) {
                $authList[$router] = $classDoc['info'] . ' > ' . $methodDoc['info'];
                $router = str_replace(UserController::$varCtrl, $this->getControllerName(), current($methodDoc[UserController::$keySame]));
                if (!empty($authRecord[$router])) {
                    $authRecord[$_router] = $authRecord[$router];
                }
                $perfectAuthData();
            } else if (isset($methodDoc[UserController::$keyPassRole])) {
                $roles = explode(',', current($methodDoc[UserController::$keyPassRole]));
                $roles = count($roles) == 1 ? ($this->user->role <= current($roles)) : in_array($this->user->role, $roles);
                if ($roles) {
                    $authRecord[$_router] = 1;
                }
            }
        };

        $perfectAuthData();

        // 三次鉴权
        if (empty($authList[$_router]) || !empty($authRecord[$_router])) {
            return true;
        }

        Yii::trace('操作权限鉴定失败: ' . $_router . ' (' . $authList[$_router] . ')');
        $info = Helper::deleteHtml('"' . $authList[$_router] . '" 操作权限不足');

        return $info;
    }

    /**
     * Get the auth list from php file
     *
     * @access public
     *
     * @param boolean $keepModule
     * @param mixed   $roleCtrl
     * @param integer $userRole
     *
     * @return array
     */
    public function authList($keepModule = false, $roleCtrl = null, $userRole = null)
    {
        return $this->cache([
            'controller.auth.list',
            func_get_args()
        ], function () use ($keepModule, $roleCtrl, $userRole) {
            $list = $this->reflectionAuthList($roleCtrl, $userRole);

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
        }, YEAR, null, Yii::$app->params['use_cache']);
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
            func_get_args()
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
        }, YEAR, null, Yii::$app->params['use_cache']);
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
     * @param mixed   $roleCtrl
     * @param integer $userRole
     * @param array   $exceptControllers
     *
     * @return array
     */
    public function reflectionAuthList($roleCtrl = null, $userRole = null, $exceptControllers = [])
    {
        $exceptControllers = array_merge($exceptControllers, self::$passAuthCtrl);
        $directory = Yii::getAlias('@backend') . DS . 'controllers';
        $controllers = Helper::readDirectory($directory, ['php'], 'IN');

        $list = [];

        foreach ($controllers as &$controller) {

            // 处理文件路径
            $controller = Helper::cutString($controller, [
                '/^0^desc',
                '.^0'
            ]);
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

                // 非 http 方法
                if (0 !== strpos($key, 'action') || !preg_match('/^(action)[A-Z]/', $key)) {
                    continue;
                }

                // 手动排除方法
                $action = Helper::camelToUnder(preg_replace('/action/', null, $key, 1), '-');
                if (!empty($classDoc[UserController::$keyInheritExcept]) && in_array($action, $classDoc[UserController::$keyInheritExcept])) {
                    continue;
                }

                // 无需通过后台配置即可决定权限的标示
                if (isset($val[UserController::$keyPassAll]) || isset($val[UserController::$keySame])) {
                    continue;
                }

                // Ajax 操作标题修饰
                if (strpos($action, 'ajax-') === 0) {
                    $style = UserController::$varInfo . ' (<b>Ajax</b>)';
                    if (empty($val[UserController::$keyInfoStyle])) {
                        $val[UserController::$keyInfoStyle] = [$style];
                    } else {
                        $val[UserController::$keyInfoStyle] = [str_replace(UserController::$varInfo, current($val[UserController::$keyInfoStyle]), $style)];
                    }
                }

                // 普通操作标题修饰
                if (!empty($val[UserController::$keyInfoStyle])) {
                    $val['info'] = str_replace(UserController::$varInfo, $val['info'], current($val[UserController::$keyInfoStyle]));
                }

                $roles = explode(',', empty($val[UserController::$keyPassRole]) ? $roleCtrl : current($val[UserController::$keyPassRole]));
                $roles = count($roles) == 1 ? ($userRole <= current($roles)) : in_array($userRole, $roles);

                if (!$roleCtrl || $roles) {
                    $controller = Helper::camelToUnder(str_replace('Controller', null, $controller), '-');
                    if (empty($val['info'])) {
                        $this->error("${controller}/${action} 未规范注释，无法纳入权限控制");
                    }
                    $self[] = [
                        'info' => $val['info'],
                        'controller' => $controller,
                        'action' => $action
                    ];
                }
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
     *
     * @param boolean $hideMenu
     *
     * @return void
     */
    public function commonParams($hideMenu = false)
    {
        Yii::$app->view->params['user_info'] = $this->user;
        $menu = $hideMenu ? [] : Yii::$app->params['menu'];

        foreach ($menu as $key => &$item) {

            $roles = empty($item['pass_role']) ? [1] : (array) $item['pass_role'];
            if (count($roles) == 1) {
                $rolesShow = $this->user->role <= current($roles);
            } else {
                $rolesShow = in_array($this->user->role, $roles);
            }
            if (!$rolesShow) {
                unset($menu[$key]);
                continue;
            }

            $routers = [];
            foreach ($item['sub'] as $router => &$page) {
                if (is_array($page)) {
                    $_roles = empty($page['pass_role']) ? $roles : (array) $page['pass_role'];
                    $params = empty($page['params']) ? [] : (array) $page['params'];
                    $page = $page['name'];
                } else {
                    $_roles = $roles;
                    $params = [];
                }
                if (count($_roles) == 1) {
                    $_rolesShow = $this->user->role <= current($_roles);
                } else {
                    $_rolesShow = in_array($this->user->role, $_roles);
                }
                if (!$_rolesShow) {
                    unset($item['sub'][$router]);
                    continue;
                }

                list($controller, $action) = explode('.', $router);
                if (!in_array($controller, $routers)) {
                    $routers[] = $controller . '/' . $action;
                }

                $page = [
                    'title' => $page,
                    'controller' => $controller,
                    'action' => $action,
                    'params' => $params
                ];
            }
            $item['router'] = $routers;
        }

        Yii::$app->view->params['menu'] = $menu;

        $hideMenu = isset($this->user->hide_menu) ? $this->user->hide_menu : false;
        Yii::$app->view->params['hidden_menu'] = $hideMenu;
    }

    /**
     * @inheritDoc
     */
    public function error($message, $code = null, $trace = null)
    {
        $this->sourceCss = false;
        $this->commonParams(true);
        parent::error($message, $code, $trace);
    }

    /**
     * 渲染页面
     *
     * @access public
     *
     * @param string $view
     * @param array  $params
     *
     * @return bool|string
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

            return $this->modal($view, $params, $title);
        }

        return $this->render($view, $params);
    }

    /**
     * 渲染模态框
     *
     * @access public
     *
     * @param string $view
     * @param array  $params
     * @param string $title
     *
     * @return bool
     */
    public function modal($view, $params = [], $title = null)
    {
        $tpl = Yii::$app->getViewPath() . DS . ltrim($view, '/') . '.php';
        $content = $this->renderFile($tpl, $params);
        $this->success([
            'title' => $title,
            'message' => $content
        ]);

        return true;
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
                    $_value[$v] = true;
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
                $_value['price'] = true;
            }

            if (isset($_value['price'])) {
                if (!is_numeric($_value['price'])) {
                    $_value['price'] = 2;
                }

                if (!isset($_value['tpl'])) {
                    $_value['tpl'] = '￥%s';
                }
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
     * 处理下拉框的数据
     *
     * @access private
     *
     * @param array   $value
     * @param object  $model
     * @param string  $key
     * @param array   $default
     * @param boolean $addAll
     *
     * @return void
     */
    private function handleSelectList(&$value, $model, $key, $default, $addAll = false)
    {
        $valued = isset($value['value']) ? $value['value'] : null;

        $list = empty($value['list']) ? null : (array) $value['list'];
        $list = $this->getEnumerate($model, $key, $list);

        if ($addAll) {
            $list['all'] = 'All';
        }

        $value['value'] = [
            'list' => $list,
            'name' => $key,
            'selected' => Helper::issetDefault($default, $key, $valued)
        ];
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
    public function handleAssistForForm($assist, &$default = [], $action = null)
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
                case 'radio' :
                case 'checkbox' :
                    $this->handleSelectList($value, $model, $key, $default);
                    break;

                case 'input' :
                default:
                    empty($value['type']) && $value['type'] = 'text';

                    if ($value['type'] == 'file') {
                        $value['rules_info'] = $model->_upload_rules;
                    }

                    if (!empty($value['value_key'])) {
                        if (isset($default[$value['value_key']])) {
                            $value['value'] = $default[$value['value_key']];
                        }
                    } else if (isset($default[$key])) {
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
        $caller .= 'Filter';
        $filter = $this->callStatic($caller);
        if (!$filter) {
            return [];
        }

        $model = new Main(static::$modelName);
        $labels = $model->attributeLabels();
        $_filter = [];

        $get = $this->callMethod('sufHandleField', $get, [
            $get,
            $caller
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

            $field = isset($value['field']) ? $value['field'] : $key;
            if (empty($value['title'])) {
                $_labels = $labels;
                if (isset($value['table'])) {
                    $_labels = Helper::singleton('model.' . $value['table'], function () use ($value) {
                        return new Main($value['table']);
                    })->attributeLabels();
                }
                $value['title'] = isset($_labels[$field]) ? $_labels[$field] : Yii::t('common', $key);
            }

            $table = isset($value['table']) ? $value['table'] : $model->tableName;
            $value['name'] = $table . '.' . $field;

            switch ($value['elem']) {
                case 'select' :
                case 'radio' :
                case 'checkbox' :
                    $this->handleSelectList($value, $model, $key, $get, true);
                    break;

                case 'input' :
                default:
                    empty($value['type']) && $value['type'] = 'text';

                    if (!empty($get[$key])) {
                        $value['value'] = htmlspecialchars_decode($get[$key]);
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
     * @param array  $filter
     * @param array  $default
     * @param string $caller
     *
     * @return array
     */
    public function getWhereByFilter($filter, $default, $caller)
    {
        $default = $this->callMethod('preHandleField', $default, [
            $default,
            $caller
        ]);

        $where = [];
        foreach ($filter as $name => $item) {

            $field = $item['name'];
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
     * @access public
     *
     * @param array $record
     *
     * @return array
     */
    public function preHookDateSection($record)
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
     * @access public
     *
     * @param array  $record
     * @param string $action
     *
     * @return array
     * @throws \Exception
     */
    public function preHookOrderAttachment($record, $action)
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
     * @access public
     *
     * @param array  $record
     * @param string $action
     *
     * @return array
     * @throws \Exception
     */
    public function preHookOrderTag($record, $action)
    {
        if (empty($action)) {
            return $record;
        }

        // 附件秩序化
        $assist = $this->callStatic($action . 'Assist', []);

        foreach ($assist as $item) {

            if (!isset($item['elem']) || $item['elem'] != 'tag') {
                continue;
            }

            if (empty($item['field_name'])) {
                throw new \Exception('Key `field_name` is required.');
            }

            if (empty($item['table'])) {
                throw new \Exception('Key `table` is required.');
            }

            if (empty($item['foreign_key'])) {
                throw new \Exception('Key `foreign_key` is required.');
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
                    $controller = $this->controller($controller, Yii::$app->id, false);
                }

                if (empty($record[$newKey])) {
                    $tagRecord['add'] = [];
                } else {
                    foreach ($record[$newKey] as $query) {
                        parse_str($query, $tagData);
                        $tagData = $this->callMethod('preHandleField', $tagData, [
                            $tagData,
                            $action
                        ], $controller);
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
     * @access public
     *
     * @param array $record
     *
     * @return array
     */
    public function preHookDateSectionDouble($record)
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
     * @access public
     *
     * @param array   $record
     * @param integer $multiple
     *
     * @return array
     */
    public function preHookPriceNumber($record, $multiple = 100)
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
     * @access public
     *
     * @param array $record
     *
     * @return array
     */
    public function preHookUbbAndHtml($record)
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
     * @access public
     *
     * @param array $where
     *
     * @return array
     */
    public function preHookLogicForWhere($where)
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
            if (strpos($_key, '.')) {
                $_key = explode('.', $_key)[1];
            }
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
     * @access public
     *
     * @param array $record
     *
     * @return array
     */
    public function sufHookDateSection($record)
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
     * @access public
     *
     * @param array  $record
     * @param string $action
     *
     * @return array
     */
    public function sufHookDateSectionDouble($record, $action)
    {
        if ($action != 'edit') {
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
     * @access public
     *
     * @param array   $record
     * @param integer $multiple
     *
     * @return array
     */
    public function sufHookPriceNumber($record, $multiple = 100)
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
     * @access public
     *
     * @param array $record
     *
     * @return array
     */
    public function sufHookUbbAndHtml($record)
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
     * @access public
     *
     * @param array  $record
     * @param string $action
     *
     * @return array
     */
    public function sufHookLogic($record, $action)
    {
        if (strpos($action, 'Filter') !== false) {
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
     * @access public
     *
     * @param string  $caller
     * @param boolean $returnList
     * @param boolean $logReference
     *
     * @return mixed
     */
    public function showList($caller = null, $returnList = false, $logReference = true)
    {
        $caller = $caller ?: $this->getCaller(2);
        if ($logReference) {
            $this->logReference($this->getControllerName($caller));
        }

        $condition = $this->callMethod($caller . 'Condition', []);
        if (empty($condition['size'])) {
            $condition['size'] = Yii::$app->params['pagenum'];
        }

        if (!empty(static::$listFunctionName)) {
            $result = $this->callMethod(static::$listFunctionName, 'function non-exists');
        } else {

            $get = Yii::$app->request->get();
            $filter = $this->getFilter($get, $caller);

            $where = $this->getWhereByFilter($filter, $get, $caller);
            $where = $this->preHookLogicForWhere($where);
            if (!Helper::arrayEmpty($where)) {
                $_where = empty($condition['where']) ? [] : $condition['where'];
                $condition['where'] = array_merge($_where, $where);
            }

            $model = new Main(static::$modelName);
            $params = [
                'table' => $model->tableName,
                'db' => static::$modelDb
            ];
            $params = array_merge($params, $condition, $get);
            $result = $this->service(static::$listApiName, $params);
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

        if (!empty($list)) {
            $list = $this->callMethod('sufHandleListBeforeField', $list, [
                $list,
                $caller
            ]);

            array_walk($list, function (&$value) use ($caller) {
                $value = $this->callMethod('sufHandleField', $value, [
                    $value,
                    $caller
                ]);
            });

            $list = $this->callMethod('sufHandleListAfterField', $list, [
                $list,
                $caller
            ]);
        }

        if ($returnList) {
            return $list;
        }

        // 是否为模态框
        $modal = strpos($caller, 'ajaxModal') !== false;

        // 宏操作与单记录操作
        $operation = $this->callStatic($caller . 'Operation');
        $operations = $this->callStatic($caller . 'Operations');

        $isset = function ($var, $default = null) {
            if (isset(static::${$var})) {
                return static::${$var};
            }

            return $default;
        };

        // 针对记录展示单选框/复选框/无
        $recordFilter = $caller . 'RecordFilter';
        $recordFilter = $isset($recordFilter, $modal ? 'radio' : false);
        $recordFilter = in_array($recordFilter, [
            'checkbox',
            'radio'
        ]) ? $recordFilter : false;

        $recordFilterName = $caller . 'RecordFilterName';
        $recordFilterName = $isset($recordFilterName, $recordFilter);

        $recordFilterValueName = $caller . 'RecordFilterValueName';
        $recordFilterValueName = $isset($recordFilterValueName, 'id');

        // 是否 ajax 分页、ajax 筛选
        $ajaxPage = $ajaxFilter = $modal;

        // 宏操作显示的方位
        $operationsPosition = $caller . 'OperationsPosition';
        $operationsPosition = $isset($operationsPosition, $modal ? 'bottom' : false);
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
            'recordFilterValueName',
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
     * @access public
     *
     * @param string $caller
     *
     * @return mixed
     */
    public function showForm($caller = null)
    {
        $caller = $caller ?: $this->getCaller(2);
        $this->logReference($this->getControllerName($caller));

        $modelInfo = static::$modelInfo;

        $assist = $this->callStatic($caller . 'Assist', []);
        $default = [];
        $list = $this->handleAssistForForm($assist, $default, $caller);
        $view = $this->pageDocuments($caller);

        return $this->display('//general/action', compact('list', 'modelInfo', 'view'));
    }

    /**
     * 新增
     */
    public function actionAdd()
    {
        return $this->showForm();
    }

    /**
     * 新增动作
     * @auth-same {ctrl}/add
     *
     * @param array  $reference
     * @param string $action
     * @param array  $post
     * @param string $caller
     */
    public function actionAddForm($reference = null, $action = 'add', $post = null, $caller = null)
    {
        if (is_string($reference)) {
            $reference = [
                'fail' => $reference,
                'success' => $reference
            ];
        }
        $modelInfo = static::$modelInfo;

        if (!$action) {
            $action = str_replace('Form', '', $caller ?: $this->getCaller(2));
            $action = Helper::camelToUnder($action, '-');
        }

        $post = $post ?: Yii::$app->request->post();

        if (!empty(static::$addFunctionName)) {
            $result = $this->callMethod(static::$addFunctionName, 'function non-exists');
        } else {
            $model = new Main(static::$modelName);

            $params = array_merge(['table' => $model->tableName], $post);
            $params = $this->callMethod('preHandleField', [], [
                $params,
                $action
            ]);
            $result = $this->service(static::$addApiName, $params);
        }

        $key = $this->getControllerName();
        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
            Yii::$app->session->setFlash('list', $post);
            $this->goReference($reference ? $reference['fail'] : "${key}/${action}");
        }

        Yii::$app->session->setFlash('success', '新增' . $modelInfo . '成功');
        $this->goReference($reference ? $reference['success'] : "${key}/index");
    }

    /**
     * 展示指定记录表单
     *
     * @access public
     *
     * @param array   $where
     * @param string  $caller
     * @param boolean $returnRecord
     * @param boolean $logReference
     *
     * @return mixed
     */
    public function showFormWithRecord($where = [], $caller = null, $returnRecord = false, $logReference = true)
    {
        $caller = $caller ?: $this->getCaller(2);
        if ($logReference) {
            $this->logReference($this->getControllerName($caller));
        }

        if (!empty(static::$getFunctionName)) {
            $result = $this->callMethod(static::$getFunctionName, 'function non-exists');
        } else {
            $id = Yii::$app->request->get('id');
            $condition = $this->callMethod($caller . 'Condition', []);

            $model = new Main(static::$modelName);
            $condition['table'] = $model->tableName;
            if (!$where) {
                $where = [[$model->tableName . '.id' => $id]];
            } else if (count($where) == count($where, 1)) { // 一维转二维
                $where = [$where];
            }
            $_where = empty($condition['where']) ? [] : $condition['where'];
            $condition['where'] = array_merge($_where, $where);

            $result = $this->service(static::$getApiName, $condition);
        }

        if (is_string($result)) {
            $this->error(Yii::t('common', $result));
        }

        $modelInfo = static::$modelInfo;
        $assist = $this->callStatic($caller . 'Assist', []);
        $list = $this->handleAssistForForm($assist, $result, $caller);

        if ($returnRecord) {
            return $result;
        }

        $view = $this->pageDocuments($caller);

        // 单记录操作
        $operation = $this->callStatic($caller . 'Operation');

        return $this->display('//general/action', compact('id', 'list', 'result', 'modelInfo', 'view', 'operation'));
    }

    /**
     * 编辑
     */
    public function actionEdit()
    {
        return $this->showFormWithRecord();
    }

    /**
     * 编辑动作
     * @auth-same {ctrl}/edit
     *
     * @param array  $reference
     * @param string $action
     * @param array  $post
     * @param string $caller
     */
    public function actionEditForm($reference = null, $action = 'edit', $post = null, $caller = null)
    {
        if (is_string($reference)) {
            $reference = [
                'fail' => $reference,
                'success' => $reference
            ];
        }
        $modelInfo = static::$modelInfo;

        if (!$action) {
            $action = str_replace('Form', '', $caller ?: $this->getCaller(2));
            $action = Helper::camelToUnder($action, '-');
        }

        $post = $post ?: Yii::$app->request->post();

        if (!empty(static::$editFunctionName)) {
            $result = $this->callMethod(static::$editFunctionName, 'function non-exists');
        } else {
            $model = new Main(static::$modelName);
            $params = array_merge([
                'table' => $model->tableName,
                'where' => [$model->tableName . '.id' => $post['id']]
            ], $post);
            $params = $this->callMethod('preHandleField', [], [
                $params,
                $action
            ]);
            $result = $this->service(static::$editApiName, $params);
        }

        $key = $this->getControllerName();
        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
            Yii::$app->session->setFlash('list', $post);
            $this->goReference($reference ? $reference['fail'] : "${key}/${action}");
        }

        Yii::$app->session->setFlash('success', '更新' . $modelInfo . '成功');
        $this->goReference($reference ? $reference['success'] : "${key}/index");
    }

    /**
     * 记录前置
     *
     * @param string $reference
     */
    public function actionFront($reference = null)
    {
        if (!empty(static::$frontFunctionName)) {
            $result = $this->callMethod(static::$frontFunctionName, 'function non-exists');
        } else {
            $model = new Main(static::$modelName);
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

        $this->goReference($reference ?: $this->getControllerName('index'));
    }

    /**
     * 裁切图片
     *
     * @auth-pass-all
     */
    public function actionAjaxModalCrop()
    {
        $options = Yii::$app->request->post();
        $view['title_info'] = '图片选区';

        return $this->display('general/crop', compact('options', 'view'));
    }

    /**
     * 保存裁切的图片
     *
     * @auth-pass-all
     */
    public function actionAjaxSaveCrop()
    {
        $base64 = Yii::$app->request->post('base64');
        $url = Yii::$app->request->post('url');
        $url = str_replace(Yii::$app->params['upload_url'], null, $url);
        $file = Yii::$app->params['upload_path'] . $url;

        $base64 = preg_replace('/^(data:\s*image\/(\w+);base64,)/', '', $base64);
        @file_put_contents($file, base64_decode($base64));
        $this->success();
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