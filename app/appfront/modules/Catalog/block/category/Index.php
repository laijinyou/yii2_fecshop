<?php

namespace fecshop\app\appfront\modules\Catalog\block\category;

use Yii;
use yii\base\InvalidValueException;

class Index extends \yii\base\BaseObject
{
    // 当前分类对象
    protected $_category;
    // 页面标题
    protected $_title;
    // 当前分类主键对应的值
    protected $_primaryVal;
    // 默认的排序字段
    protected $_defautOrder;
    // 默认的排序方向，升序还是降序
    protected $_defautOrderDirection = SORT_DESC;
    // 当前的where条件
    protected $_where;
    // url的参数，每页产品个数
    protected $_numPerPage = 'numPerPage';
    // url的参数，排序方向
    protected $_direction = 'dir';
    // url的参数，排序字段
    protected $_sort = 'sort';
    // url的参数，页数
    protected $_page = 'p';
    // url的参数，价格
    protected $_filterPrice = 'price';
    // url的参数，价格
    protected $_filterPriceAttr = 'price';
    // 产品总数
    protected $_productCount;
    protected $_filter_attr;
    protected $_numPerPageVal;
    
    public function init()
    {
        parent::init();
        $this->getQuerySort();
    }

    protected $_sort_items;

    public function getQuerySort()
    {
        if (!$this->_sort_items) {
            $category_sorts = Yii::$app->store->get('category_sort');
            if (is_array($category_sorts)) {
                foreach ($category_sorts as $one) {
                    $sort_key        = $one['sort_key'];
                    $sort_label      = $one['sort_label'];
                    $sort_db_columns = $one['sort_db_columns'];
                    $sort_direction  = $one['sort_direction'];
                    $this->_sort_items[$sort_key] = [
                        'label'      => $sort_label,
                        'db_columns' => $sort_db_columns,
                        'direction'  => $sort_direction,
                    ];
                }
            }
        }
        
    }
    
    public function getLastData()
    {
        // 每页显示的产品个数，进行安全验证，如果个数不在预先设置的值内，则会报错。
        // 这样是为了防止恶意攻击，也就是发送很多不同的页面个数的链接，绕开缓存。
        $this->getNumPerPage();
        
        if(!$this->initCategory()){
            Yii::$service->url->redirect404();
            return;
        }

        // return the products of this category's messages.
        $productCollInfo     = $this->getCategoryProductColl();
        $products         = $productCollInfo['coll'];
        $this->_productCount = $productCollInfo['count'];
        

        // return this category's messages.
        $category_name        = Yii::$service->store->getStoreAttrVal($this->_category['name'], 'name');
        $category_image       = $this->_category['image'] ? Yii::$service->category->image->getUrl($this->_category['image']) : '';
        $category_description = Yii::$service->store->getStoreAttrVal($this->_category['description'], 'description');

        return [
            'category_name'         => $category_name,
            'name_default_lang'     => Yii::$service->fecshoplang->getDefaultLangAttrVal($this->_category['name'], 'name'),
            'category_image'        => $category_image,
            'category_description'  => $category_description,
            'products'              => $products,
            'product_count'         => $this->_productCount,
            'query_item'            => $this->getQueryItem(),
            'pagination'            => $this->getProductPage(),
            'product_mini_page'     => $this->getProductMiniPage(),
            'refine_by_info'        => $this->getRefineByInfo(),
            'filter_info'           => Yii::$service->category->getFilterInfo($this->_category, $this->_where),
            'filter_price'          => $this->getFilterPrice(),
            'filter_category'       => $this->getFilterCategoryHtml(),
            'categoryM'             => $this->_category,
        ];
    }

