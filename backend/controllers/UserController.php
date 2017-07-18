<?php

namespace backend\controllers;

use common\components\Helper;
use Yii;

/**
 * 用户管理
 *
 * @auth-inherit-except add
 * @auth-inherit-except front
 */
class UserController extends GeneralController
{
    // 模型
    public static $modelName = 'User';

    // 模型描述
    public static $modelInfo = '用户';

    /**
     * @var string 模态框的名称
     */
    public static $ajaxModalListTitle = '选择用户';

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function indexOperation()
    {
        return array_merge(parent::indexOperation(), [
            [
                'text' => '同步',
                'value' => 'sync-user',
                'level' => 'success',
                'icon' => 'retweet',
                'params' => ['openid']
            ],
            [
                'text' => '配置权限',
                'value' => 'edit-auth',
                'level' => 'info',
                'icon' => 'cog',
                'show_condition' => function ($record) {
                    return $record['role'] == 1;
                }
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function ajaxModalListOperations()
    {
        return [
            [
                'text' => '选定',
                'script' => true,
                'value' => '$.modalRadioValueToInput("radio", "producer_id")',
                'icon' => 'flag'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'username' => 'input',
            'phone' => 'input',
            'role' => [
                'value' => parent::SELECT_KEY_ALL
            ],
            'sex' => [
                'value' => parent::SELECT_KEY_ALL
            ],
            'country' => 'input',
            'province' => 'input',
            'city' => 'input',
            'add_time' => [
                'elem' => 'input',
                'type' => 'date',
                'between' => true
            ],
            'state' => [
                'value' => parent::SELECT_KEY_ALL
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexSorter()
    {
        return [
            'id',
            'username',
            'update_time',
            'state'
        ];
    }

    /**
     * @inheritDoc
     */
    public static function ajaxModalListFilter()
    {
        return [
            'username' => 'input',
            'phone' => 'input',
            'role' => [
                'value' => parent::SELECT_KEY_ALL
            ],
            'sex' => [
                'value' => parent::SELECT_KEY_ALL
            ],
            'state' => [
                'value' => parent::SELECT_KEY_ALL
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexAssist()
    {
        return [
            'id' => 'code',
            'head_img_url' => [
                'img',
                'width' => '64px'
            ],
            'username',
            'phone' => 'empty',
            'role' => 'info',
            'sex' => [
                'code',
                'color' => [
                    0 => 'default',
                    1 => 'primary',
                    2 => 'danger'
                ],
                'info'
            ],
            'address' => [
                'title' => '地址',
                'tip'
            ],
            'update_time',
            'state' => [
                'code',
                'color' => 'auto',
                'info'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function ajaxModalListAssist()
    {
        return [
            'username',
            'phone' => 'empty',
            'role' => 'info',
            'sex' => [
                'code',
                'color' => [
                    0 => 'default',
                    1 => 'primary',
                    2 => 'danger'
                ],
                'info'
            ],
            'state' => [
                'code',
                'color' => 'auto',
                'info'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function editAssist($action = null)
    {
        return [
            'username' => [
                'placeholder' => '建议填写'
            ],
            'phone',
            'role' => [
                'elem' => 'select',
                'value' => 0,
                'tip' => [
                    '普通用户' => '无法登录后台',
                    '管理员' => '管理整个后台',
                    '分销商' => '管理个人分销系统'
                ]
            ],
            'openid' => [
                'label' => 4,
                'readonly' => true
            ],
            'sex' => [
                'elem' => 'select'
            ],
            'country',
            'province',
            'city',
            'head_img_url' => [
                'label' => 4,
            ],
            'state' => [
                'elem' => 'select',
                'value' => 1
            ]
        ];
    }

    /**
     * @auth-pass-all
     */
    public function actionAjaxModalList()
    {
        return $this->showList();
    }

    /**
     * 编辑 (危险)
     *
     * @inheritDoc
     * @auth-info-style <span class="text-danger">{info}</span>
     */
    public function actionEdit()
    {
        return parent::showFormWithRecord();
    }

    /**
     * 权限编辑 (危险)
     *
     * @access          public
     * @auth-info-style <span class="text-danger">{info}</span>
     */
    public function actionEditAuth()
    {
        $userId = Yii::$app->request->get('id');
        if (empty($userId)) {
            $this->error('用户ID参数未指定');
        }

        $authList = $this->getAuthList(true);
        $authRecord = array_keys($this->getAuthRecord($userId));
        $admin = $this->listAdmin();

        return $this->display('auth', [
            'user_id' => $userId,
            'list' => $authList,
            'record' => $authRecord,
            'admin' => $admin
        ]);
    }

    /**
     * 获取管理员的权限列表
     *
     * @access    public
     * @auth-same user/edit-auth
     *
     * @param integer $id
     *
     * @return void
     */
    public function actionAjaxGetUserAuth($id)
    {
        $this->success($this->getAuthRecord($id));
    }

    /**
     * 权限编辑动作
     *
     * @access    public
     * @auth-same user/edit-auth
     */
    public function actionEditAuthForm()
    {
        $oldAuth = Yii::$app->request->post('old_auth');
        $oldAuth = empty($oldAuth) ? [] : explode(',', $oldAuth);
        $nowAuth = Yii::$app->request->post('new_auth', []);

        $result = Helper::getDiffWithAction($oldAuth, $nowAuth);
        if (!$result) {
            Yii::$app->session->setFlash('warning', '权限配置未曾变化');
            $this->goReference($this->getControllerName('index'));
        }

        list($add, $del) = $result;
        $result = $this->service('user.edit-auth', [
            'user_id' => Yii::$app->request->post('user_id'),
            'add' => $add,
            'del' => $del
        ]);

        if (is_string($result)) {
            $this->error(Yii::t('common', $result));
        }

        Yii::$app->session->setFlash('success', '权限配置成功');
        $this->goReference($this->getControllerName('index'));
    }

    /**
     * 同步用户信息
     *
     * @access public
     *
     * @param string $openid
     */
    public function actionSyncUser($openid)
    {
        $user = Yii::$app->wx->user->get($openid);

        $key = $this->getControllerName('index');
        if (!isset($user->nickname)) {
            Yii::$app->session->setFlash('info', '该用户未关注公众号，无法同步');
            $this->goReference($key);
        }

        $result = $this->service(static::$editApiName, [
            'table' => 'user',
            'where' => ['openid' => $openid],
            'username' => $user['nickname'],
            'sex' => $user['sex'],
            'city' => $user['city'],
            'province' => $user['province'],
            'country' => $user['country'],
            'head_img_url' => $user['headimgurl'],
        ]);

        if (is_string($result)) {
            Yii::$app->session->setFlash('danger', Yii::t('common', $result));
            $this->goReference($key);
        }

        Yii::$app->session->setFlash('success', '同步用户信息成功');
        $this->goReference($key);
    }

    /**
     * @inheritDoc
     */
    public function sufHandleField($record, $action = null, $callback = null)
    {
        if (!empty($record['country'])) {
            $record['address'] = Helper::joinString('-', $record['country'], $record['province'], $record['city']);
        }

        return parent::sufHandleField($record, $action);
    }
}