<?php

namespace frontend\controllers;

use common\models\Main;
use Yii;

/**
 * WeChat reply controller
 */
class WeChatReplyController extends GeneralController
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        $wx = Yii::$app->wx;

        if (Yii::$app->request->get('signature')) {
            $wx->listen(null, function ($message) use ($wx) {
                return $this->replyTextLottery($message, $wx);
            });
        }
    }

    /**
     * 回复抽奖活动
     *
     * @param object $message
     * @param object $wx
     *
     * @return string
     */
    private function replyTextLottery($message, $wx)
    {
        $br = PHP_EOL;
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

        $model = new Main('ActivityLotteryCode');

        // 公司代码验证
        $company = strtolower($company);
        if (false === ($code = array_search($company, $model->_company))) {
            return "【公司不在抽奖范围内】{$br}啊哦，你关注的品牌还不是喀客旅行的小伙伴~{$br}不如快介绍他们给喀客认识，下次说不定就有你的份！";
        }

        // 时间判断
        if (isset($model->_activity_date[$code])) {

            $date = $model->_activity_date[$code];

            if (isset($date['begin']) && TIME < strtotime($date['begin'])) {
                return "【活动未开始】{$br}抽奖活动还未开始，不要太心急哦~开始时间：${date['begin']}~ 爱你么么哒";
            }
            if (isset($date['end']) && TIME > strtotime($date['end'])) {
                return "【活动已结束】{$br}艾玛，你来晚了！本期抽奖活动已经落幕！{$br}还是拿着优惠券去商城逛逛酒店吧~";
            }
        }

        $user = $wx->user->get($message->FromUserName);
        $result = $this->service('general.log-lottery-code', [
            'openid' => $user->openid,
            'nickname' => $user->nickname,
            'company' => $code,
            'real_name' => $name,
            'phone' => $phone
        ]);
        if (is_string($result)) {
            return "Oops! An error has occurred.{$br}${result}";
        }

        // 已参与判断
        if (!empty($result['exists'])) {
            return "【已参与过抽奖】{$br}宝贝，不要太贪心哦~你已经有一个专属抽奖码啦~{$br}抽奖码：${result['code']}";
        }

        return "【参与成功】{$br}耶！这是喀客旅行为你提供的抽奖码：${result['code']}！希望你能抽中奖品～";
    }
}