<?php

return [
    /**
     * Payment 模块的配置，您可以在@appfront/config/fecshop_local_modules/Payment.php 
     * 中进行配置，二开，或者重写该模块（在上面路径中如果文件不存在，自行新建配置文件。）
     */
    'payment' => [
        'class' => '\fecshop\app\appfront\modules\Payment\Module',
        // 模块内部的params配置。
        'params'=> [],
    ],
];
