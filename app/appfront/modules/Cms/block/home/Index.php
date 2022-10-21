<?php

namespace fecshop\app\appfront\modules\Cms\block\home;

use Yii;

class Index extends \yii\base\BaseObject
{
    // This current app's name, such as appfront, apphtml5, appserver.
    private $_appName = '';
    // Given products' key, such as appfront_home, apphtml5_home, appserver_home.
    private $_givenProductsKey = '';

    private $_metaTitle = 'meta_title';
    private $_metaKeywords = 'meta_keywords';
    private $_metaDescription = 'meta_description';

    // Get the last data for this current page.
    public function getLastData()
    {
        // Get this current app's name.
        $this->_appName = Yii::$service->helper->getAppName();
        if (!$this->_appName) {
            return null;
        }
        $this->_givenProductsKey = $this->_appName.'_home';

        $this->initHead();

        // Here you can change the current layout File.
        // Yii::$service->page->theme->layoutFile = 'home.php';

        return [
            'bestFeaturedProducts' => $this->getGivenProducts('best_feature_sku'),
            'bestSellerProducts' => $this->getGivenProducts('best_seller_sku'),
        ];
    }

    // Get given products by key.
    public function getGivenProducts($key)
    {
        $skusStr = Yii::$app->store->get($this->_givenProductsKey, $key);
        $skusArr = explode(',', $skusStr);

        return $this->getProductBySkus($skusArr);
    }

    public function getProductBySkus($skus)
    {
        if (is_array($skus) && !empty($skus)) {
            $filter['select'] = [
                'sku', 'spu', 'name', 'image',
                'price', 'special_price',
                'special_from', 'special_to','brand_id',
                'url_key', 'score', 'reviw_rate_star_average', 'review_count'
            ];
            $filter['where'] = ['in', 'sku', $skus];
            $products = Yii::$service->product->getProducts($filter);
            $products = Yii::$service->category->product->convertToCategoryInfo($products);

            return $products;
        }
    }

    // Init this current html head meta tags and values.
    public function initHead()
    {
        // Get this current app's value.
        $home_title = Yii::$app->store->get($this->_givenProductsKey, $this->_metaTitle);
        $home_meta_keywords = Yii::$app->store->get($this->_givenProductsKey, $this->_metaKeywords);
        $home_meta_description = Yii::$app->store->get($this->_givenProductsKey, $this->_metaDescription);

        // Set the keywords meta tag in this current html.
        Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => Yii::$service->store->getStoreAttrVal($home_meta_keywords, $this->_metaKeywords),
        ]);

        // Set the description meta tag in this current html.
        Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => Yii::$service->store->getStoreAttrVal($home_meta_description, $this->_metaDescription),
        ]);

        // Set the title meta tag in this current html.
        Yii::$app->view->title = Yii::$service->store->getStoreAttrVal($home_title, $this->_metaTitle);
    }
}
