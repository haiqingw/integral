<?php
/**
 * Created by lzh
 * CreateTime: 16/7/7 下午3:07
 * Description:
 */
namespace Paymax\config;
class PaymaxConfig{
	//Paymax服务器地址
	public static $API_BASE_URL = "https://www.paymax.cc/merchant-api";
	//编码集
	public static $CHARSET = "UTF-8";
	//签名后数据的key
	public static $SIGN = "sign";
	//发起实时代扣（代收）的uri
	public static $CHARGE_REAL_TIME = "/v1/real_time/pay";
	//下载对账文件的uri
	public static $STATEMENT_URI = "/v1/statement/download";
	//SDK版本
	public static $SDK_VERSION = "1.0.0";
	//     //paymax secret key
	public static $SECRET_KEY = "";
	//     public static $SECRET_KEY = "6d7180e700864d259cbe2512006a0aaf";
	//     //paymax public key
	public static $MY_PRIVATE_KEY = "";
	//     public static $MY_PRIVATE_KEY = "/key/rsa_private_key.pem";
	//     //my private key
	public static $PAYMAX_PUBLIC_KEY = "";
	//     public static $PAYMAX_PUBLIC_KEY= "/key/paymax_public_key.pem";
	
}