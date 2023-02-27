<?php

namespace fecshop\models\redis;

use yii\redis\ActiveRecord;

class SessionStorage extends ActiveRecord
{
    public static function primaryKey()
    {
        return ['id'];
    }
    
    public function attributes()
    {
        return [
            'id', 'session_uuid',
            'session_key', 'session_value',
            'session_timeout','session_updated_at'
        ];
    }
    /**
     * relations can not be defined via a table as there are not tables in redis. You can only define relations via other records.
     */
}
