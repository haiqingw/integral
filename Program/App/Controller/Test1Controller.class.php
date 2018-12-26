<?php
namespace App\Controller;
use Think\Controller;
require_cache("./Public/ttf/class/paymax/main.php");
class Test1Controller extends Controller{
	//拉卡拉代付接口测试
	public function daifutest(){
    	$obj = new \RealTimeDeduct();
        $ordernum = "CESHITIXIAN" . time();
		$obj->setParams("order_no",$ordernum);
		$obj->setParams("mobile_no","13354872086");
		$obj->setParams("bank_account_no","6236680410000187288");
		$obj->setParams("bank_account_name","吴海青");
		$obj->setParams("amount","10.00");
		$obj->setParams("extra","12");
		$obj->setParams("comment","收益提现");
		$rer = $obj->DoChargeRealTime(195);
		var_dump($rer);
	}
	//手动退款
	public function wxtk(){
		$res = $this->refund("2018092999614",0,0);
		dump($res);
	}
	protected function refund($orderNo,$totalFee,$refundFee){
		importTTF("WxApi.class.php");
		$api = new \Refund();
		$refundNo = "TK".date("YmdHis");
		$api->initParams($orderNo, $refundNo, $totalFee, $refundFee);
		$response = $api->refundHandle();
		$response = get_object_vars($response);
		file_put_contents("./Uploads/logs/TK" . date('Y-m-d') . '.log', var_export($response,true));
		return $response;
	}
}
