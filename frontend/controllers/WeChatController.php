<?php

namespace frontend\controllers;

use common\components\Helper;
use Yii;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * WeChat reply controller
 */
class WeChatController extends GeneralController
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (in_array($action->id, ['reply'])) {
            $action->controller->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    /**
     * 监听消息
     */
    public function actionReply()
    {
        $wx = Yii::$app->wx;

        if (Yii::$app->request->get('signature')) {
            $wx->listen([
                'text' => function ($message) use ($wx) {
                    return $this->replyTextLottery($message, $wx);
                }
            ]);
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

        if ($text == 'leon') {
            $url = $this->lotteryImg('阿里巴巴集团', 'xL3js0A');
            return "<img src=" . $url . ">";
        }

        // 格式判断
        $text = str_replace('＋', '+', $text);
        $char = substr_count($text, '+');
        if ($char < 2) {
            return null;
        }

        list($company, $name, $phone) = explode('+', $text);

        // 名字/手机号码验证
        if (empty($name) || empty($phone)) {
            return "名字和手机号码用于中奖联络方式，请规范填写哦~";
        }

        $model = parent::model('ActivityLotteryCode');

        // 公司代码验证
        $company = strtolower($company);
        if (false === ($code = array_search($company, $model->_company))) {
            return "该品牌还不是喀客旅行的合作伙伴~";
        }

        // 时间判断
        if (isset($model->_activity_date[$code])) {

            $date = $model->_activity_date[$code];

            if (isset($date['begin']) && TIME < strtotime($date['begin'])) {
                return "抽奖活动还未开始，不要太心急哦~开始时间：${date['begin']}~ 爱你么么哒";
            }
            if (isset($date['end']) && TIME > strtotime($date['end'])) {
                return "哎呀，你来晚了！抽奖活动已经结束了！";
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
            return "Oops! An error has occurred.{$br}{$br}${result}";
        }

        // 已参与判断
        if (!empty($result['exists'])) {
            return "宝贝，不要太贪心哦~你已经参与过啦~{$br}抽奖码：${result['code']}，祝你好运~";
        }

        return "WoW~ 这是喀客旅行为你提供的抽奖码：${result['code']}！希望你能抽中奖品～";
    }

    /**
     * 生成抽奖码图片
     *
     * @access protected
     *
     * @param string $company
     * @param string $code
     *
     * @return string
     */
    protected function lotteryImg($company, $code)
    {
        $bg = self::getPathByUrl('img/activity/lottery-bg.jpg', 'frontend_source');
        $img = Image::make($bg);

        $fonts = self::getPathByUrl('fonts/hanyi.ttf', 'frontend_source');

        // 添加文本
        $text = function ($text, $size, $y, $fonts, $width = 750) use ($img) {

            list($w) = Helper::textPx($text, $fonts, $size, 0.78);
            $x = ($width - $w) / 2;

            $img->text($text, $x, $y, function ($font) use ($fonts, $size) {
                $font->file($fonts);
                $font->size($size);
            });
        };

        // 打印公司名称
        $text($company, 38, 325, $fonts);

        // 打印抽奖码
        $text($code, 28, 836, $fonts);

        $this->user = (object) ['id' => 1];
        $tmp = Yii::$app->params['tmp_path'] . '/' . $this->user->id . '.jpg';
        $img->save($tmp);

        $url = self::getUrlByPath($tmp, 'jpg', 'general_');

        return $url;
    }
}