    // 得到子分类，如果子分类不存在，则返回同级分类。
    protected function getFilterCategory()
    {
        $category_id     = $this->_primaryVal;
        $parent_id       = $this->_category['parent_id'];
        $filter_category = Yii::$service->category->getFilterCategory($category_id, $parent_id);

        return $filter_category;
    }
    /**
     * @param $filter_category | Array
     * 通过递归的方式，得到分类以及子分类的html。
     */
    protected function getFilterCategoryHtml($filter_category = '')
    {
        $str = '';
        if (!$filter_category) {
            $filter_category = $this->getFilterCategory();
        }
        if (!Yii::$service->category->isEnableFilterSubCategory()) {
            
            return $str;
        }
        if (is_array($filter_category) && !empty($filter_category)) {
            $str .= '<ul>';
            foreach ($filter_category as $cate) {
                $name    = Yii::$service->store->getStoreAttrVal($cate['name'], 'name');
                $url     = Yii::$service->url->getUrl($cate['url_key']);
                $current = '';
                if (isset($cate['current']) && $cate['current']) {
                    $current = 'class="current"';
                }
                $str .= '<li '.$current.'><a href="'.$url.'">'.$name.'</a>';
                if (isset($cate['child']) && is_array($cate['child']) && !empty($cate['child'])) {
                    $str .= $this->getFilterCategoryHtml($cate['child']);
                }
                $str .= '</li>';
            }
            $str .= '</ul>';
        }
        //exit;
        return $str;
    }
    
    /**
     * 得到产品页面的toolbar部分
     * 也就是分类页面的分页工具条部分。
     */
    protected function getProductMiniPage()
    {
        $productNumPerPage = $this->getNumPerPage();
        $productCount = $this->_productCount;
        $pageNum = $this->getPageNum();
        $config = [
            'class'       => 'fecshop\app\appfront\widgets\Pagination',
            'view'        => 'widgets/page_mini.php',
            'method'      => 'getMiniBar',
            'pageNum'     => $pageNum,
            'numPerPage'  => $productNumPerPage,
            'countTotal'  => $productCount,
            'page'        => $this->_page,
        ];

        return Yii::$service->page->widget->renderContent('category_product_page', $config);
    }
    
    /**
     * 得到产品页面的toolbar部分
     * 也就是分类页面的分页工具条部分。
     */
    protected function getProductPage()
    {
        // 一ページの商品量の制限をチェックする。
        $productsLimitPerPage = $this->getNumPerPage();
        // 商品の合計を取る。
        $productCount = $this->_productCount;
        // ページのナンバーを取る
        $pageNum = $this->getPageNum();

        $config = [
            'class'         => 'fecshop\app\appfront\widgets\Pagination',
            'view'          => 'widgets/pagination.php',
            'pageNum'       => $pageNum,
            'numPerPage'    => $productsLimitPerPage,
            'countTotal'    => $productCount,
            'page'          => $this->_page,
        ];

        return Yii::$service->page->widget->renderContent('widgets_pagination', $config);
    }
    /**
     * 分类页面toolbar部分：
     * 产品排序，产品每页的产品个数等，为这些部分提供数据。
     */
    protected function getQueryItem()
    {
        //$category_query  = Yii::$app->controller->module->params['category_query'];
        //$numPerPage      = $category_query['numPerPage'];
        
        $appName = Yii::$service->helper->getAppName();
        $numPerPage = Yii::$app->store->get($appName.'_catalog','category_query_numPerPage');
        $numPerPage = explode(',', $numPerPage);
        $sort                   = $this->_sort_items;
        $frontNumPerPage = [];
        if (is_array($numPerPage) && !empty($numPerPage)) {
            $attrUrlStr = $this->_numPerPage;
            foreach ($numPerPage as $np) {
                $urlInfo = Yii::$service->url->category->getFilterChooseAttrUrl($attrUrlStr, $np, $this->_page);
                //var_dump($url);
                //exit;
                $frontNumPerPage[] = [
                    'value'    => $np,
                    'url'        => $urlInfo['url'],
                    'selected'    => $urlInfo['selected'],
                ];
            }
        }
        $frontSort = [];
        $hasSelect = false;
        if (is_array($sort) && !empty($sort)) {
            $attrUrlStr = $this->_sort;
            $dirUrlStr  = $this->_direction;
            foreach ($sort as $np=>$info) {
                $label      = $info['label'];
                $direction  = $info['direction'];
                $arr['sort']= [
                    'key' => $attrUrlStr,
                    'val' => $np,
                ];
                $arr['dir'] = [
                    'key' => $dirUrlStr,
                    'val' => $direction,
                ];
                $urlInfo = Yii::$service->url->category->getFilterSortAttrUrl($arr, $this->_page);
                if ($urlInfo['selected']) {
                    $hasSelect = true;
                }
                $frontSort[] = [
                    'label'     => $label,
                    'value'     => $np,
                    'url'       => $urlInfo['url'],
                    'selected'  => $urlInfo['selected'],
                ];
            }
        }
        if (!$hasSelect ){ // 默认第一个为选中的排序方式
            $frontSort[0]['selected'] = true;
        }
        $data = [
            'frontNumPerPage' => $frontNumPerPage,
            'frontSort'       => $frontSort,
        ];

        return $data;
    }
    /**
     * @return Array
     * 得到当前分类，侧栏用于过滤的属性数组，由三部分计算得出
     * 1.全局默认属性过滤（catalog module 配置文件中配置 category_filter_attr），
     * 2.当前分类属性过滤，也就是分类表的 filter_product_attr_selected 字段
     * 3.当前分类去除的属性过滤，也就是分类表的 filter_product_attr_unselected
     * 最终出来一个当前分类，用于过滤的属性数组。
     */
    protected function getFilterAttr()
    {
        return Yii::$service->category->getFilterAttr($this->_category);
    }
    
