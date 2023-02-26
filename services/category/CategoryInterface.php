<?php

namespace fecshop\services\category;

interface CategoryInterface
{
    // 通过主键获取分类
    public function getByPrimaryKey($primaryKey);

    // 查询
    public function coll($filter);

    // 保存
    public function save($one, $originUrlKey);

    // 删除
    public function remove($ids);
}
