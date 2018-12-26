<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2018年8月27日 下午2:55:52
# Filename: ThawController.class.php
# Description: 
#================================================
namespace App\Controller;
use Think\Controller;
class ThawController extends BaseController{
	// 待解冻金额
	public function thawmoney($params){
		// 有咩有未激活的机器 刷卡时间是否过期 有没有解冻记录
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if(parent::check($phone, $bid, $pid)){
			$bid = parent::busID(array("phone" => $phone,"plat" => $pid));
			$thawMoney = 0;
			$sql = 'select * from p_terminal_manage where bid = ' . $bid . ' and isActive = 2';
			$row = M()->query($sql);
			if(count($row[0]) > 0){
				$commodityID = $row[0]['proid'];
				$table = M("commodity_category")->where('id=(select category_id from p_commodity where id = '.$commodityID.')')->getField('ruleList');
				$tableName = str_replace("Cashback/","posdata_",$table);
				// 找激活订单
				$trade = M($tableName)->where(array('terminalNo' => $row[0]['terminal']))->order('tradeTime asc')->find();
				// 找解冻条件列表
				$thaw = sRec('thaw', 'status=1||plat=' . $pid . '||commodityID=' . $commodityID, '', '', '');
				if($thaw){
					for($i = 0; $i < count($thaw); $i++){
						// 找解冻记录
						if(!cRec('thaw_log', 'uid=' . $bid . '||thawID=' . $thaw[$i]['id'])){
							// 是否过期
							$lastTime = strtotime(date('Y-m-d H:i:s', $trade['tradeTime']) . " +{$thaw[$i]['days']} day");
							$nowTime = time();
							if($nowTime < $lastTime){
								$thawMoney += $thaw[$i]['cashMoney'];
							}
						}
					}
				}
			}
			$ret = array("responseStatus" => 1,"thawMoney" => $thawMoney);
		}else{
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	// 解冻列表 》 解冻金额 剩余天数 刷卡条件 是否解冻（已解冻、待解冻、已过期） 当前刷卡金额 
	public function thawlist($params){
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if(parent::check($phone, $bid, $pid)){
			$bid = parent::busID(array("phone" => $phone,"plat" => $pid));
			// start
			$sql = 'select * from p_terminal_manage where bid = ' . $bid . ' and isActive = 2';
			$row = M()->query($sql);
			if(count($row[0]) > 0){
				$commodityID = $row[0]['proid'];
				$table = M("commodity_category")->where('id=(select category_id from p_commodity where id = '.$commodityID.')')->getField('ruleList');
				$tableName = str_replace("Cashback/","posdata_",$table);
				// 找激活订单
				$trade = M($tableName)->where(array('terminalNo' => $row[0]['terminal']))->order('tradeTime asc')->find();
				// 找解冻条件列表
				$thaw = sRec('thaw', 'status=1||plat=' . $pid, '', '', '');
				if($thaw){
					$data = array();
					for($i = 0; $i < count($thaw); $i++){
						// 找解冻记录
						if(!cRec('thaw_log', 'uid=' . $bid . '||thawID=' . $thaw[$i]['id'])){
							// 是否过期
							$lastTime = strtotime(date('Y-m-d H:i:s', $trade['tradeTime']) . " +{$thaw[$i]['days']} day");
							$nowTime = time();
							if($nowTime < $lastTime){
								$data[$i]['isThaw'] = "待解冻";
								$data[$i]['surplus'] = ceil(($lastTime - $nowTime) / 86400);
							}else{
								$data[$i]['isThaw'] = "已过期";
								$data[$i]['surplus'] = 0;
							}
						}else{
							// 已解冻
							$data[$i]['isThaw'] = "已解冻";
							$data[$i]['surplus'] = 0;
						}
						$data[$i]['thawMoney'] = $thaw[$i]['cashMoney'];
						$data[$i]['conditions'] = "刷满" . $thaw[$i]['upToStandard'] . "万可解冻" . $thaw[$i]['cashMoney'] . "元";
						$terInfo = getTerminal($bid, $tableName);
						$totalMoney = 0;
						if(!empty($terInfo['tableName'])){
							$totalMoney = M($terInfo['tableName'])->where(array('plat' => $pid,'terminalNo' => $terInfo['terminalNo']))->sum('tradeAmt');
						}
						$data[$i]['nowTotalMoney'] = $totalMoney;
					}
					$ret = array("responseStatus" => 1,"data" => $data);
				}else{
					$ret['responseStatus'] = 300;
				}
			}else{
				$ret['responseStatus'] = 300;
			}
			// end
		}else{
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
}