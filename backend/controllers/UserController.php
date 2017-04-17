<?php

namespace backend\controllers;

use common\components\Helper;
use Yii;

/**
 * 用户管理
 *
 * @auth-inherit-except front
 */
class UserController extends GeneralController
{
    // 模型
    public static $modelName = 'User';

    // 模型描述
    public static $modelInfo = '用户';

    /**
     * @inheritDoc
     */
    public static function indexOperations()
    {
        return [
            [
                'text' => '新增用户',
                'value' => 'user/add',
                'icon' => 'plus'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public static function indexOperation()
    {
        return array_merge(parent::indexOperation(), [
            [
                'text' => '配置权限',
                'value' => 'edit-auth',
                'level' => 'info',
                'icon' => 'cog',
                'show_condition' => function ($record) {
                    return !!$record['role'];
                }
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public static function indexFilter()
    {
        return [
            'username' => 'input',
            'real_name' => 'input',
            'phone' => 'input',
            'role' => [
                'value' => 'all'
            ],
            'sex' => [
                'value' => 'all'
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
                'value' => 'all'
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
                'width' => '50px'
            ],
            'username',
            'real_name' => [
                'empty',
                'tip'
            ],
            'phone',
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
    public static function editAssist($action = null)
    {
        return [
            'username' => [
                'placeholder' => '建议填写'
            ],
            'real_name' => [
                'placeholder' => '建议填写'
            ],
            'phone',
            'role' => [
                'elem' => 'select',
                'value' => 0,
                'tip' => '管理员可登录后台'
            ],
            'openid' => [
                'label' => 4,
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
     * 编辑
     *
     * @inheritDoc
     * @auth-info-style <span class="text-danger">{info}</span>
     */
    public function actionEdit()
    {
        return parent::actionEdit();
    }

    /**
     * 新增
     *
     * @inheritDoc
     * @auth-same user/edit
     */
    public function actionAdd()
    {
        return parent::actionAdd();
    }

    /**
     * 权限编辑
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
        $authList = $this->authList(true);
        $authRecord = array_keys($this->authRecord($userId));

        return $this->display('auth', [
            'user_id' => $userId,
            'list' => $authList,
            'record' => $authRecord
        ]);
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
            $this->goReference($this->getControllerName());
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
        $this->goReference($this->getControllerName());
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