<?php

namespace fecshop\app\appfront\modules\Catalog\controllers;

use fecshop\app\appfront\modules\AppfrontController;

class FavoriteproductController extends AppfrontController
{
    public $enableCsrfValidation = false;
    // 增加收藏
    public function actionAdd()
    {
        return $this->getBlock()->getLastData();
        //return $this->render($this->action->id,$data);
    }
    // 收藏列表
    //public function actionLists()
    //{
    //    $data = $this->getBlock()->getLastData($editForm);
    //
    //    return $this->render($this->action->id, $data);
    //}
}
