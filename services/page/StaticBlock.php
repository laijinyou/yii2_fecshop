<?php

namespace fecshop\services\page;

use fecshop\services\Service;
use Yii;
use yii\base\InvalidValueException;

/**
 * Page StaticBlock services.废弃，staticBlock在Yii::$service->cms->staticBlock中实现
 */
class StaticBlock extends Service
{
    /**
     * @param  $key|array
     */
    public function getByKey($key, $lang = '')
    {
        if (!$lang) {
            $lang = Yii::$service->store->currentLanguage;
        }
        if (!$lang) {
            throw new InvalidValueException('language is empty');
        }
    }

    /**
     *	@param $_id | Int
     *  get StaticBlock one data by $_id.
     */
    public function getById($_id)
    {
    }

    /**
     *	@param $filter | Array
     *  get StaticBlock collections by $filter .
     */
    public function getStaticBlockList($filter)
    {
    }
}