    // 得到分类侧栏用于属性过滤的部分数据。
    protected function getRefineByInfo()
    {
        $get_arr = Yii::$app->request->get();
        //var_dump($get_arr);
        if (is_array($get_arr) && !empty($get_arr)) {
            $refineInfo = [];
            $filter_attrs = $this->getFilterAttr();
            $filter_attrs[] = 'price';
            //var_dump($filter_attrs);
            $currentUrl = Yii::$service->url->getCurrentUrl();
            foreach ($get_arr as $k=>$v) {
                $attr = Yii::$service->url->category->urlStrConvertAttrVal($k);
                //echo $attr;
                if (in_array($attr, $filter_attrs)) {
                    $refine_attr_str = '';
                    if ($attr == 'price') {
                        $refine_attr_str = $this->getFormatFilterPrice($v);
                        //$refine_attr_str = Yii::$service->url->category->urlStrConvertAttrVal($v);
                    } else {
                        $refine_attr_str = Yii::$service->category->getCustomCategoryFilterAttrItemLabel($k, $v);
                        if (!$refine_attr_str) {
                            $refine_attr_str = Yii::$service->url->category->urlStrConvertAttrVal($v);
                        }
                    }
                    $removeUrlParamStr = $k.'='.$v;
                    $refine_attr_url = Yii::$service->url->removeUrlParamVal($currentUrl, $removeUrlParamStr);
                    $attrLabel = Yii::$service->category->getCustomCategoryFilterAttrLabel($attr);
                    $refineInfo[] = [
                        'name' =>  $refine_attr_str,
                        'url'  =>  $refine_attr_url,
                        'attr' => $attr,
                        'attrLabel' => $attrLabel,
                    ];
                }
            }
        }
        if (!empty($refineInfo)) {
            $arr[] = [
                'name'    => 'clear all',
                'url'    => Yii::$service->url->getCurrentUrlNoParam(),
            ];
            $refineInfo = array_merge($arr, $refineInfo);
        }
        // var_dump( $refineInfo);
        return $refineInfo;
    }
    
    // 侧栏价格过滤部分。
    protected function getFilterPrice()
    {
        $filter = [];
        if (!Yii::$service->category->isEnableFilterPrice()) {
            
            return $filter;
        }
        //$priceInfo = Yii::$app->controller->module->params['category_query'];
        $appName = Yii::$service->helper->getAppName();
        $category_query_priceRange = Yii::$app->store->get($appName.'_catalog','category_query_priceRange');
        $category_query_priceRange = explode(',',$category_query_priceRange);
        if ( !empty($category_query_priceRange) && is_array($category_query_priceRange)) {
            foreach ($category_query_priceRange as $price_item) {
                $price_item = trim($price_item);
                $info = Yii::$service->url->category->getFilterChooseAttrUrl($this->_filterPrice, $price_item, $this->_page);
                $info['val'] = $this->getFormatFilterPrice($price_item);
                $filter[$this->_filterPrice][] = $info;
            }
        }

        return $filter;
    }
    
