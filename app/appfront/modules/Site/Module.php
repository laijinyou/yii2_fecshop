<?php

namespace fecshop\app\appfront\modules\Site;

use fecshop\app\appfront\modules\AppfrontModule;
use Yii;

// Site Module 模块
class Module extends AppfrontModule
{
    public $blockNamespace;

    public function init()
    {
        parent::init();
        // 以下代码必须指定
        $nameSpace = __NAMESPACE__;
        // 如果 Yii::$app 对象是由类\yii\web\Application 实例化出来的。
        if (Yii::$app instanceof \yii\web\Application) {
            // 设置模块 controller namespace的文件路径
            $this->controllerNamespace = $nameSpace . '\\controllers';
            // 设置模块block namespace的文件路径
            $this->blockNamespace = $nameSpace . '\\block';
        // console controller
        //} elseif (Yii::$app instanceof \yii\console\Application) {
            //$this->controllerNamespace = $nameSpace . '\\console\\controllers';
            //$this->blockNamespace = $nameSpace . '\\console\\block';
        }
        //$this->_currentDir			= 	__DIR__ ;
        //$this->_currentNameSpace	=   __NAMESPACE__;

        // Set the default theme layout file of this current module.
        Yii::$service->page->theme->layoutFile = 'home.php';
    }
}
