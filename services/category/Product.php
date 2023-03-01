<?php

namespace fecshop\services\category;

use fecshop\services\Service;
use Yii;

// 分类对应的产品的一些操作
class Product extends Service
{
    public $pageNum = 1;

    public $numPerPage = 50;

    public $allowedNumPerPage;

    /**
     * @param $filter | Array   example:
     * [
     *     'category_id'    => 1,
     *     'pageNum'        => 2,
     *     'numPerPage'     => 50,
     *     'orderBy'        => 'name',
     *     'where'          => [
     *         ['>','price',11],
     *         ['<','price',22],
     *     ],
     * ]
     * 通过搜索条件得到当类下的产品。
     */
    public function coll($filter)
    {
        $category_id = isset($filter['category_id']) ? $filter['category_id'] : '';
        if (!$category_id) {
            Yii::$service->helper->errors->add('category id is empty');

            return;
        } else {
            unset($filter['category_id']);
            $filter['where'][] = ['category' => $category_id];
        }
        if (!isset($filter['pageNum']) || !$filter['pageNum']) {
            $filter['pageNum'] = 1;
        }
        if (!isset($filter['numPerPage']) || !$filter['numPerPage']) {
            $filter['numPerPage'] = $this->numPerPage;
        }
        if (isset($filter['orderBy']) && !empty($filter['orderBy'])) {
            if (!is_array($filter['orderBy'])) {
                Yii::$service->helper->errors->add('orderBy must be array');

                return;
            }
        }

        return Yii::$service->product->coll($filter);
    }

    /**
     * @param $filter | Array    和上面的函数 coll($filter) 类似。
     */
    public function getFrontList($filter)
    {
        $filter['group'] = '$spu';
        $filter['select'][] = 'brand_id';
        $coll = Yii::$service->product->getFrontCategoryProducts($filter);
        $collection = $coll['coll'];
        $count = $coll['count'];
        $arr = $this->convertToCategoryInfo($collection);
        
        return [
            'coll' => $arr,
            'count'=> $count,
        ];
    }

    // 将service取出来的数据，处理一下，然后前端显示。
    public function convertToCategoryInfo($collection)
    {
        $arr = [];
        if (is_array($collection) && !empty($collection)) {
            foreach ($collection as $one) {
                // 产品id
                $primary_key = Yii::$service->product->getPrimaryKey();
                $_id = (string)$one[$primary_key];

                // 产品名称
                if (is_array($one['name']) && !empty($one['name'])) {
                    // 多语言
                    $name = Yii::$service->store->getStoreAttrVal($one['name'], 'name');
                } else {
                    // 单语言
                    $name = $one['name'];
                }

                // 封面图片
                $image = $one['image'];
                if (isset($image['main']['image']) && !empty($image['main']['image'])) {
                    $image = $image['main']['image'];
                } else {
                    // 产品没有封面图片的时候显示默认图片
                    $image = Yii::$service->product->image->defautImg();
                }
                
                // 品牌名称
                $brand_name = $one['brand_id'] ? Yii::$service->product->brand->getBrandNameById($one['brand_id']) : '';

                // 产品详情url
                $url = Yii::$service->url->getUrl($one['url_key']);

                // 价格
                $price_info = Yii::$service->product->price->getCurrentCurrencyProductPriceInfo($one['price'], $one['special_price'], $one['special_from'], $one['special_to']);

                $arr[] = [
                    '_id'                     => $_id,
                    'name'                    => $name,
                    'sku'                     => $one['sku'],
                    'image'                   => $image,
                    'brand_id'                => $one['brand_id'],
                    'brand_name'              => $brand_name,
                    'url'                     => $url,
                    'is_in_stock'             => $one['is_in_stock'],
                    'reviw_rate_star_average' => isset($one['reviw_rate_star_average']) ? $one['reviw_rate_star_average'] : 0,
                    'review_count'            => isset($one['review_count']) ? $one['review_count'] : 0,
                    'price_msg'               => $price_info['price'],
                    'special_price_msg'       => $price_info['special_price'],
                    'special_from'            => $one['special_from'],
                    'special_to'              => $one['special_to'],
                ];
            }
        }

        return $arr;
    }
}