    // 格式化价格格式，侧栏价格过滤部分。
    protected function getFormatFilterPrice($price_item)
    {
        list($f_price, $l_price) = explode('-', $price_item);
        $str = '';
        if ($f_price == '0' || $f_price) {
            $f_price = Yii::$service->product->price->formatPrice($f_price);
            $str .= $f_price['symbol'].$f_price['value'].'---';
        }
        if ($l_price) {
            $l_price = Yii::$service->product->price->formatPrice($l_price);
            $str .= $l_price['symbol'].$l_price['value'];
        }

        return $str;
    }
    
    /**
     * 用于搜索条件的排序部分
     */
    protected function getOrderBy()
    {
        $primaryKey = Yii::$service->category->getPrimaryKey();
        $sort = Yii::$app->request->get($this->_sort);
        $direction = Yii::$app->request->get($this->_direction);

        $sortConfig = $this->_sort_items;
        if (is_array($sortConfig)) {
            if ($sort && isset($sortConfig[$sort])) {
                $orderInfo = $sortConfig[$sort];
            } else {
                foreach ($sortConfig as $k => $v) {
                    $orderInfo = $v;
                    if (!$direction) {
                        $direction = $v['direction'];
                    }
                    break;
                }
            }
            $db_columns = $orderInfo['db_columns'];
            $storageName = Yii::$service->product->serviceStorageName();
            if ($direction == 'desc') {
                $direction =  $storageName == 'mongodb' ? -1 :  SORT_DESC;
            } else {
                $direction = $storageName == 'mongodb' ? 1 :SORT_ASC;
            }
            
            return [$db_columns => $direction];
        }
        
    }
    /**
     * 分类页面的产品，每页显示的产品个数。
     * 对于前端传递的个数参数，在后台验证一下是否是合法的个数（配置里面有一个分类产品个数列表）
     * 如果不合法，则报异常
     * 这个功能是为了防止分页攻击，伪造大量的不同个数的url，绕过缓存。
     */
    protected function getNumPerPage()
    {
        if (!$this->_numPerPageVal) {
            // 获取url的请求参数，每页产品个数。
            $numPerPage = Yii::$app->request->get($this->_numPerPage);
            //$category_query_config = Yii::$app->getModule('catalog')->params['category_query'];
            // 获取当前app入口，即appfront。
            $appName = Yii::$service->helper->getAppName();
            $categoryConfigNumPerPage = Yii::$app->store->get($appName.'_catalog','category_query_numPerPage');
            $category_query_config['numPerPage'] = explode(',',$categoryConfigNumPerPage);
            if (!$numPerPage) {
                if (isset($category_query_config['numPerPage'])) {
                    if (is_array($category_query_config['numPerPage'])) {
                        $this->_numPerPageVal = $category_query_config['numPerPage'][0];
                    }
                }
            } elseif (!$this->_numPerPageVal) {
                if (isset($category_query_config['numPerPage']) && is_array($category_query_config['numPerPage'])) {
                    $numPerPageArr = $category_query_config['numPerPage'];
                    if (in_array((int) $numPerPage, $numPerPageArr)) {
                        $this->_numPerPageVal = $numPerPage;
                    } else {
                        throw new InvalidValueException('Incorrect numPerPage value:'.$numPerPage);
                    }
                }
            }
        }

        return $this->_numPerPageVal;
    }
    
    // 得到当前第几页
    protected function getPageNum()
    {
        // 获取url的请求参数p的值。
        $numPerPage = Yii::$app->request->get($this->_page);
        // p有值就是p，没有值默认为第一页。
        return $numPerPage ? (int) $numPerPage : 1;
    }
    
    // 得到当前分类的产品
    protected function getCategoryProductColl()
    {
        $productPrimaryKey = Yii::$service->product->getPrimaryKey();
        $select = [
            $productPrimaryKey, 'sku', 'spu', 'name', 'image',
            'price', 'special_price',
            'special_from', 'special_to','is_in_stock',
            'url_key', 'score', 'reviw_rate_star_average', 'review_count'
        ];
        if (is_array($this->_sort_items)) {
            foreach ($this->_sort_items as $sort_item) {
                $select[] = $sort_item['db_columns'];
            }
        }
        $filter = [
            'pageNum'     => $this->getPageNum(),
            'numPerPage'  => $this->getNumPerPage(),
            'orderBy'     => $this->getOrderBy(),
            'where'       => $this->_where,
            'select'      => $select,
        ];
        //var_dump($filter);exit;
        return Yii::$service->category->product->getFrontList($filter);
    }
    
