<?php
/**
 * Created by lzh
 * CreateTime: 16/7/6 下午5:22
 * Description:
 */

namespace Paymax\model;


use Paymax\config\PaymaxConfig;

class Charge extends ApiResource
{
    public static function do_real_time_deduct($params = null)
    {
        return self::_request(PaymaxConfig::$API_BASE_URL.PaymaxConfig::$CHARGE_REAL_TIME,$params);
    }

    public static function retrieve($orderNo)
    {
        if (empty($orderNo)){
            return 'orderNo can not be blank.';
        }
        return self::_request(PaymaxConfig::$API_BASE_URL.PaymaxConfig::$CHARGE_REAL_TIME.'/'.$orderNo);
    }

    public static function do_download_file($params = null)
    {
        return self::_downloadRequest(PaymaxConfig::$API_BASE_URL.PaymaxConfig::$STATEMENT_URI,$params);
    }
}