<?php
//error_reporting(0);
use Paymax\config\SignConfig;
use Paymax\config\PaymaxConfig;

/**
 * Created by lzh
 * CreateTime: 16/7/7 下午2:49
 * Description:
 */


if (!function_exists('curl_init')) {
    throw new Exception('Paymax needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('Paymax needs the JSON PHP extension.');
}
define("ROOT_PATH",dirname(__FILE__));
//配置
require(ROOT_PATH . '/config/PaymaxConfig.php');
require(ROOT_PATH . '/config/SignConfig.php');

//异常
require(ROOT_PATH . '/exception/PaymaxException.php');
require(ROOT_PATH . '/exception/AuthorizationException.php');
require(ROOT_PATH . '/exception/InvalidRequestException.php');
require(ROOT_PATH . '/exception/InvalidResponseException.php');

//model
require(ROOT_PATH . '/model/Paymax.php');
require(ROOT_PATH . '/model/ApiResource.php');
require(ROOT_PATH . '/model/Charge.php');
require(ROOT_PATH . '/model/Refund.php');

//签名和验签
require(ROOT_PATH . '/sign/RSAUtil.php');

//Util
require(ROOT_PATH . '/util/HttpCurlUtil.php');
require(ROOT_PATH . '/util/PaymaxUtil.php');