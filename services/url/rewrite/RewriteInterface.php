<?php

namespace fecshop\services\url\rewrite;

interface RewriteInterface
{
    public function getByPrimaryKey($primaryKey);

    public function coll($filter);

    public function save($one);

    public function remove($ids);

    public function find();

    public function findOne($where);

    public function newModel();
}
