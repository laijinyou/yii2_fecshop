<?php

namespace fecshop\models\mysqldb;

use yii\db\ActiveRecord;

class CategoryProduct extends ActiveRecord
{
    // 指定数据库表名称。
    public static function tableName()
    {
        return '{{%category_product}}';
    }
}
