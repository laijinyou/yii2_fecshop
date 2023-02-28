<?php

namespace fecshop\app\appfront\modules\Catalog;

use fecshop\app\appfront\modules\AppfrontModule;
use Yii;

class Module extends AppfrontModule
{
    public $blockNamespace;

    public function init()
    {
        parent::init();

        // 获取当前路径（fecshop\app\appfront\modules\Catalog）
        $nameSpace = __NAMESPACE__;
        
        // 判断 Yii::$app 对象是由类\yii\web\Application 实例化出来的。
        if (Yii::$app instanceof \yii\web\Application) {
            // 设置模块 controller block 的文件路径
            $this->controllerNamespace = $nameSpace . '\\controllers';
            $this->blockNamespace = $nameSpace . '\\block';
        }

        // 设置本模块theme的默认layout文件。
        Yii::$service->page->theme->layoutFile = 'main.php';
    }
}
