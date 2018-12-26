<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2018年4月20日 上午10:23:09
# Filename: main.php
# Description: paymax main
#================================================
use Paymax\config\PaymaxConfig;
use Paymax\config\SignConfig;
use Paymax\model\Charge;
require_once ("init.php");
class Setc{
	public function __construct(){
	}
	/**
	 * 设置参数
	 * @param string $key
	 * @param string $val
	 */
	public function setParams($key, $val){
		$this->params[$key] = trim($val);
	}
	/**
	 * 接口配置
	 * @date: 2018年4月27日 上午9:47:40
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function initSignConfig($pid){
		$info = $this->get_paymax_key_file_path($pid);
		if($info){
			PaymaxConfig::$SECRET_KEY = $info['secretKey'];
			PaymaxConfig::$MY_PRIVATE_KEY = $info['privateKeyUrl'];
			PaymaxConfig::$PAYMAX_PUBLIC_KEY = $info['publicKeyUrl'];
			SignConfig::setSecretKey(PaymaxConfig::$SECRET_KEY);
			SignConfig::setPrivateKeyPath(ROOT_PATH . PaymaxConfig::$MY_PRIVATE_KEY);
			SignConfig::setPaymaxPublicKeyPath(ROOT_PATH . PaymaxConfig::$PAYMAX_PUBLIC_KEY);
		}
	}
	/**
	 * 获取接口秘钥信息
	 * @date: 2018年6月5日 下午2:38:02
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	private function get_paymax_key_file_path($plat){
		if(!empty($plat)){
			$field = array('secretKey', 
				'publicKeyUrl', 'privateKeyUrl');
			return M("paymax")->field($field)->where(array(
				"companyID" => $plat))->find();
		}
		return false;
	}
}
/**
 * 代付
 * @date: 2018年4月27日 上午9:45:45
 * @author: HaiQing.Wu <398994668@qq.com>
 */
class RealTimeDeduct extends Setc{
	/**
	 * -----------------
	 * order_no 订单号
	 * mobile_no 手机号
	 * bank_account_no 卡号
	 * bank_account_name 姓名
	 * amount 代付金额
	 * extra  预留字段
	 * comment 自定义摘
	 * -----------------
	 */
	protected $params = ['order_no' => "", 
		'mobile_no' => '', 'account_type' => '1', 
		'bank_account_no' => '', 
		'bank_account_name' => '', 
		'amount' => '1.00', 'extra' => '1038', 
		'comment' => ''];
	/**
	 * 发起实时代扣
	 */
	public function DoChargeRealTime($pid){
		$re = $this->initSignConfig($pid);
		$jsonData = Charge::do_real_time_deduct($this->params);
		$retArray = json_decode($jsonData,true);
		if($retArray['failure_code'] == 'SUCCESS'){
			$ret = array("responceStatus" => 1);
		}else{
			$ret = array("responceStatus" => 2);
		}
		$ret['data'] = $retArray;
		return $ret;
	}
}
/**
 * 代付查询
 * @date: 2018年4月27日 上午9:45:45
 * @author: HaiQing.Wu <398994668@qq.com>
 */
class QueryDeFu extends Setc{
	/**
	 * 查询实时代扣交易
	 */
	public function DoChargeRealTimeQuery($orderNo, $plat){
		$this->initSignConfig($plat);
		return Charge::retrieve($orderNo);
	}
}
class DownLoadCharge extends Setc{
	/**
	 * 下载对账单
	 */
	public function DoDownloadChargeBillFile(){
		$this->initSignConfig();
		$req_data = array(
			'appointDay' => '20170418', 
			'channelCategory' => 'LAKALA', 
			'statementType' => 'CHARGE_BILL');
		echo "\n=============================================================\n";
		echo Charge::do_download_file($req_data);
	}
	/**
	 * 下载回盘文件
	 */
	public function DoDownloadChargeReturnFile(){
		$this->initSignConfig();
		$req_data = array(
			'appointDay' => '20170418', 
			'channelCategory' => 'LAKALA', 
			'statementType' => 'CHARGE_RETURN');
		echo "\n=============================================================\n";
		echo Charge::do_download_file($req_data);
	}
}
/* $deductObj = new RealTimeDeductExample();
$deductObj->DoChargeRealTime();//发起代收(代扣） */
//$deductObj->DoChargeRealTimeQuery('30b80ff2932c4abb9e80da0ac25caa46');//查询实时代收结果信息
//$deductObj->DoDownloadChargeBillFile();//下载对账单
//$deductObj->DoDownloadChargeReturnFile();//下载回盘文件
