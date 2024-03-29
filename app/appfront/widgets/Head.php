<?php

namespace fecshop\app\appfront\widgets;

use fecshop\interfaces\block\BlockCache;
use Yii;

class Head implements BlockCache
{
    public function getLastData()
    {
        return [];
    }

    public function getCacheKey()
    {
        $store = Yii::$service->store->currentLangCode;
        $moduleId = Yii::$app->controller->module->id;
        $controllerId = Yii::$app->controller->id;
        $actionId = Yii::$app->controller->action->id;
        $urlPathKey = $moduleId.'_'.$controllerId.'_'.$actionId;
        $appName        = Yii::$service->helper->getAppName();
        $cacheKeyName   = 'head';
        $currentStore   = Yii::$service->store->currentStore;
        return self::BLOCK_CACHE_PREFIX.'_'.$currentStore.'_'.$store.'_'.$urlPathKey.'_'.$appName.'_'.$cacheKeyName;
    }
}
