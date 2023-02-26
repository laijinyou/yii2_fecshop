<?php

namespace fecshop\app\appfront\modules\Catalog\helpers;

use Yii;

class Price extends \yii\base\BaseObject
{
    public function getCategoryPrice($price, $special_price)
    {
        $price_info = Yii::$service->product->price->format_price($price);
        $return = [
            'price' => [
                'symbol' => $price_info['symbol'],
                'value' => $price_info['value'],
            ],
        ];
        if ($special_price) {
            $special_price_info = Yii::$service->product->price->format_price($special_price);
            $return['special_price'] = [
                'symbol' => $special_price_info['symbol'],
                'value' => $special_price_info['value'],
            ];
        }

        return $return;
    }
}
