<?php

namespace fecshop\services\page;

use fecshop\services\Service;
use Yii;

class Footer extends Service
{
    const TEXT_TERMS    = 'footer_text_terms';
    const COPYRIGHT     = 'footer_copyright';
    const FOLLOW_USE    = 'footer_follow_us';
    const PAYMENT_IMG   = 'footer_payment_img';

    // 得到页面底部的html部分
    public function getTextTerms()
    {
        Yii::$service->page->staticblock->get(self::TEXT_TERMS);
    }

    // 得到页面底部的版权部分
    public function getCopyRight()
    {
        Yii::$service->page->staticblock->get(self::COPYRIGHT);
    }

    // 得到页面底部的follow us部分
    public function followUs()
    {
        Yii::$service->page->staticblock->get(self::FOLLOW_USE);
    }

    // 得到页面底部的支付图片部分
    public function getPaymentImg()
    {
        Yii::$service->page->staticblock->get(self::PAYMENT_IMG);
    }
}
