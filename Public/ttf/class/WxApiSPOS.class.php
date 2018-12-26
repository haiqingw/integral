<?php
/*=============================================================================
#
# Author: hojk - hojk@foxmail.com
#
# Last modified: 2016-02-18 19:07
#
# Filename:	WxApi.class.php
#
# Description: 微信Api整合 (企业付款/统一下单/其他功能)
#
=============================================================================*/

//异常类
class WxException extends Exception {
	public function errorMessage(){
		return $this->getMessage();
	}
}

//微信公众号默认参数
class DefaultConfig{
//     const APPID = "wxd0c1332a83dfeec0";                         //公众号appid
//     const APPSECRET= "e3fb834dc7d8ea679a5c9f1385af9406";        //公众号secret
    const APPID = "wx3800bd6addca264f";                         //公众号appid
    const APPSECRET= "728f0d6ae1d5a67d3d12473829b7ebc2";        //公众号secret
    const KEY= "7c4d2bcacbd979cb33ddb1364403ddc8";              //商户支付秘钥
    const MCHID= "1501119481";                                  //商户号
    const SUBMCHID= "";                                  // 小掌柜
    //const SUBMCHID= "";                                  //  安掌柜
    const NOTIFY_URL= "";                                       //默认回调地址
    const FK_MCHID="";
}

class Refund extends WxApi{
	private $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
	private $params = array(
		"appid"=>DefaultConfig::APPID,  //公众账号appid 必填
		"mch_id"=>DefaultConfig::MCHID,    //商户号 必填
		"nonce_str"=>"",    //随机字符串 必填
		//"sign"=>"", //签名 必填
		"out_trade_no"=>"", //商户订单号 唯一 必填
		"out_refund_no"=>"", //商户退款订单号 唯一 必填
		"total_fee"=>"",   // 订单金额 分
		"refund_fee"=>"",   // 退款金额 分
		"refund_desc"=>"押金退还",   //	退款原因
		//"notify_url"=>"", //退款结果通知url
	);
	// 退款参数初始化
	public function initParams($orderNo,$refundNo,$totalFee,$refundFee){
		$this->params['nonce_str']=parent::random_str();
		$this->params['out_trade_no']=$orderNo;
		$this->params['out_refund_no']=$refundNo;
		$this->params['total_fee']=$totalFee;
		$this->params['refund_fee']=$refundFee;
		$this->params['sign']=$this->getSign($this->params);
	}
	
	// 退款操作
	public function refundHandle(){
		$inputObj = $this->params;
		//检测必填参数
		if(!$inputObj['out_trade_no']) {
			throw new WxException("缺少接口必填参数out_trade_no！");
		}else if(!$inputObj['out_refund_no']){
			throw new WxException("缺少接口必填参数out_refund_no！");
		}else if(!$inputObj['total_fee']) {
			throw new WxException("缺少接口必填参数total_fee！");
		}else if(!$inputObj['refund_fee']) {
			throw new WxException("缺少接口必填参数refund_fee！");
		}
		return simplexml_load_string($this->curl_post_ssl($this->url,$this->arrayToXml($this->params)),'SimpleXMLElement', LIBXML_NOCDATA);
	}
}

class WxApi{

    //企业付款_请求地址
    private $smUrl="https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";

    //企业付款_参数
    private $paramsPayer=array(
        "mch_appid"=>DefaultConfig::APPID,  //公众账号appid 必填
        "mchid"=>DefaultConfig::FK_MCHID,    //商户号 必填
        //"device_info"=>"",  //微信支付分配的终端设备号 非必填
        "nonce_str"=>"",    //随机字符串 必填
        //"sign"=>"", //签名 必填
        "partner_trade_no"=>"", //商户订单号 唯一 必填
        "openid"=>"o6ON0v-pNGyube21pVXf7U80MMs8",   // 用户openid 必填
        "check_name"=>"NO_CHECK",   //	NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账） OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
        "re_user_name"=>"测试", //收款用户真实姓名 如果check_name设置为FORCE_CHECK或OPTION_CHECK，则必填用户真实姓名
        "amount"=>100,    //金额 分
        "desc"=>"货款", //企业付款操作说明信息 必填
        "spbill_create_ip"=>""  //调用接口的机器IP地址
    );

    //企业付款_初始化一批变量
    public function initPayer(){
        $this->paramsPayer['nonce_str']=self::random_str();
        $this->paramsPayer['partner_trade_no']="YDSM_".date('YmdHis').mt_rand(1000,9999);
        $this->paramsPayer['spbill_create_ip']=$_SERVER['REMOTE_ADDR'];
        //$this->paramsPayer['spbill_create_ip']="192.168.1.110";
        $this->paramsPayer['sign']=$this->getSign($this->paramsPayer);
    }

