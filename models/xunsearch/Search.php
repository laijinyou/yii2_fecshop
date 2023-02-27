<?php

namespace fecshop\models\xunsearch;

class Search extends \hightman\xunsearch\ActiveRecord
{
    public static function projectName()
    {
        return 'search';    // 这将使用 @fecshop/config/xunsearch/search.ini 作为项目名
    }
}
