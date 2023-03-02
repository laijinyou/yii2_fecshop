<?php

namespace fecshop\app\appfront\modules\Cms\block\home;

use Yii;

// ホームーページ
class Index extends \yii\base\BaseObject
{
    // カルーセルの名前
    protected $CAROUSEL_NAMES = array("best_seller_sku", "best_feature_sku");
    // アプリの名前
    protected $APP_NAME = "";
    // storeの親キー
    protected $STORE_PARENT_KEY = "";
    // store接尾辞として付ける
    protected $STORE_SUFFIX = "_home";
    // HTMLヘッドメタデータ
    protected $META_LENGTH = 5;
    protected $TITLE = "meta_title";
    protected $HEAD_META = array("meta_title", "meta_keywords", "meta_description");

    // レスポンスのデータを取る
    public function getLastData()
    {
        // アプリ名前を取る
        $this -> APP_NAME = Yii::$service->helper->getAppName();
        // storeの親キー
        $this -> STORE_PARENT_KEY = $this -> APP_NAME.$this -> STORE_SUFFIX;
        // htmlのheadタグのデータを設置する
        $this->setHtmlHead();
        
        // カルーセルの名前
        $carouselNames = $this -> CAROUSEL_NAMES;
        // カルーセルのデータを取る
        $carousels = array();
        foreach ($carouselNames as $carouselName) {
            // カルーセルの商品を取る
            $carousels[$carouselName] = $this->getCarouselProducts($carouselName);
        };

        return $carousels;
    }

    // カルーセルの商品を取る
    public function getCarouselProducts($carouselName)
    {
        // skus文字列を取る
        $skus = Yii::$app->store->get($this -> STORE_PARENT_KEY, $carouselName);
        // 文字列を文字列により分割して文字列の配列を返します
        $product_skus = explode(',', $skus);
        // 商品のデータを取る
        $products = $this->getProductsBySkus($product_skus);

        return $products;
    }

    public function getProductsBySkus($product_skus)
    {
        if (is_array($product_skus) && !empty($product_skus)) {
            $filter['select'] = [
                'sku', 'spu', 'name', 'image',
                'price', 'special_price',
                'special_from', 'special_to','brand_id',
                'url_key', 'score', 'reviw_rate_star_average', 'review_count'
            ];
            $filter['where'] = ['in', 'sku', $product_skus];
            $products = Yii::$service->product->getProducts($filter);
            $products = Yii::$service->category->product->convertToCategoryInfo($products);

            return $products;
        }
    }

    // htmlのheadタグのデータを設置する。
    public function setHtmlHead()
    {
        // storeからヘッドメタデータを取る
        foreach ($this -> HEAD_META as $meta) {
            $data = Yii::$app->store->get($this -> STORE_PARENT_KEY, $meta);
            $meta_data = Yii::$service->store->getStoreAttrVal($data, $meta);
            if ($meta === $this -> TITLE) {
                // HTMLのタイトルを設置する
                Yii::$app->view->title = $meta_data;
            } else {
                // HTMLのkeywords、descriptionを設置する
                Yii::$app->view->registerMetaTag([
                    'name' => substr($meta, $this -> META_LENGTH),
                    'content' => $meta_data,
                ]);
            }
        };
    }
}
