<?php

namespace frontend\controllers;

use common\components\Helper;
use common\controllers\MainController;
use yii\helpers\ArrayHelper;
use yii;
use yii\helpers\Url;

/**
 * General controller
 */
class GeneralController extends MainController
{
    /**
     * @cont string user info key
     */
    const USER = 'frontend_user_info';

    /**
     * @cont string reference
     */
    const REFERENCE = 'frontend_reference';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        Yii::trace('设置语言包');
        if (Yii::$app->session->has(self::LANGUAGE)) {
            Yii::$app->language = Yii::$app->session->get(self::LANGUAGE);
        }

        Yii::trace('获取用户信息');
        if (!$this->user && Yii::$app->session->has(self::USER)) {
            $this->user = (object) Yii::$app->session->get(self::USER);
        }

        $this->weChatLogic();
    }

    /**
     * 微信相关的业务逻辑
     *
     * @access public
     * @return void
     */
    public function weChatLogic()
    {
        $wx = Yii::$app->wx;

        if (!Yii::$app->request->get('code')) {
            return;
        }

        if (!$this->user) {
            $result = $wx->user();
            $result = $this->service('user.get-with-we-chat', $result);
            if (is_string($result)) {
                $this->error(Yii::t('common', $result));
            }

            $this->loginUser($result, isset($result['state']) ? 'we-chat-login' : 'we-chat-bind');
        }
    }

    /**
     * 清理缓存
     */
    public function actionClearFrontend()
    {
        $token = Yii::$app->request->get('token');

        if (!$token || $token != strrev(md5(Yii::$app->params['socks_token']))) {
            $this->fail([
                'param illegal',
                'param' => 'token'
            ]);
        }

        Yii::$app->cache->flush();
        $this->success();
    }

    /**
     * 用户登录
     *
     * @access public
     *
     * @param array  $user
     * @param string $type
     * @param string $system
     *
     * @return void
     */
    public function loginUser($user, $type = 'login', $system = 'kake')
    {
        Yii::trace("将用户信息设置到 Session 中 - 来自 <{$system}> 系统的 <{$type}> 类型登录");

        Yii::$app->session->set(self::USER, $user);
        $this->user = (object) array_merge((array) $this->user, $user);

        $this->service('user.login-log', [
            'id' => $user['id'],
            'ip' => Yii::$app->request->userIP,
            'type' => $type
        ]);
    }

    /**
     * 需要登录
     *
     * @access public
     * @return mixed
     */
    public function mustLogin()
    {
        if (!$this->user || !isset($this->user->openid)) {
            Yii::$app->wx->config('oauth.callback', $this->currentUrl());

            return Yii::$app->wx->auth();
        }

        return true;
    }

    /**
     * 创建安全链接
     *
     * @access protected
     *
     * @param mixed  $params
     * @param string $router
     * @param boolean $checkUser
     *
     * @return \yii\web\Response
     */
    protected function createSafeLink($params, $router, $checkUser = true)
    {
        $item = [
            'item' => $params,
            'ip' => Yii::$app->request->userIP
        ];

        if ($checkUser) {
            $item['user_id'] = $this->user->id;
        }

        $item = Helper::createSign($item, 'sign');
        $item = Yii::$app->rsa->encryptByPublicKey(json_encode($item));

        return $this->redirect([
            $router,
            'safe' => $item
        ]);
    }

    /**
     * 验证安全链接
     *
     * @access protected
     *
     * @param boolean $checkUser
     *
     * @return mixed
     */
    protected function validateSafeLink($checkUser = true)
    {
        $item = Yii::$app->request->get('safe');
        $item = json_decode(Yii::$app->rsa->decryptByPrivateKey($item), true);
        if (!$item) {
            $error = true;
        }

        if (!Helper::validateSign($item, 'sign')) {
            $error = true;
        }

        if ($checkUser && $this->user->id != $item['user_id']) {
            $error = true;
        }

        if (Yii::$app->request->userIP != $item['ip']) {
            $error = true;
        }

        if (!empty($error)) {
            Yii::error('支付链接异常: ' . json_encode($item, JSON_UNESCAPED_UNICODE));
            $this->error(Yii::t('common', 'payment link illegal'));
        }

        return $item['item'];
    }

    /**
     * 获取产品详情
     *
     * @access public
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function getProduct($id)
    {
        $id = (int) $id;
        if (empty($id)) {
            $this->error(Yii::t('common', 'product id required'));
        }

        $controller = $this->controller('product');
        $condition = $this->callMethod('editCondition', [], null, $controller);

        $condition = ArrayHelper::merge($condition, [
            'where' => [
                ['product.id' => $id],
                ['product.state' => 1],
            ]
        ]);

        $detail = $this->service('product.detail', $condition);
        if (empty($detail)) {
            return false;
        }

        $detail = $this->callMethod('sufHandleField', $detail, [
            $detail,
            'detail'
        ], $controller);

        if (!empty($detail)) {
            $detail['min_price'] = min(array_column($detail['package'], 'price'));
        }

        return $detail;
    }

    /**
     * 列表产品焦点图
     *
     * @access public
     *
     * @param integer $limit
     *
     * @return array
     */
    public function listProductFocus($limit)
    {
        return $this->cache([
            'list-focus',
            $limit
        ], function () use ($limit) {
            $controller = $this->controller('product');
            $condition = [
                'join' => [
                    [
                        'table' => 'attachment',
                        'as' => 'cover',
                        'left_on_field' => 'attachment_cover',
                    ],
                ],
                'select' => [
                    'cover.deep_path AS cover_deep_path',
                    'cover.filename AS cover_filename',
                    'product.id',
                    'product.attachment_cover'
                ],
                'order' => 'product.top DESC, product.update_time DESC',
                'where' => [
                    ['product.manifestation' => 1],
                    ['product.state' => 1]
                ],
                'limit' => $limit,
            ];
            $list = $this->service('product.list', $condition);
            array_walk($list, function (&$item) use ($controller) {
                $item = $this->createAttachmentUrl($item, ['attachment_cover' => 'cover']);
            });

            return $list;
        }, DAY);
    }

    /**
     * 列表产品
     *
     * @access public
     *
     * @param integer $page
     * @param integer $page_size
     * @param integer $manifestation
     * @param integer $classify
     * @param boolean $sale
     * @param string  $keyword
     *
     * @return array
     */
    public function listProduct($page = 1, $page_size = null, $manifestation = null, $classify = null, $sale = null, $keyword = null)
    {
        list($offset, $limit) = Helper::page($page, $page_size ?: Yii::$app->params['items_page_size']);
        $params = compact('manifestation', 'classify', 'sale', 'keyword', 'limit', 'offset');

        return $this->cache([
            'list-product',
            func_get_args()
        ], function () use ($params) {

            $controller = $this->controller('product');

            $list = $this->service('product.simple-list', $params);
            array_walk($list, function (&$item) use ($controller) {
                $item = $this->createAttachmentUrl($item, ['attachment_cover' => 'cover']);
            });

            return $list;
        }, DAY);
    }

    /**
     * 列表产品套餐
     *
     * @access public
     *
     * @param integer $product_id
     *
     * @return array
     */
    public function listProductPackage($product_id)
    {
        $product_id = (int) $product_id;
        if (empty($product_id)) {
            $this->error(Yii::t('common', 'product package id required'));
        }

        $list = $this->service('product.package-list', ['product_id' => $product_id]);

        $controller = $this->controller('product-package');
        array_walk($list, function (&$item) use ($controller) {
            $item = $this->callMethod('sufHandleField', $item, [$item], $controller);
        });

        return array_combine(array_column($list, 'id'), $list);
    }

    /**
     * 获取子订单详情
     *
     * @access public
     *
     * @param integer $id
     *
     * @return array
     */
    public function getOrderSub($id)
    {
        $id = (int) $id;
        if (empty($id)) {
            $this->error(Yii::t('common', 'order sub id required'));
        }

        $condition = OrderController::$orderSubCondition;
        $condition['where'] = [
            ['order_sub.id' => $id]
        ];
        $detail = $this->service('order.detail', $condition);

        $controller = $this->controller('order-sub');
        $detail = $this->callMethod('sufHandleField', $detail, [
            $detail,
            'detail'
        ], $controller);
        $detail = $this->createAttachmentUrl($detail, ['attachment_cover' => 'cover']);

        return $detail;
    }

    /**
     * 列表子订单
     *
     * @access public
     *
     * @param integer $page
     * @param mixed   $state
     *
     * @return array
     */
    public function listOrderSub($page = 1, $state = null)
    {
        $where = [
            ['order.user_id' => $this->user->id]
        ];

        if (is_numeric($state)) {
            $where[] = ['order_sub.state' => $state];
        } else if (is_array($state)) {
            $where[] = [
                'in',
                'order_sub.state',
                $state
            ];
        }

        $condition = OrderController::$orderSubCondition;
        $condition['where'] = $where;
        list($condition['offset'], $condition['limit']) = Helper::page($page, Yii::$app->params['items_page_size']);

        $list = $this->service('order.list', $condition);

        $controller = $this->controller('order');
        array_walk($list, function (&$item) use ($controller) {
            $item = $this->callMethod('sufHandleField', $item, [$item], $controller);
            $item = $this->createAttachmentUrl($item, ['attachment_cover' => 'cover']);
        });

        return $list;
    }

    /**
     * 列表广告
     *
     * @access public
     *
     * @param mixed $limit
     *
     * @return array
     */
    public function listBanner($limit = null)
    {
        return $this->cache([
            'list-banner',
            $limit
        ], function () use ($limit) {
            $controller = $this->controller('ad');
            $condition = $this->callMethod('editCondition', [], null, $controller);

            $condition = ArrayHelper::merge($condition, [
                'where' => [
                    ['ad.state' => 1],
                    [
                        '<',
                        'ad.from',
                        date('Y-m-d H:i:s', TIME)
                    ],
                    [
                        '>',
                        'ad.to',
                        date('Y-m-d H:i:s', TIME)
                    ]
                ],
                'limit' => $limit
            ]);

            $bannerList = $this->service('general.list-ad', $condition);
            array_walk($bannerList, function (&$item) use ($controller) {
                $item = $this->callMethod('sufHandleField', $item, [$item], $controller);
            });

            return $bannerList;
        }, HOUR);
    }
}