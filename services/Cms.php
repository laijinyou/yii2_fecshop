<?php

namespace fecshop\services;

/**
 * @property \fecshop\services\cms\Article $article
 * @property \fecshop\services\cms\StaticBlock $staticblock
 */
class Cms extends Service
{
    /**
     * cms storage db, you can set value: mysqldb,mongodb.
     */
    public $storage = 'mysqldb';
}
