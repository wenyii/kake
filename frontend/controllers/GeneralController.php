<?php

namespace frontend\controllers;

use common\components\Helper;
use common\controllers\MainController;
use common\models\Main;
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

        if (Yii::$app->request->get('signature')) {
            $wx->listen(null, function ($message) use ($wx) {
                return $this->replyText($message, $wx);
            });
        }

        // 授权请求
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
     * 监听微信文本消息
     *
     * @param object $message
     * @param object $wx
     *
     * @return string
     */
    private function replyText($message, $wx)
    {
        $br = PHP_EOL;

        // 时间判断
        if (TIME < strtotime($startTime = '2017-05-18 12:00:00')) {
            return "【活动未开始】{$br}抽奖活动还未开始，不要太心急哦~开始时间：{$startTime}~ 爱你么么哒";
        }
        if (TIME > strtotime($endTime = '2017-05-23 23:59:59')) {
            return "【活动已结束】{$br}艾玛，你来晚了！本期抽奖活动已经落幕！{$br}还是拿着优惠券去商城逛逛酒店吧~";
        }

        $model = new Main('ActivityLotteryCode');
        $text = trim($message->Content);

        // 格式判断
        $text = str_replace('＋', '+', $text);
        $char = substr_count($text, '+');
        if ($char < 2) {
            return "【回复的格式不正确】{$br}回复格式不正确，小喀无法识别！{$br}正确格式：品牌名+姓名+手机号{$br}“+”一定要打出来哦~";
        }

        list($company, $name, $phone) = explode('+', $text);

        // 名字/手机号码验证
        if (empty($name) || empty($phone)) {
            return "【名字和手机号码不规范】{$br}如果你是中国人，你的名字应该是2~4个字，你的手机号应该是11位数哦~{$br}如果你不是·····现在取名或者办理手机号还来得及！";
        }

        // 公司代码验证
        if (false === ($code = array_search(strtolower($company), $model->_company))) {
            return "【公司不在抽奖范围内】{$br}啊哦，你关注的品牌还不是喀客旅行的小伙伴~{$br}不如快介绍他们给喀客认识，下次说不定就有你的份！";
        }

        $result = $this->service('general.detail', [
            'table' => $model->tableName,
            'where' => [
                ['openid' => $message->FromUserName]
            ]
        ], 'no');

        // 已参与判断
        if (!empty($result)) {
            return "【已参与过抽奖】{$br}宝贝，不要太贪心哦~你已经有一个专属抽奖码啦~{$br}抽奖码：${result['code']}";
        }

        $user = $wx->user->get($message->FromUserName);
        $code = $this->service('general.log-lottery-code', [
            'openid' => $user->openid,
            'nickname' => $user->nickname,
            'company' => $code,
            'real_name' => $name,
            'phone' => $phone
        ]);

        return "【参与成功】{$br}耶！这是喀客旅行为你提供的抽奖码：{$code}！希望你能抽到酒店！";
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
     * @param mixed   $params
     * @param string  $router
     * @param boolean $checkUser
     *
     * @return string
     */
    protected function createSafeLink($params, $router, $checkUser = true)
    {
        $item = ['item' => $params];

        if ($checkUser) {
            $item['user_id'] = $this->user->id;
        }

        $item = Helper::createSign($item, 'sign');
        $item = base64_encode(Yii::$app->rsa->encryptByPublicKey(json_encode($item)));

        $url = Helper::joinString('/', Yii::$app->params['frontend_url'], $router) . '/';

        return $url . '?safe=' . $item;
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
            $error = '非法链接';
        }

        if (!$error && !Helper::validateSign($item, 'sign')) {
            $error = '签名错误';
        }

        if (!$error && $checkUser && $this->user->id != $item['user_id']) {
            $error = '非法代付';
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
            'get-product',
            $id
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
            'list-product',
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
            'list-ad',
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
}