    // 得到用于查询的where数组。
    protected function initWhere()
    {
        $filterAttr = $this->getFilterAttr();
        foreach ($filterAttr as $attr) {
            $attrUrlStr = Yii::$service->url->category->attrValConvertUrlStr($attr);
            $val = Yii::$app->request->get($attrUrlStr);
            if ($val) {
                $val = Yii::$service->url->category->urlStrConvertAttrVal($val);
                $where[$attr] = $val;
            }
        }
        $filter_price = Yii::$app->request->get($this->_filterPrice);
        list($f_price, $l_price) = explode('-', $filter_price);
        if ($f_price == '0' || $f_price) {
            $where[$this->_filterPriceAttr]['$gte'] = (float) $f_price;
        }
        if ($l_price) {
            $where[$this->_filterPriceAttr]['$lte'] = (float) $l_price;
        }
        $where['category'] = $this->_primaryVal;
        //var_dump($where);exit;
        return $where;
    }
    
    // 分类部分的初始化，对一些属性进行赋值。
    protected function initCategory()
    {
        // 获取分类的主键
        $primaryKey = Yii::$service->category->getPrimaryKey();
        // 获取请求对分类的主键的值。
        $primaryVal = Yii::$app->request->get($primaryKey);
        // 全局共享数据。
        $this->_primaryVal = $primaryVal;
        // 通过主键获取分类信息。
        $category = Yii::$service->category->getByPrimaryKey($primaryVal);
        // 该主键分类不存在或状态为不显示的情况。
        if ($category) {
            $enableStatus = Yii::$service->category->getCategoryEnableStatus();
            if ($category['status'] != $enableStatus) {
                return false;
            }
        } else {
            return false;
        }
        // 全局共享数据。
        $this->_category = $category;

        // 设置当前语言的页面关键词。
        Yii::$app->view->registerMetaTag([
            'name' => 'keywords',
            'content' => Yii::$service->store->getStoreAttrVal($category['meta_keywords'], 'meta_keywords'),
        ]);
        // 设置当前语言的页面描述。
        Yii::$app->view->registerMetaTag([
            'name' => 'description',
            'content' => Yii::$service->store->getStoreAttrVal($category['meta_description'], 'meta_description'),
        ]);

        // 获取当前语言的页面标题。
        $this->_title = Yii::$service->store->getStoreAttrVal($category['title'], 'title');

        // 获取当前语言的分类名称。
        $name = Yii::$service->store->getStoreAttrVal($category['name'], 'name');
        // 设置面包屑
        $this->breadcrumbs($name);
        $this->_title = $this->_title ? $this->_title : $name;
        // 设置当前语言的页面标题。
        Yii::$app->view->title = $this->_title;
        $this->_where = $this->initWhere();
        return true;
    }

    // 面包屑导航
    protected function breadcrumbs($name)
    {
        // 获取app入口名称
        $appName = Yii::$service->helper->getAppName();
        // 获取相应的面包屑缓存
        $category_breadcrumbs = Yii::$app->store->get($appName.'_catalog','category_breadcrumbs');
        
        if ($category_breadcrumbs == Yii::$app->store->enable) {
            $parent_info = Yii::$service->category->getAllParentInfo($this->_category['parent_id']);
            if (is_array($parent_info) && !empty($parent_info)) {
                foreach ($parent_info as $info) {
                    $parent_name = Yii::$service->store->getStoreAttrVal($info['name'], 'name');
                    $parent_url = Yii::$service->url->getUrl($info['url_key']);
                    Yii::$service->page->breadcrumbs->addItems(['name' => $parent_name, 'url' => $parent_url]);
                }
            }
            Yii::$service->page->breadcrumbs->addItems(['name' => $name]);
        } else {
            Yii::$service->page->breadcrumbs->active = false;
        }
    }
}
