<?php

/*
 * FecShop file.
 *
 * @link http://www.fecshop.com/
 * @copyright Copyright (c) 2016 FecShop Software LLC
 * @license http://www.fecshop.com/license/
 */

namespace fecshop\services\product\attrgroup;

use fecshop\services\Service;
use yii\db\Query;
use Yii;

/**
 * Product ProductMysqldb Service 未开发。
 * @author Terry Zhao <2358269014@qq.com>
 * @since 1.0
 */
class AttrGroupMysqldb extends Service implements AttrGroupInterface
{
    public $numPerPage = 20;
    
    protected $_attrGroupModelName = '\fecshop\models\mysqldb\product\AttrGroup';

    protected $_attrGroupModel;
    
    public function init()
    {
        parent::init();
        list($this->_attrGroupModelName, $this->_attrGroupModel) = \Yii::mapGet($this->_attrGroupModelName);
    }
    
    public function getPrimaryKey()
    {
        return 'id';
    }
    
    /**
     * 得到分类激活状态的值
     */
    public function getEnableStatus()
    {
        $model = $this->_attrGroupModel;
        
        return $model::STATUS_ENABLE;
    }
    
    public function getByPrimaryKey($primaryKey = null)
    {
        if ($primaryKey) {
            $one = $this->_attrGroupModel->findOne($primaryKey);
            
            return $one;
        } else {
            
            return new $this->_attrGroupModel();
        }
    }
    
    public function getByName($name)
    {
        if ($name) {
            $one = $this->_attrGroupModel->findOne(['name' => $name]);
            
            return $one;
        } else {
            
            return new $this->_attrGroupModel();
        }
    }
    
    /*
     * example filter:
     * [
     * 		'numPerPage' 	=> 20,
     * 		'pageNum'		=> 1,
     * 		'orderBy'	=> ['_id' => SORT_DESC, 'sku' => SORT_ASC ],
     * 		'where'			=> [
                ['>','price',1],
                ['<=','price',10]
     * 			['sku' => 'uk10001'],
     * 		],
     * 	'asArray' => true,
     * ]
     */
    public function coll($filter = '')
    {
        $query = $this->_attrGroupModel->find();
        $query = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll = $query->all();
        //$arr = [];
        //foreach ($coll as $one) {
        //    $arr[] = $this->unserializeData($one) ;
        //}
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
        $primaryVal = isset($one[$this->getPrimaryKey()]) ? $one[$this->getPrimaryKey()] : '';
        $remoteId = isset($one['remote_id']) ? $one['remote_id'] : '';
        $isUpdate = false;
        if ($primaryVal) {
            $model = $this->_attrGroupModel->findOne($primaryVal);
            if (!$model) {
                Yii::$service->helper->errors->add('Product attr group {primaryKey} is not exist', ['primaryKey' => $this->getPrimaryKey()]);

                return;
            }
            $isUpdate = true;
        } else if ($remoteId) {    
            $model = $this->_attrGroupModel->findOne(['remote_id' => $remoteId]);
            if (!$model) {
                $model = new $this->_attrGroupModelName();
                $model->created_at = time();
            } else {
                $isUpdate = true;
            }
        } else {
            $model = new $this->_attrGroupModelName();
            $model->created_at = time();
        }
        // attr Group name唯一性判断
        $groupName = $one['name'];
        if (!$groupName) {
            Yii::$service->helper->errors->add('name can not empty');
            
            return false;
        }
        if ($isUpdate) {
            $groupId = $model['id'];
            if ($groupId) {
                $oneM = $this->_attrGroupModel->find()->asArray()->where([
                    'and',
                    ['<>', 'id', $groupId],
                    ['name' => $groupName]
                ])->one();
                if ($oneM['id']) {
                    Yii::$service->helper->errors->add('name must unique');
            
                    return false;
                }
            }
        } else {
           $oneM = $this->_attrGroupModel->find()->asArray()->where(['name' => $groupName] )->one();
            if ($oneM['id']) {
                Yii::$service->helper->errors->add('name must unique');
        
                return false;
            }
        }
        $model->updated_at = time();
        $primaryKey = $this->getPrimaryKey();
        $model      = Yii::$service->helper->ar->save($model, $one);
        $primaryVal = $model[$primaryKey];

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
                $model = $this->_attrGroupModel->findOne($id);
                $model->delete();
            }
        } else {
            $id = $ids;
            $model = $this->_attrGroupModel->findOne($id);
            $model->delete();
        }

        return true;
    }
    
    public function getActiveAllColl()
    {
        // attribute Group
        $filter = [
            'where' => [
                ['status' => $this->getEnableStatus()]
            ],
            'fetchAll' => true,
            'asArray' => true,
        ];
        $query = $this->_attrGroupModel->find();
        $query = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll = $query->all();
        if (is_array($coll)) {
            foreach ($coll as $k => $groupOne) {
                if ($groupOne['attr_ids']) {
                    $attr_ids = unserialize($groupOne['attr_ids']);
                    $coll[$k]['attr_ids'] = $attr_ids;
                }
            }
            
            return $coll;
        }
        
        return null;
    }
    
    
    
    public function getAttrGroupArr()
    {
        // attribute Group
        $filter = [
            'where' => [
                ['status' => $this->getEnableStatus()]
            ],
            'fetchAll' => true,
            'asArray' => true,
        ];
        $query = $this->_attrGroupModel->find();
        $query = Yii::$service->helper->ar->getCollByFilter($query, $filter);
        $coll = $query->all();
        $arr = [];
        if (is_array($coll)) {
            foreach ($coll as $k => $groupOne) {
                if ($groupOne['name']) {
                    $groupName = $groupOne['name'];
                    $arr[$groupName] = $groupName;
                }
            }
            
            return $arr;
        }
        
        return null;
    }
    
    
    public function getByRemoteId($remoteId)
    {
        if (!$remoteId) {
            
            return null;
        } 
        $one = $this->_attrGroupModel->findOne(['remote_id' => $remoteId]);
        if (!isset($one['remote_id']) || !$one['remote_id']){
            
            return null;
        }
        
        return $one;
    }
    
}
