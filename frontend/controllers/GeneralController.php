<?php

namespace frontend\controllers;

use common\components\Helper;
use common\controllers\MainController;
use yii\helpers\ArrayHelper;
use yii;

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

        if (!empty(Yii::$app->params['upgrade'])) {
            return $this->redirect(['/general/upgrade']);
        }

        Yii::trace('设置语言包');
        if (Yii::$app->session->has(self::LANGUAGE)) {
            Yii::$app->language = Yii::$app->session->get(self::LANGUAGE);
        }

        Yii::trace('获取用户信息');
        if (!$this->user && Yii::$app->session->has(self::USER)) {
            $this->user = (object) Yii::$app->session->get(self::USER);
        }

        $this->weChatLogin();

        return true;
    }

    /**
     * 微信授权登录
     *
     * @access public
     * @return void
     */
    public function weChatLogin()
    {
        // 授权请求
        if (!Yii::$app->request->get('code')) {
            return;
        }
        if (!$this->user) {
            $result = Yii::$app->wx->user();
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
    public function actionClearCache()
    {
        $this->ipa(function () {
            return Yii::$app->cache->flush();
        });
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
     * 获取参数
     *
     * @access public
     *
     * @param mixed $keys
     *
     * @return mixed
     */
    public function params($keys)
    {
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();

        $params = [];
        foreach ((array) $keys as $item) {
            if (isset($get[$item])) {
                $params[$item] = $get[$item];
            } else if (isset($post[$item])) {
                $params[$item] = $post[$item];
            }
        }

        if (empty($params)) {
            return false;
        }

        return is_array($keys) ? $params : current($params);
    }

    /**
     * 创建安全链接
     *
     * @access protected
     *
     * @param mixed   $params
     * @param string  $router
     * @param boolean $checkUser
     *
     * @return string
     */
    protected function createSafeLink($params, $router, $checkUser = true)
    {
        $item = [
            'item' => $params,
            'time' => TIME
        ];

        if ($checkUser) {
            $item['user_id'] = $this->user->id;
        }

        $item = Helper::createSign($item, 'sign');
        $item = base64_encode(Yii::$app->rsa->encryptByPublicKey(json_encode($item)));

        $url = Helper::joinString('/', Yii::$app->params['frontend_url'], $router) . '/';

        $channel = $this->params('channel');
        $channel = $channel ? "&channel={$channel}" : null;

        return "{$url}?safe={$item}{$channel}";
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
        $item = base64_decode(Yii::$app->request->get('safe'));
        $item = json_decode(Yii::$app->rsa->decryptByPrivateKey($item), true);

        $error = false;
        if (!$error && !$item) {
            $error = '非法的支付链接';
        }

        if (!$error && !Helper::validateSign($item, 'sign')) {
            $error = '支付链接签名错误';
        }

        $timeout = Yii::$app->params['order_pay_timeout'] * 60;
        if (!$error && (empty($item['time']) || TIME - $item['time'] > $timeout)) {
            $error = '支付链接已经超时';
        }

        if (!$error && $checkUser && $this->user->id != $item['user_id']) {
            $error = '非法他人代付';
        }

        if ($error) {
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

        return $this->cache([
            'get.product',
            func_get_args()
        ], function () use ($id) {

            $controller = $this->controller('product');
            $condition = $this->callMethod('editCondition', [], null, $controller);

            $condition = ArrayHelper::merge($condition, [
                'where' => [
                    ['product.id' => $id],
                    ['product.state' => 1],
                ]
            ]);

            $detail = $this->service('product.detail', $condition, 'no');
            if (empty($detail)) {
                return false;
            }

            $detail = $this->callMethod('sufHandleField', $detail, [
                $detail,
                'detail'
            ], $controller);
            if (empty($detail['package'])) {
                return false;
            }

            if (!empty($detail)) {
                $field = $detail['sale'] ? 'sale_price' : 'price';
                if ($detail['real_sales'] > $detail['virtual_sales']) {
                    $detail['max_sales'] = $detail['real_sales'];
                } else {
                    $detail['max_sales'] = $detail['virtual_sales'] + $detail['real_sales'];
                }

                foreach ($detail['package'] as $key => $item) {
                    if (empty($item['bidding'])) {
                        unset ($detail['package'][$key]);
                    }
                }

                if (empty($detail['package'])) {
                    $this->error(Yii::t('common', 'product package illegal'));
                }

                $detail['min_price'] = min(array_column($detail['package'], $field));
            }

            return $detail;
        }, DAY, null, Yii::$app->params['use_cache']);
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
            'list.focus',
            func_get_args()
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
                    'product.attachment_cover',
                    'product.update_time'
                ],
                'order' => 'product.top DESC, product.update_time DESC',
                'where' => [
                    ['product.manifestation' => 1],
                    ['product.state' => 1]
                ],
                'limit' => $limit,
            ];
            $list = $this->service('product.list', $condition, 'no');
            array_walk($list, function (&$item) use ($controller) {
                $item = $this->createAttachmentUrl($item, ['attachment_cover' => 'cover']);
            });

            return $list;
        }, DAY, null, Yii::$app->params['use_cache']);
    }

    /**
     * 列表产品
     *
     * @access public
     *
     * @param integer $page
     * @param integer $pageSize
     * @param integer $time
     * @param array   $options
     *
     * @return array
     */
    public function listProduct($page = 1, $pageSize = null, $time = DAY, $options = [])
    {
        $where = [];

        if (isset($options['manifestation']) && is_numeric($options['manifestation'])) {
            $where[] = ['product.manifestation' => $options['manifestation']];
        }

        if (isset($options['classify']) && is_numeric($options['classify'])) {
            $where[] = ['product.classify' => $options['classify']];
        }

        if (isset($options['sale'])) {
            $controller = $this->controller('product');
            $_where = $this->callStatic('saleReverseWhereLogic', [], [$options['sale'] ? 1 : 0], $controller);
            $where = array_merge($where, $_where);
        }

        if (!empty($options['keyword'])) {
            $where[] = [
                'or',
                [
                    'like',
                    'product.title',
                    $options['keyword']
                ],
                [
                    'like',
                    'product.destination',
                    $options['keyword']
                ]
            ];
        }

        $condition = DetailController::$productListCondition;
        $condition['where'] = array_merge($condition['where'], $where);

        if (!empty($options['hot'])) {
            $condition['order'] = '(product.virtual_sales + product.real_sales) DESC';
        }

        $pageParams = Helper::page($page, $pageSize ?: Yii::$app->params['product_page_size']);
        list($condition['offset'], $condition['limit']) = $pageParams;

        return $this->cache([
            'list.product',
            func_get_args()
        ], function () use ($condition, $time) {
            $controller = $this->controller('product-package');
            $list = $this->service('product.list', $condition, 'no');
            foreach ($list as $key => &$item) {
                if (empty($item['price'])) {
                    unset($list[$key]);
                    continue;
                }
                $item = $this->callMethod('sufHandleField', $item, [$item], $controller);
                $item = $this->createAttachmentUrl($item, ['attachment_cover' => 'cover']);
                $item['max_sales'] = max($item['virtual_sales'], $item['real_sales']);
                $item['min_price'] = $item['price'];
                if (!empty($item['sale_price'])) {
                    $item['min_price'] = min($item['sale_price'], $item['price']);
                }
            }

            return $list;
        }, $time, null, Yii::$app->params['use_cache']);
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

        $list = $this->service('product.package-list', ['product_id' => $product_id], 'no');

        $purchaseTimes = [];
        if ($this->user) {
            $purchaseTimes = $this->service('order.purchase-times', [
                'user_id' => $this->user->id,
                'package_ids' => array_column($list, 'id')
            ]);
        }

        $controller = $this->controller('product-package');
        array_walk($list, function (&$item) use ($controller, $purchaseTimes) {

            $limit = 'purchase_limit';
            $mLimit = 'min_purchase_limit';

            if ($item[$limit] <= 0) {
                $item[$mLimit] = -1;
            } else {
                $item[$mLimit] = $item[$limit];
                if (isset($purchaseTimes[$item['id']])) {
                    $item[$mLimit] = $item[$limit] - $purchaseTimes[$item['id']];
                    $item[$mLimit] = $item[$mLimit] <= 0 ? 0 : $item[$mLimit];
                }
            }

            $item = $this->callMethod('sufHandleField', $item, [$item], $controller);
            $item['min_price'] = $item['price'];
            if (!empty($item['sale_price'])) {
                $item['min_price'] = min($item['sale_price'], $item['price']);
            }
        });

        return array_combine(array_column($list, 'id'), $list);
    }

    /**
     * 列表子订单
     *
     * @access public
     *
     * @param integer $page
     * @param mixed   $state
     * @param integer $page_size
     *
     * @return array
     */
    public function listOrderSub($page = 1, $state = null, $page_size = null)
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

        if (!empty($condition['where'])) {
            $where = array_merge($condition['where'], $where);
        }
        $condition['where'] = $where;

        list($condition['offset'], $condition['limit']) = Helper::page($page, $page_size ?: Yii::$app->params['order_page_size']);
        $list = $this->service('order.list', $condition, 'no');

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
     * @param integer $type
     * @param integer $limit
     *
     * @return array
     */
    public function listAd($type, $limit = null)
    {
        return $this->cache([
            'list.ad',
            func_get_args()
        ], function () use ($type, $limit) {
            $controller = $this->controller('ad');
            $condition = $this->callMethod('editCondition', [], null, $controller);

            $condition = ArrayHelper::merge($condition, [
                'where' => [
                    ['ad.state' => 1],
                    ['ad.type' => $type],
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

            $adList = $this->service('general.list-ad', $condition);
            array_walk($adList, function (&$item) use ($controller) {
                $item = $this->callMethod('sufHandleField', $item, [$item], $controller);
            });

            return $adList;
        }, HOUR, null, Yii::$app->params['use_cache']);
    }

    /**
     * 系统维护页面
     *
     * @access public
     * @return mixed
     */
    public function actionUpgrade()
    {
        $params = Yii::$app->params;
        if (!$params['upgrade']) {
            return $this->redirect(['site/index']);
        }

        $message = sprintf($params['upgrade_message'], $params['upgrade_minute']);
        $this->message($message, $params['upgrade_title']);

        return null;
    }
}