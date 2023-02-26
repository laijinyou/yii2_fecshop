<?php

namespace fecshop\app\appfront\helper;

class Format extends \yii\base\BaseObject
{
    /**
     * @param $price | Float ,价格
     * @param $bits | Int , 小数点后几位的格式，譬如4.00
     * @return float， 返回格式化后的数据
     * 一般用于模板中，按照显示格式显示产品数据。
     */
    public static function price($price, $bits = 2)
    {
        return number_format($price, $bits);
    }
}