    //企业付款_发送
    public function sendMoney(){
        //$this->initPayer();
        //var_dump($this->paramsPayer);
		return simplexml_load_string($this->curl_post_ssl($this->smUrl,$this->arrayToXml($this->paramsPayer)),'SimpleXMLElement', LIBXML_NOCDATA);
	}
    //获取企业付款商户订单号
    public function get_partner_trade_no(){
        return $this->paramsPayer['partner_trade_no'];
    }
    
    //获取openid 和 session_key
    public function getSessionKey($code){
    	$url = "https://api.weixin.qq.com/sns/jscode2session?appid=".DefaultConfig::APPID."&secret=".DefaultConfig::APPSECRET."&js_code={$code}&grant_type=authorization_code";
    	$data = self::curl_request($url);
    	$data = json_decode($data,true);
    	return $data;
    }

    //统一下单_请求地址
    private $uoUrl="https://api.mch.weixin.qq.com/pay/unifiedorder";

    //统一下单_参数
    private $paramsUnifiedorder=array(
        'appid'=>DefaultConfig::APPID,  //微信分配的公众账号ID
        'mch_id'=>DefaultConfig::MCHID, //微信支付分配的商户号
        //'sub_mch_id'=>DefaultConfig::SUBMCHID,
        //'device_info'=>'', //终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB"
        'nonce_str'=>'',  //随机字符串，不长于32位。推荐随机数生成算法
        //'sign'=>'', //签名，详见签名生成算法
        'body'=>'test', //商品或支付单简要描述
        'detail'=>'test',   //商品名称明细列表
        'attach'=>'id=1',   //附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
        'out_trade_no'=>'', //商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
        //'fee_type'=>'', //符合ISO 4217标准的三位字母代码，默认人民币：CNY，其他值列表详见货币类型
        'total_fee'=>'100',    //订单总金额，单位为分，详见支付金额
        'spbill_create_ip'=>'', //APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP。
        'time_start'=>'',   //订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
        'time_expire'=>'',  //订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则 注意：最短失效时间间隔必须大于5分钟
        //'goods_tag'=>'',    //商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
        'notify_url'=>'http://www.weixin.qq.com/wxpay/pay.php',   //接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数。
        'trade_type'=>'NATIVE',   //取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
        'product_id'=>'123456789',   //trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义。
        //'limit_pay'=>'',    //no_credit--指定不能使用信用卡支付
        //'openid'=>'',   //当trade_type=JSAPI时，此参数必传
    );
    //统一下单_初始化一批变量
    public function initUnifiedorder(){
        $this->paramsUnifiedorder['nonce_str']=self::random_str();
        $this->paramsUnifiedorder['spbill_create_ip']=$_SERVER['REMOTE_ADDR'];
        $this->paramsUnifiedorder['time_start']=date("YmdHis");
        $this->paramsUnifiedorder['time_expire']=date("YmdHis", time() + 600);
    }
    //获取统一下单商户订单号
    public function get_out_trade_no(){
        return $this->paramsUnifiedorder['out_trade_no'];
    }
    //统一下单
    public function unifiedorder(){

        $this->initUnifiedorder();
        $this->paramsUnifiedorder['sign']=$this->getSign($this->paramsUnifiedorder);
        $inputObj=$this->paramsUnifiedorder;

        //检测必填参数
		if(!$inputObj['out_trade_no']) {
			throw new WxException("缺少统一支付接口必填参数out_trade_no！");
		}else if(!$inputObj['body']){
			throw new WxException("缺少统一支付接口必填参数body！");
		}else if(!$inputObj['total_fee']) {
			throw new WxException("缺少统一支付接口必填参数total_fee！");
		}else if(!$inputObj['trade_type']) {
			throw new WxException("缺少统一支付接口必填参数trade_type！");
		}

        //关联参数
		if($inputObj['trade_type'] == "JSAPI" && !$inputObj['openid']){
			throw new WxException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！");
		}
		if($inputObj['trade_type'] == "NATIVE" && !$inputObj['product_id']){
			throw new WxException("统一支付接口中，缺少必填参数product_id！trade_type为NATIVE时，product_id为必填参数！");
		}

        //将对象数组转换成xml
        $xml=self::arrayToXml($inputObj);
        $response=self::curl_request($this->uoUrl,$xml);
        //格式化xml
        $array=$this->FromXml($response);
        //var_dump($array);
        return $array;

    }
    /*
     * 修改默认参数
     * $params $type 1 企业付款参数 2 统一下单参数
     */
    public function setParams($name,$value,$type = 2){
        switch($type){
            case 1:
                $this->paramsPayer[$name]=$value;
                break;
            case 2:
            	$this->paramsUnifiedorder[$name]=$value;
                break;
        }
    }
    /**
 	 * 
 	 * 支付结果通用通知
 	 * @param function $callback
 	 * 直接回调函数使用方法: notify(you_function);
 	 * 回调类成员函数方法:notify(array($this, you_function));
 	 * $callback  原型为：function function_name($data){}
 	 */
	public static function notify($callback, &$msg){
		//获取通知的数据
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		//如果返回成功则验证签名
		try {
			$result = WxPayResults::Init($xml);
		} catch (WxException $e){
			$msg = $e->errorMessage();
			return false;
		}
		
		return call_user_func($callback, $result);
	}
    /**
	 * 
	 * 查询订单，WxPayOrderQuery中out_trade_no、transaction_id至少填一个
	 * appid、mchid、spbill_create_ip、nonce_str不需要填入
	 * @param WxPayOrderQuery $inputObj
	 * @param int $timeOut
	 * @throws WxException
	 * @return 成功时返回，其他抛异常
	 */
	public static function orderQuery($inputObj) {
		$url = "https://api.mch.weixin.qq.com/pay/orderquery";
		//检测必填参数
		if(!$inputObj->IsOut_trade_noSet() && !$inputObj->IsTransaction_idSet()) {
			throw new WxException("订单查询接口中，out_trade_no、transaction_id至少填一个！");
		}
		$inputObj->SetAppid(DefaultConfig::APPID);//公众账号ID
		$inputObj->SetMch_id(DefaultConfig::MCHID);//商户号
		$inputObj->SetSubMch_id(DefaultConfig::SUBMCHID);//商户号
		$inputObj->SetNonce_str(self::random_str());//随机字符串
		
		$inputObj->SetSign();//签名
		$xml = $inputObj->ToXml();
		
		//$startTimeStamp = self::getMillisecond();//请求开始时间
		$response = self::curl_request($url,$xml,6);
		$result = WxPayResults::Init($response);
		//self::reportCostTime($url, $startTimeStamp, $result);//上报请求花费时间
		
		return $result;
	}
    /*
    public function getQrcode(){
        $qrUrl="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->getAccessToken();
        $var=array( "expire_seconds"=>604800, "action_name"=>"QR_SCENE", "action_info"=>array( "scene"=>array( "scene_id"=>123 ) ) );
        //获取ticket
        $data=self::curl_request($qrUrl,json_encode($var));
        $ticket=json_decode($data,true);
        $tkUrl="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket['ticket'];
        //利用ticket获取二维码
        $data=self::curl_request($tkUrl);
        $filename="a.jpg";
        file_put_contents($filename,$data);
        return $filename;
    }
     */
    //获取access_token
    public static function getAccessToken(){
        $acUrl="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".DefaultConfig::APPID."&secret=".DefaultConfig::APPSECRET;
        $data=self::curl_request($acUrl);
        $data=json_decode($data,true);
        return $data['access_token'];
    }
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxException
     */
	public function FromXml($xml) {	
		if(!$xml){
			throw new WxException("xml数据异常！");
		}
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $values;
	}
    /**
     * 生成随机数
     */
    static function random_str( $length = 16 ) {  
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  
		$str ="";  
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
			//$str .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
		}  
		return $str;  
	}

    /**
     * 	作用：格式化参数，签名过程需要使用
     */
    private function formatBizQueryParaMap($paraMap, $urlencode){
        //var_dump($paraMap);//die;
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if($urlencode) {
                $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        //var_dump($reqPar);//die;
        return $reqPar;
    }

    /**
     * 	作用：生成签名
     */
    public function getSign($Obj) {
        /*
        echo "<pre>";
        var_dump($Obj);//die;
        echo "</pre>";
         */
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
        //echo '【string1】'.$String.'</br>';
        //签名步骤二：在string后加入KEY
        $String = $String."&key=".DefaultConfig::KEY;
        //echo "【string2】".$String."</br>";
        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }
    /*
     * array转换xml
     */
    static function arrayToXml($arr) {
        $xml = "<xml>";
        foreach ($arr as $key=>$val) {
        	 if (is_numeric($val)) {
        	 	$xml.="<".$key.">".$val."</".$key.">"; 
        	 } else{
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        	 } 
        }
        $xml.="</xml>";
        return $xml; 
    }
    /*
     * curl get post
     */
    static function curl_request($url,$data = null,$second=30){
        $curl = curl_init();
		curl_setopt($curl, CURLOPT_TIMEOUT,$second);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /*
     * curl post ssl 带证书
     */
    protected function curl_post_ssl($url, $vars, $second=30,$aHeader=array()){
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
		//这里设置代理，如果有的话
		//curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
		//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
		
		//以下两种方式需选择一种
		//第一种方法，cert 与 key 分别属于两个.pem文件
		curl_setopt($ch,CURLOPT_SSLCERT,'D:\wwwroot\Git\ThinkPHP\Library\Vendor\cert\apiclient_cert.pem');
 		curl_setopt($ch,CURLOPT_SSLKEY,'D:\wwwroot\Git\ThinkPHP\Library\Vendor\cert\apiclient_key.pem');
 		curl_setopt($ch,CURLOPT_CAINFO,'D:\wwwroot\Git\ThinkPHP\Library\Vendor\cert\rootca.pem');
		//第二种方式，两个文件合成一个.pem文件
		//curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');
	 
		if( count($aHeader) >= 1 ){
			curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
		}
	 
		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
		$data = curl_exec($ch);
		if($data){
			curl_close($ch);
			return $data;
		} else { 
			$error = curl_errno($ch);
			echo "call faild, errorCode:$error\n"; 
			curl_close($ch);
			return false;
		}
	}

    /*
     * curl post ssl 带证书《微信红包，带服务器域名验证》
     */
    protected function post_sendhb($strXml) {
        $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        //因为微信红包在使用过程中需要验证服务器和域名，故需要设置下面两行
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 只信任CA颁布的证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 检查证书中是否设置域名，并且是否与提供的主机名匹配

        curl_setopt($ch,CURLOPT_SSLCERT,'D:\wwwroot\easypay\cert\apiclient_cert.pem');
        curl_setopt($ch,CURLOPT_SSLKEY,'D:\wwwroot\easypay\cert\apiclient_key.pem');
        curl_setopt($ch,CURLOPT_CAINFO,'D:\wwwroot\easypay\cert\rootca.pem');
        // CA根证书（用来验证的网站证书是否是CA颁布）


        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strXml);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            echo "call faild, errorCode:$error\n";
            curl_close($ch);
            return false;
        }
    }
    /**
	 * 获取毫秒级别的时间戳
	 */
	private static function getMillisecond(){
		//获取毫秒的时间戳
		$time = explode ( " ", microtime () );
		$time = $time[1] . ($time[0] * 1000);
		$time2 = explode( ".", $time );
		$time = $time2[0];
		return $time;
	}
}

