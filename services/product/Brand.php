<?php

/*
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fecshop\services\product;

//use fecshop\models\mysqldb\cms\StaticBlock;
use Yii;
use fecshop\services\Service;

/**
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class Brand extends Service
{
    public $numPerPage = 20;

    protected $_modelName = '\fecshop\models\mysqldb\product\Brand';

    protected $_model;
    
    public $status_enable = 1;
    public $status_disable = 2;
    /**
     *  language attribute.
     */
    protected $_lang_attr = [
        'name',
    ];
    
    public function init()
    {
        parent::init();
        list($this->_modelName, $this->_model) = Yii::mapGet($this->_modelName);
    }
    
    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getByPrimaryKey($primaryKey)
    {
        if ($primaryKey) {
            $one = $this->_model->findOne($primaryKey);
            foreach ($this->_lang_attr as $attrName) {
                if (isset($one[$attrName])) {
                    $one[$attrName] = unserialize($one[$attrName]);
                }
            }

            return $one;
        } else {
            
            return new $this->_modelName();
        }
    }
    
    public function getByRemoteId($remoteId)
    {
        if (!$remoteId) {
            
            return null;
        } 
        $one = $this->_model->findOne(['remote_id' => $remoteId]);
        if (!isset($one['remote_id']) || !$one['remote_id']){
            
            return null;
        }
        
        return $one;
    }
    
    /*
     * example filter:
     * [
     * 		'numPerPage' 	=> 20,
     * 		'pageNum'		=> 1,
     * 		'orderBy'	=> ['_id' => SORT_DESC, 'sku' => SORT_ASC ],
            'where'			=> [
                ['>','price',1],
                ['<=','price',10]
     * 			['sku' => 'uk10001'],
     * 		],
     * 	'asArray' => true,
     * ]
     */
    public function coll($filter = '')
    {
        $query = $this->_model->find();
        $query = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll = $query->all();
        if (!empty($coll)) {
            foreach ($coll as $k => $one) {
                foreach ($this->_lang_attr as $attr) {
                    $one[$attr] = $one[$attr] ? unserialize($one[$attr]) : '';
                }
                $coll[$k] = $one;
            }
        }
        
        return [
            'coll' => $coll,
            'count'=> $query->limit(null)->offset(null)->count(),
        ];
    }

    /**
     * @param $one|array
     * save $data to cms model,then,add url rewrite info to system service urlrewrite.
     */
    public function save($one)
    {
        $remoteId = isset($one['remote_id']) ? $one['remote_id'] : '';
        $primaryVal = isset($one[$this->getPrimaryKey()]) ? $one[$this->getPrimaryKey()] : '';
        
        if ($primaryVal) {
            $model = $this->_model->findOne($primaryVal);
            if (!$model) {
                Yii::$service->helper->errors->add('brand {primaryKey} is not exist', ['primaryKey' => $this->getPrimaryKey()]);

                return;
            }
        } else if ($remoteId) {    
            $model = $this->_model->findOne(['remote_id' => $remoteId]);
            if (!$model) {
                $model = new $this->_modelName();
                $model->created_at = time();
            }
        } else {
            $model = new $this->_modelName();
            $model->created_at = time();
        }
        $model->updated_at = time();
        foreach ($this->_lang_attr as $attrName) {
            if (is_array($one[$attrName]) && !empty($one[$attrName])) {
                $one[$attrName] = serialize($one[$attrName]);
            }
        }
        $primaryKey = $this->getPrimaryKey();
        $model      = Yii::$service->helper->ar->save($model, $one);
        $primaryVal = $model[$primaryKey];
        foreach ($this->_lang_attr as $attr) {
            $model[$attr] = $model[$attr] ? unserialize($model[$attr]) : '';
        }
        
        return $model;
    }

    public function upsert($one)
    {
        $currentDateTime = \fec\helpers\CDate::getCurrentDateTime();
        $primaryVal = $one['id'];
        //var_dump($one);exit;
        if (!$primaryVal) {  // ??????id??????????????????0???????????????
            //Yii::$service->helper->errors->add('id can not empty');
            //return;
            $model = new $this->_modelName();
            $model->created_at = time();
        } else {
            $model = $this->_model->findOne($primaryVal);
        }
        // ????????????id???????????????????????????????????????id
        if (!$model) {
            $model = new $this->_modelName();
            $model->created_at = time();
            $model->id = $primaryVal;
        }
        $model->updated_at = time();
        foreach ($this->_lang_attr as $attrName) {
            if (is_array($one[$attrName]) && !empty($one[$attrName])) {
                $one[$attrName] = serialize($one[$attrName]);
            }
        }
        $primaryKey = $this->getPrimaryKey();
        $model      = Yii::$service->helper->ar->save($model, $one);
        $primaryVal = $model[$primaryKey];
        foreach ($this->_lang_attr as $attr) {
            $model[$attr] = $model[$attr] ? unserialize($model[$attr]) : '';
        }
        return $model;
    }
    
    public function remove($ids)
    {
        if (!$ids) {
            Yii::$service->helper->errors->add('remove id is empty');

            return false;
        }
        if (is_array($ids) && !empty($ids)) {
            foreach ($ids as $id) {
                $model = $this->_model->findOne($id);
                $model->delete();
            }
        } else {
            $id = $ids;
            $model = $this->_model->findOne($id);
            $model->delete();
        }

        return true;
    }
    
    /**
     * ??????????????????
     */
    public function getStatusArr()
    {
       
        return [
            $this->status_enable => Yii::$service->page->translate->__('Enable'),
            $this->status_disable => Yii::$service->page->translate->__('Disable'),
        ];
    }
    
    protected $_allBrandIdAndNames;
    public function getBrandNameByIdWithAll($brandId)
    {
        if (!$this->_allBrandIdAndNames) {
            $this->_allBrandIdAndNames = $this->getAllBrandIdAndNames();
        }
        if (isset($this->_allBrandIdAndNames[$brandId]) && $this->_allBrandIdAndNames[$brandId]) {
            
            return $this->_allBrandIdAndNames[$brandId];
        }
        return $brandId;
    }
    
    /**
     * ????????????id ??? names ??????
     */
    public function getAllBrandIdAndNames()
    {
        $filter = [
            'where' => [
                ['status' => $this->status_enable]
            ],
            'fetchAll' => true,
            'asArray' => true,
        ];
        $data = $this->coll($filter);
        $arr = [];
        if (is_array($data['coll']) && !empty($data['coll'])) {
            foreach ($data['coll'] as $one) {
                $name = Yii::$service->store->getStoreAttrVal($one['name'], 'name');
                $arr[$one['id']] = $name;
            }
        }
        
        return $arr;
    }
    /**
     * @param $brandId | int,  ??????id
     * ????????????id??????????????????name
     */
    public function getBrandNameById($brandId)
    {
        $brandModel = $this->getByPrimaryKey($brandId);
        if (!$brandModel['id']) {
            
            return '';
        }
        
        return Yii::$service->store->getStoreAttrVal($brandModel['name'], 'name');
    }
    
    
    
    
}
