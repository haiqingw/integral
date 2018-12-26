<?php
/**
 * +-------------------------------------------
 * | Description: 三要素实名验证 
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2017年2月21日 上午11:08:31
 * +-----------------------------------------------------
 * | Filename: FourElementAuth.class.php
 * +-----------------------------------------------------
 */
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "four_des/des.php";
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "four_des/rsa.php";
include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "Tool.class.php";
define("API_URL","http://api.hfdatas.com/superapi/super/auth/smrz3");
define("USER_CODE","MDWL201702171648030000");
define("DES_KEY","IFv4v/KXehpbPX8c/Tdr6UNd");
define("SYS_CODE","MDWLAPP20170516170044");
define("DES_IV",date("dHis"));
class ThreeElementAuth{
	/**
	 * 验证
	 * @date: 2017年2月21日 上午11:10:31
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return: obj
	 */
	public function check($parm){
		if($this->checkParams($parm,array(
			"name", 
			"idCard", 
			"bank_card", 
		))){
			$config = array(
				"apiUrl" => API_URL, 
				"userCode" => USER_CODE,  //平台分配的用户编号
				"desKey" => DES_KEY,  //平台KEY值(商户接入后才能获取)
				"desIv" => date("dHis"),  //设置偏移量 8位数
				"rsa_key" => array(
					"privateKey" => dirname(__FILE__) . DIRECTORY_SEPARATOR . "four_key/rsa_private_key.pem",  //私钥证书路径
					"publicKey" => dirname(__FILE__) . DIRECTORY_SEPARATOR . "four_key/rsa_public_key.pem"
				)
			);
			$params['header'] = array(
				"qryBatchNo" => date("Ymd") . time(),  //查询批次号(唯一，不超过20位)
				"version" => "2.0", 
				"userCode" => USER_CODE,  //商户编号(平台分配的商户编号)
				"sysCode" => SYS_CODE,  //应用编号(平台创建应用分配的唯一编号)
				"qryReason" => "银行卡四要素实名认证",  //查询原因(简单说明调用原由，可为空)
				"qryDate" => date("Ymd"),  //查询日期(格式：yyyyMMdd，可为空)
				"qryTime" => date("His")
			); //查询时间(格式：hhmmss，可为空)
			$params['condition'] = array(
				"realName" => trim($parm['name']),  //姓名(不超过20位)
				"idCard" => trim($parm['idCard']),  //身份证号码(必须符合身份证标准规范)
				"bankCard" => trim($parm['bank_card']),  //银行卡号
			);
			$jsonParams = json_encode($params);
			unset($params);
			$desObj = new des(DES_KEY,DES_IV);
			$condition = $desObj->encrypt($jsonParams);
			$rsaObj = new rsa($config['rsa_key']);
			$signature = $rsaObj->encode($condition);
			unset($rsaObj);
			$urlParams = array(
				"condition" => $condition, 
				"userCode" => USER_CODE, 
				"signature" => $signature, 
				"vector" => DES_IV
			);
			$par = http_build_query($urlParams);
			$toolObj = new Tool();
			ob_clean();
			$data = $toolObj->curl_request(API_URL,$par);
			$jsonData = json_decode($data);
			if(is_object($jsonData)){
				if(!empty($jsonData->contents)){ //调用成功
					$d = $desObj->decrypt($jsonData->contents);
					$res = json_decode($d,true);
					foreach($res['data'] as $key => $val){
						for($i = 0;$i < count($val['record']);$i++){
							$rc = $val['record'][$i]['resCode'];
							$rm = $val['record'][$i]['resDesc'];
						}
					}
					if($rc == '00'){
						$status = array(
							"status" => 1, 
							"msg" => $rm
						);
					}else{
						$status = array(
							"status" => 2, 
							"msg" => $rm
						);
					}
				}elseif(!empty($jsonData->msg)){ //调用失败
					// 					$fail = json_decode($data,true);
					$status = array(
						"status" => 2, 
						"msg" => "验证失败"
					);
				}
			}else{
				$status = array(
					"status" => 3, 
					"msg" => "检查api地址或网络"
				);
				// 				return "检查api地址或网络";
			}
		}else{
			$status = array(
				"status" => 3, 
				"msg" => "缺少参数"
			);
		}
		return $status;
	}
	/**
	 * 检测元素是否正确
	 * 只能检测一维数组
	 * @param $arr 被检测的数组
	 * @param $repar $arr中必要的元素(键名)
	 * @return boolean
	 */
	function checkParams($arr, $repar = array()){
		if(!is_array($arr)){
			return false;
		}
		if(!count($arr)){
			return false;
		}
		// 判断每个元素是否存在
		if(count($repar)){
			foreach($repar as $val){
				if(!in_array($val,array_keys($arr))){
					return false;
					break;
				}
			}
		}
		// 判断每个元素的值是否正确
		foreach($arr as $val){
			if($val == "" || $val == false || $val == null || $val == "undefined" || $val == "null"){
				return false;
				break;
			}
		}
		return true;
	}
	/**
	 * 验证
	 * @date: 2017年2月21日 上午11:10:31
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return: obj
	 */
	public function check_old($params){
		$config = array(
			"apiUrl" => API_URL, 
			"userCode" => USER_CODE,  //平台分配的用户编号
			"desKey" => DES_KEY,  //平台KEY值(商户接入后才能获取)
			"desIv" => date("dHis"),  //设置偏移量 8位数
			"rsa_key" => array(
				"privateKey" => dirname(__FILE__) . DIRECTORY_SEPARATOR . "four_key/rsa_private_key.pem",  //私钥证书路径 
				"publicKey" => dirname(__FILE__) . DIRECTORY_SEPARATOR . "four_key/rsa_public_key.pem"
			)
		);
		$params['header'] = array(
			"qryBatchNo" => date("Ymd") . time(),  //查询批次号(唯一，不超过20位)
			"version" => "2.0", 
			"userCode" => USER_CODE,  //商户编号(平台分配的商户编号)
			"sysCode" => SYS_CODE,  //应用编号(平台创建应用分配的唯一编号)
			"qryReason" => "银行卡四要素实名认证",  //查询原因(简单说明调用原由，可为空)
			"qryDate" => date("Ymd"),  //查询日期(格式：yyyyMMdd，可为空)
			"qryTime" => date("His")
		); //查询时间(格式：hhmmss，可为空)
		$jsonParams = json_encode($params);
		unset($params);
		$desObj = new des(DES_KEY,DES_IV);
		$condition = $desObj->encrypt($jsonParams);
		$rsaObj = new rsa($config['rsa_key']);
		$signature = $rsaObj->encode($condition);
		unset($rsaObj);
		$urlParams = array(
			"condition" => $condition, 
			"userCode" => USER_CODE, 
			"signature" => $signature, 
			"vector" => DES_IV
		);
		$par = http_build_query($urlParams);
		$toolObj = new Tool();
		$data = $toolObj->curl_request(API_URL,$par);
		$jsonData = json_decode($data);
		if(is_object($jsonData)){
			if(!empty($jsonData->contents)){ //调用成功
				$d = $desObj->decrypt($jsonData->contents);
				file_put_contents("./ssssssssssssssssssssssssssssssssssssssss",var_export(json_decode($d,true),true));
				return json_decode($d,true);
			}elseif(!empty($jsonData->msg)){ //调用失败
				return json_decode($data,true);
			}
		}else{
			return "检查api地址或网络";
		}
	}
}