/**
 * 
 * 数据对象基础类，该类中定义数据类最基本的行为，包括：
 * 计算/设置/获取签名、输出xml格式的参数、从xml读取数据对象等
 * @author widyhu
 *
 */
class WxPayDataBase
{
	protected $values = array();
	
	/**
	* 设置签名，详见签名生成算法
	* @param string $value 
	**/
	public function SetSign()
	{
		$sign = $this->MakeSign();
		$this->values['sign'] = $sign;
		return $sign;
	}
	
	/**
	* 获取签名，详见签名生成算法的值
	* @return 值
	**/
	public function GetSign()
	{
		return $this->values['sign'];
	}
	
	/**
	* 判断签名，详见签名生成算法是否存在
	* @return true 或 false
	**/
	public function IsSignSet()
	{
		return array_key_exists('sign', $this->values);
	}

	/**
	 * 输出xml字符
	 * @throws WxException
	**/
	public function ToXml()
	{
		if(!is_array($this->values) 
			|| count($this->values) <= 0)
		{
    		throw new WxException("数组数据异常！");
    	}
    	
    	$xml = "<xml>";
    	foreach ($this->values as $key=>$val)
    	{
    		if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml; 
	}
	
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxException
     */
	public function FromXml($xml)
	{	
		if(!$xml){
			throw new WxException("xml数据异常！");
		}
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $this->values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $this->values;
	}
	
	/**
	 * 格式化参数格式化成url参数
	 */
	public function ToUrlParams()
	{
		$buff = "";
		foreach ($this->values as $k => $v)
		{
			if($k != "sign" && $v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 生成签名
	 * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
	 */
	public function MakeSign()
	{
		//签名步骤一：按字典序排序参数
		ksort($this->values);
		$string = $this->ToUrlParams();
		//签名步骤二：在string后加入KEY
		$string = $string . "&key=".DefaultConfig::KEY;
		//签名步骤三：MD5加密
		$string = md5($string);
		//签名步骤四：所有字符转为大写
		$result = strtoupper($string);
		return $result;
	}
	
	/**
	 * 获取设置的值
	 */
	public function GetValues()
	{
		return $this->values;
	}
}
/**
 * 
 * 接口调用结果类
 * @author widyhu
 *
 */
class WxPayResults extends WxPayDataBase
{
	/**
	 * 
	 * 检测签名
	 */
	public function CheckSign()
	{
		//fix异常
		if(!$this->IsSignSet()){
			throw new WxException("签名错误！");
		}
		
		$sign = $this->MakeSign();
		if($this->GetSign() == $sign){
			return true;
		}
		throw new WxException("签名错误！");
	}
	
	/**
	 * 
	 * 使用数组初始化
	 * @param array $array
	 */
	public function FromArray($array)
	{
		$this->values = $array;
	}
	
	/**
	 * 
	 * 使用数组初始化对象
	 * @param array $array
	 * @param 是否检测签名 $noCheckSign
	 */
	public static function InitFromArray($array, $noCheckSign = false)
	{
		$obj = new self();
		$obj->FromArray($array);
		if($noCheckSign == false){
			$obj->CheckSign();
		}
        return $obj;
	}
	
	/**
	 * 
	 * 设置参数
	 * @param string $key
	 * @param string $value
	 */
	public function SetData($key, $value)
	{
		$this->values[$key] = $value;
	}
	
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxException
     */
	public static function Init($xml)
	{	
		$obj = new self();
		$obj->FromXml($xml);
		//fix bug 2015-06-29
		if($obj->values['return_code'] != 'SUCCESS'){
			 return $obj->GetValues();
		}
		$obj->CheckSign();
        return $obj->GetValues();
	}
}

/**
 * 
 * 回调基础类
 * @author widyhu
 *
 */
class WxPayNotifyReply extends  WxPayDataBase
{
	/**
	 * 
	 * 设置错误码 FAIL 或者 SUCCESS
	 * @param string
	 */
	public function SetReturn_code($return_code)
	{
		$this->values['return_code'] = $return_code;
	}
	
	/**
	 * 
	 * 获取错误码 FAIL 或者 SUCCESS
	 * @return string $return_code
	 */
	public function GetReturn_code()
	{
		return $this->values['return_code'];
	}

	/**
	 * 
	 * 设置错误信息
	 * @param string $return_code
	 */
	public function SetReturn_msg($return_msg)
	{
		$this->values['return_msg'] = $return_msg;
	}
	
	/**
	 * 
	 * 获取错误信息
	 * @return string
	 */
	public function GetReturn_msg()
	{
		return $this->values['return_msg'];
	}
	
	/**
	 * 
	 * 设置返回参数
	 * @param string $key
	 * @param string $value
	 */
	public function SetData($key, $value)
	{
		$this->values[$key] = $value;
	}
}
/**
 * 
 * 回调基础类
 * @author widyhu
 *
 */
class WxPayNotify extends WxPayNotifyReply
{
    public $paramsReceive = array();
	/**
	 * 
	 * 回调入口
	 * @param bool $needSign  是否需要签名输出
	 */
	final public function Handle()
	{
		$msg = "OK";
		//当返回false的时候，表示notify中调用NotifyCallBack回调失败获取签名校验失败，此时直接回复失败
		$result = WxApi::notify(array($this, 'NotifyCallBack'), $msg);
		if($result == false){
			$this->SetReturn_code("FAIL");
			$this->SetReturn_msg($msg);
            $this->ReplyNotify(false);
			return false;
		} else {
			//该分支在成功回调到NotifyCallBack方法，处理完成之后流程
			$this->SetReturn_code("SUCCESS");
			$this->SetReturn_msg("OK");
			$this->ReplyNotify(true);
            return $this->paramsReceive;
		}
	}
	/**
	 * 
	 * 回调方法入口，子类可重写该方法
	 * 注意：
	 * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
	 * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
	 * @param array $data 回调解释出的参数
	 * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
	 * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
	 */
	//public function NotifyProcess($data, &$msg)
	//{
		//TODO 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
		//return true;
	//}
	
	/**
	 * 
	 * notify回调方法，该方法中需要赋值需要输出的参数,不可重写
	 * @param array $data
	 * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
	 */
	final public function NotifyCallBack($data)
	{
		$msg = "OK";
		$result = $this->NotifyProcess($data, $msg);
        $this->paramsReceive = $data;
		
		if($result == true){
			$this->SetReturn_code("SUCCESS");
			$this->SetReturn_msg("OK");
		} else {
			$this->SetReturn_code("FAIL");
			$this->SetReturn_msg($msg);
		}
		return $result;
	}
    /**
	 * 
	 * 回复通知
	 * @param bool $needSign 是否需要签名输出
	 */
	final private function ReplyNotify($needSign = true)
	{
		//如果需要签名
		if($needSign == true && 
			$this->GetReturn_code($return_code) == "SUCCESS")
		{
			$this->SetSign();
		}
		echo $this->ToXml();
	}
	
}
/**
 * 
 * 订单查询输入对象
 * @author widyhu
 *
 */
class WxPayOrderQuery extends WxPayDataBase
{
	/**
	* 设置微信分配的公众账号ID
	* @param string $value 
	**/
	public function SetAppid($value)
	{
		$this->values['appid'] = $value;
	}
	/**
	* 获取微信分配的公众账号ID的值
	* @return 值
	**/
	public function GetAppid()
	{
		return $this->values['appid'];
	}
	/**
	* 判断微信分配的公众账号ID是否存在
	* @return true 或 false
	**/
	public function IsAppidSet()
	{
		return array_key_exists('appid', $this->values);
	}


	/**
	* 设置微信支付分配的商户号
	* @param string $value 
	**/
	public function SetMch_id($value)
	{
		$this->values['mch_id'] = $value;
	}
	public function SetSubMch_id($value)
	{
		$this->values['sub_mch_id'] = $value;
	}
	/**
	* 获取微信支付分配的商户号的值
	* @return 值
	**/
	public function GetMch_id()
	{
		return $this->values['mch_id'];
	}
	/**
	* 判断微信支付分配的商户号是否存在
	* @return true 或 false
	**/
	public function IsMch_idSet()
	{
		return array_key_exists('mch_id', $this->values);
	}


	/**
	* 设置微信的订单号，优先使用
	* @param string $value 
	**/
	public function SetTransaction_id($value)
	{
		$this->values['transaction_id'] = $value;
	}
	/**
	* 获取微信的订单号，优先使用的值
	* @return 值
	**/
	public function GetTransaction_id()
	{
		return $this->values['transaction_id'];
	}
	/**
	* 判断微信的订单号，优先使用是否存在
	* @return true 或 false
	**/
	public function IsTransaction_idSet()
	{
		return array_key_exists('transaction_id', $this->values);
	}


	/**
	* 设置商户系统内部的订单号，当没提供transaction_id时需要传这个。
	* @param string $value 
	**/
	public function SetOut_trade_no($value)
	{
		$this->values['out_trade_no'] = $value;
	}
	/**
	* 获取商户系统内部的订单号，当没提供transaction_id时需要传这个。的值
	* @return 值
	**/
	public function GetOut_trade_no()
	{
		return $this->values['out_trade_no'];
	}
	/**
	* 判断商户系统内部的订单号，当没提供transaction_id时需要传这个。是否存在
	* @return true 或 false
	**/
	public function IsOut_trade_noSet()
	{
		return array_key_exists('out_trade_no', $this->values);
	}


	/**
	* 设置随机字符串，不长于32位。推荐随机数生成算法
	* @param string $value 
	**/
	public function SetNonce_str($value)
	{
		$this->values['nonce_str'] = $value;
	}
	/**
	* 获取随机字符串，不长于32位。推荐随机数生成算法的值
	* @return 值
	**/
	public function GetNonce_str()
	{
		return $this->values['nonce_str'];
	}
	/**
	* 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
	* @return true 或 false
	**/
	public function IsNonce_strSet()
	{
		return array_key_exists('nonce_str', $this->values);
	}
}

class PayNotifyCallBack extends WxPayNotify{
	//查询订单
	public function Queryorder($transaction_id){
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg){
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}
//客服类
class CustomerService extends WxApi{
    //添加客服账号
    public static function addCustomerService(){
        $url="https://api.weixin.qq.com/customservice/kfaccount/add?access_token=".parent::getAccessToken();
        $data=array(
            "kf_account" => "hojk@nmgydjt",
            "nickname" => "小美",
            "password" => "123456",
        );
        //$data=json_encode($data,JSON_UNESCAPED_UNICODE);
        $response = parent::curl_request($url,$data);
        echo "<pre>";
        var_dump($response);
        echo "</pre>";
    }
    //获取客服账号
    public static function getCustomerService(){
        $url="https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token=".parent::getAccessToken();
        echo $url;
        $response = parent::curl_request($url);
        echo "<pre>";
        var_dump($response);
        echo "</pre>";
    }
    //发送消息
    public static function sendMessage($openid,$type,$content){
        $url="https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".parent::getAccessToken();
        switch($type){
            case 'text':
                $data=array(
                "touser"=>$openid,
                "msgtype"=>"text",
                "text"=>array(
                "content"=>urlencode($content)
                ),
                );
                break;
            case 'image':
                $data=array(
                "touser"=>$openid,
                "msgtype"=>"image",
                "image"=>array(
                "media_id"=>$content
                ),
                );
                break;
            case 'news':
                $data=array(
                "touser"=>$openid,
                "msgtype"=>"mpnews",
                "mpnews"=>array(
                "media_id"=>$content
                ),
                );
                break;
        }
        $data=urldecode(json_encode($data));
        $response = parent::curl_request($url,$data);
        // file_put_contents("wt.txt",var_export($response,true),FILE_APPEND);
        /*
         echo "<pre>";
         var_dump($response);
         echo "</pre>";
         */
    }
}
/*
 *  刷卡支付
 */
class PosPay extends WxApi{

    /*
     *  接口地址
     */
    private $PosPayUrl = array(
        "submitOrder" => "https://api.mch.weixin.qq.com/pay/micropay",
        "searchOrder" => "https://api.mch.weixin.qq.com/pay/orderquery",
        "cancelOrder" => "https://api.mch.weixin.qq.com/secapi/pay/reverse",
        "refundOrder" => "https://api.mch.weixin.qq.com/secapi/pay/refund",
        "searchRefund" => "https://api.mch.weixin.qq.com/pay/refundquery",
        "downloadBill" => "https://api.mch.weixin.qq.com/pay/downloadbill",
    );
    
    private $paramsPosPay = array();

    public function setPosParams($key,$val){
        $this->paramsPosPay[$key]=$val;
    }
    public function getPosParams($key){
        return $this->paramsPosPay[$key];
    }
    public function init(){
        $this->paramsPosPay = array(
            "appid"=>DefaultConfig::APPID,
            "mch_id"=>DefaultConfig::MCHID,
            "sub_mch_id"=>DefaultConfig::SUBMCHID,
            //"device_info"=>"",
            "nonce_str"=>parent::random_str(),
            //"sign"=>"",
            "body"=>"小掌柜",
            "detail"=>"小掌柜",
            "attach"=>"",
            "out_trade_no"=>"YDSM_".date('YmdHis').parent::random_str(4).mt_rand(1000,9999),
            "total_fee"=>"1",
            //"fee_type"=>"CNY",
            "spbill_create_ip"=>$_SERVER['REMOTE_ADDR'],
            //"goods_tag"=>"",
            //"limit_pay"=>"no_credit",
            "auth_code"=>"",
        );
    }
    public function get_pospay_out_trade_no(){
        return $this->paramsPosPay['out_trade_no'];
    }
    /*
     *  提交刷卡订单
     */
    public function submitPosPay(){

        $url = $this->PosPayUrl['submitOrder'];
        $this->paramsPosPay['sign']=$this->getSign($this->paramsPosPay);
        //var_dump($this->paramsPosPay);
        $xml = $this->arrayToXml($this->paramsPosPay);
        $response = $this->FromXml(parent::curl_request($url,$xml));
        return $response;
        //echo "<pre>";
        //var_dump($response);
        //echo "</pre>";
        
    }
    /*
     *  查询订单
     */
    public function searchPosPay($ordernum){

        $url = $this->PosPayUrl['searchOrder'];
        $params = array(
            "appid"=>DefaultConfig::APPID,
            "mch_id"=>DefaultConfig::MCHID,
            "sub_mch_id"=>DefaultConfig::SUBMCHID,
            "out_trade_no"=>$ordernum,
            "nonce_str"=>parent::random_str(),
        );
        $params['sign']=$this->getSign($params);
        $xml = $this->arrayToXml($params);
        $response = $this->FromXml(parent::curl_request($url,$xml));
        return $response;
        
    }

}


/**
 * 发送模板消息
 * $template=array("openids"=>array('',''),"title"=>"","keyword1"=>"","keyword2"=>"","detail"=>"#");
 * $type 1 交易模板 2 明细模板 3 注册审核模板
 */
class TemplateMessage extends WxApi{
	public static function sendTemplate($openid){
		$access_token=parent::getAccessToken();
// 		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
		$url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=".$access_token;
		$template_id="WbStBJGBiAQHFGeNwZaP7VBR2uUMCoehpdZlNe5U39A";
		$template_data=array(
			'keyword1'=>array('value'=>'1.11元','color'=>'#173177'),
			'keyword2'=>array('value'=>dateFormat(time()),'color'=>'#f60'),
			'keyword3'=>array('value'=>'业务员返现','color'=>'#333'),
		);
		$data = array(
			'touser'=>$openid,
			'template_id'=>$template_id,
			'data'=>$template_data
		);
		$res = parent::curl_request($url,json_encode($data));
		return $res;
	}
    public static function sendTemplateMessage($template,$type=4){
        if(count($template['openids'])==0 || !is_array($template)){
            return false;
        }else{
            $access_token=parent::getAccessToken();
            $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$access_token;
            //dump($res['access_token']);
            $openids=$template['openids'];
            for($i=0;$i<count($openids);$i++){
                switch($type){
                    //实时交易模板
                    case 1:
                        $template_id="tmXL7aCR8SXwYw7lHwOSVRqUYHyASXmXuM2RZnTuBxc";
                        $template_data=array(
                            'first'=>array('value'=>'支付成功！','color'=>'#173177'),
                            'orderMoneySum'=>array('value'=>$template['tmoney'].'元','color'=>'#f60'),
                            'orderProductName'=>array('value'=>$template['bname'],'color'=>'#333'),
                            'Remark'=>array('value'=>'支付时间：'.$template['paytime'],'color'=>'#333'),
                        );
                        break;
                    //交易明细模板
                    case 2:
                        $template_id="eXFOPIsLGUYByUeSsqdM3PyWYnvwXdI0qlUvLIU8Dus";
                        $template_data=array(
                            'first'=>array('value'=>$template['title'],'color'=>'#173177'),
                            'keyword1'=>array('value'=>'已支付订单','color'=>'#f60'),
                            'keyword2'=>array('value'=>$template['keyword2'],'color'=>'#333'),
                            'remark'=>array('value'=>'点击查看详情...','color'=>'#333'),
                        );
                        break;
                    //新用户注册审批提醒
                    case 3:
                        $template_id="ke0BJkNV9nH5PQZ2LYb7S7iHRrHGG_iufTwB8GQhong";
                        $template_data=array(
                            'first'=>array('value'=>$template['title'],'color'=>'#173177'),
                            'keyword1'=>array('value'=>$template['keyword1'],'color'=>'#f60'),
                            'keyword2'=>array('value'=>$template['keyword2'],'color'=>'#333'),
                            'remark'=>array('value'=>'请速速进入小掌柜后台审核','color'=>'#333'),
                        );
                        break;
                    //收款通知
                    case 4:
                        $template_id="Q1ce2BfJEEjNDDfoYyqTNWZHnH7r1tj32uFPwwvtlX8";
                        $template_data=array(
                            'first'=>array('value'=>$template['title'],'color'=>'#173177'),
                            'keyword1'=>array('value'=>$template['keyword1'],'color'=>'#f60'),  //收款门店
                            'keyword2'=>array('value'=>$template['keyword2'],'color'=>'#333'),  //实收金额
                            'keyword3'=>array('value'=>$template['keyword3'],'color'=>'#333'),  //订单金额
                            'keyword4'=>array('value'=>$template['keyword4'],'color'=>'#333'),  //收款时间
                            'keyword5'=>array('value'=>$template['keyword5'],'color'=>'#333'),  //交易单号
                            'remark'=>array('value'=>'小掌柜祝您生活愉快','color'=>'#333'),
                        );
                        break;
                    //提现审核
                    case 5:
                        $template_id="K4t4Tl2ebShRSpWtkvh4KOGH2Xoz63yoDLaXstx0LqU";
                        $template_data=array(
                            'first'=>array('value'=>$template['title'],'color'=>'#173177'),
                            'keyword1'=>array('value'=>$template['keyword1'],'color'=>'#f60'),  //提现金额
                            'keyword2'=>array('value'=>$template['keyword2'],'color'=>'#333'),  //提现方式
                            'keyword3'=>array('value'=>$template['keyword3'],'color'=>'#333'),  //申请时间
                            'keyword4'=>array('value'=>$template['keyword4'],'color'=>'#333'),  //审核结果
                            'keyword5'=>array('value'=>$template['keyword5'],'color'=>'#333'),  //审核时间
                            'remark'=>array('value'=>'请速速进入小掌柜后台审核','color'=>'#333'),
                        );
                        break;
                }
                $data = array(
                    'touser'=>$openids[$i],
                    'template_id'=>$template_id,
                    'url'=>$template['detail'],
                    'data'=>$template_data
                );
                $res = parent::curl_request($url,json_encode($data));
            }
        }
    }

}

//企业红包发送
class QySendHB extends WxApi{
    public function send($openid,$money){
        $params=array(
            "nonce_str"=>parent::random_str(),
            "mch_billno"=>"YDSM".date('YmdHis').self::random_str(4).mt_rand(1000,9999),
            // "mch_billno"=>$ordernum,
            "mch_id"=>DefaultConfig::MCHID,
            "wxappid"=>DefaultConfig::APPID,
            "send_name"=>"小掌柜",
            "re_openid"=>$openid,
            "total_amount"=>$money*100,
            "total_num"=>1,
            "wishing"=>"小掌柜只给你最好的",  //祝福语
            "client_ip"=>$_SERVER['REMOTE_ADDR'],
            "act_name"=>"小掌柜感恩回馈活动",  //活动名称
            "remark"=>"小掌柜.发现世界.发现你！",  //红包备注
        );
        $params['sign']=$this->getSign($params);
        $response = simplexml_load_string($this->post_sendhb($this->arrayToXml($params)),'SimpleXMLElement', LIBXML_NOCDATA);
        $result = get_object_vars($response);
        return $result;
    }
}
