<?php

namespace console\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "activity_lottery_code".
 *
 * @property integer $id
 * @property string  $openid
 * @property integer $company
 * @property string  $nickname
 * @property string  $real_name
 * @property string  $phone
 * @property string  $code
 * @property string  $add_time
 * @property string  $update_time
 * @property integer $state
 */
class ActivityLotteryCode extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity_lottery_code';
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->kake;
    }
}