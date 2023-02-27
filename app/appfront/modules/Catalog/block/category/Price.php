<?php

namespace fecshop\app\appfront\modules\Catalog\block\category;

use Yii;

class Price extends \yii\base\BaseObject
{
    public $price;
    public $special_price;
    public $special_from;
    public $special_to;

    public function getLastData()
    {
        return  Yii::$service->product->price->getCurrentCurrencyProductPriceInfo($this->price, $this->special_price, $this->special_from, $this->special_to);
    }
}
