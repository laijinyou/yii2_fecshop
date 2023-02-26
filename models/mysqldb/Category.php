<?php

namespace fecshop\models\mysqldb;

use yii\db\ActiveRecord;
use yii\base\InvalidValueException;

class Category extends ActiveRecord
{
    const MENU_SHOW      = 1;
    const MENU_NOT_SHOW  = 2;
    const STATUS_ENABLE  = 1;
    const STATUS_DISABLE = 2;
    
    // 指定数据库表名称。
    public static function tableName()
    {
        return '{{%category}}';
    }
    
    public function beforeSave($insert)
    {
        foreach ($this->attributes() as $attr) {
            if (is_array($this->{$attr})) {
                throw new InvalidValueException('category model save fail,  attribute ['.$attr. '] is array, you must serialize it before save ');
            }
        }
        return parent::beforeSave($insert);
    }
}
