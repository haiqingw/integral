<?php
/*=============================================================================
 #
 # Author: hojk - hojk@foxmail.com
 #
 # Last modified: 2016-02-25 14:31
 #
 # Filename: DrawcashController.class.php
 #
 # Description: 账户提现
 #
 =============================================================================*/
namespace Home\Controller;
use Think\Controller;
class DrawcashController extends CommonController{
	/**
	 * 获取账号提现状态
	 * @param number $bid
	 */
	protected static function getDrawcashStatus($bid){
		$row = fRec(T_BUS,"id=" . $bid,"status");
		if($row['status'] == 1){
// 			file_put_contents("sdddddaaaer",var_export($row,true));
			return true;
		}
		return false;
	}
	//false 关闭 true 不关闭
	protected static function isClosed(){
		if(parent::getCloseInfo('paid','all')){
			return false;
		}
		return true;
	}
	//处理接收到的金额 传drawcash_id为通过审核
	public function receiveFlowWallet(){
		$params = I('post.');
		//判断参数是否正确
		if(parent::checkParams($params,array(
			'money', 
			"bankId", 
		))){
			//$ids = array(2,873,1,7890);  || in_array($bid,$ids)
			if(self::isClosed()){
				$bid = $this->mid;
				$row = fRec("business","id=" . $bid,"name,platform_id");
				$pid = $row['platform_id'];
				if($bid){
					$storageType = "trade";
					//如果账号提现状态正常
					if(self::getDrawcashStatus($bid)){
						$poundage = 2;
						//提现金额
						$money = parent::subDecimals($params['money']);
						//扣除手续费
						$arrivemoney = parent::subDecimals($money - $poundage);
						//获取商户银行卡信息
						$bankinfo = self::getBankInfo($bid,$params['bankId']);
						//获取商户微信信息
						//$ordernum = "YDSMsDAOb" . time() . rand(1,100);
						//先判断是否有这么多钱提出
						//$SecStat = A("SecStatistics");
						//$ketixian = $SecStat->balanceWithdrawn($bid,$params['type']); //parent::getCanBeWithdrawalAmount($bid);
						$ye = self::busBalance($bid,$storageType,$pid);
						if($money > $ye){
							//file_put_contents("money.txt","BEGIN: money:".$money." ketixian: ".$ketixian." END ",FILE_APPEND);
							$status = array(
								'status' => 0, 
								'msg' => '可提现金额不足'
							);
						}else{
							//然后判断是否达到系统设置的提现金额
							if($money >= 50000){
								//需要审核
								$status = array(
									'status' => 0, 
									'msg' => '提现到银行卡单笔限额5万元!'
								);
							}else{
								$ordernum = date("YmdHis") . rand(10000,99999);
								//不需要审核 确认发送
								//更改已提现总额
								$walletData = array(
									"bid" => $bid, 
									//类型   YE
									"type" => $storageType, 
									"money" => $money, 
									//变更余额  P 存储  T 提现
									"tranType" => 'T', 
									"suoyin" => $ordernum, 
									// 存储类型  trade 交易 bind 绑定 agent  代理商    withdraw 提现
									"storageType" => "withdraw", 
									"platform_id" => $pid
								);
								$obj = new \Common\Api\SecStatistics();
								$a = $obj->storage($walletData);
								if($a){
									//默认数据准备添加
									$data = array(
										"user_id" => $params['bankId'], 
										"bus_id" => $bid, 
										"drawcash_type" => $storageType, 
										"drawcash_ordernum" => $ordernum, 
										"drawcash_status" => 2,  //状态 1 未结算 待审核 2 已结算',
										"drawcash_money" => $arrivemoney, 
										"drawcash_time" => time()
									);
									aRec(T_DCWWW,$data);
									$Kernel = A("Kernel");
									$Kernel->addRecordsOrder($bid,$money,$ordernum,$params['bankId'],$poundage);
									//判断是否在提现时间内 如果不在 添加到审核列表
									$start = "01:29:59";
									$end = "01:30:00";
									// 								$drawTime = fRec("draw_set",array(
									// 									"key" => "automatic"
									// 								),"start,end");
									// 								if($drawTime){
									// 									$start = date("H:i:s",$drawTime['start']);
									// 									$end = date("H:i:s",$drawTime['end']);
									// 								}
									$now = date("H:i:s");
									//当前时间大于等于开始时间 小于等于结束时间 内 跑 代付
									if($now >= $start && $now <= $end){
										//file_put_contents("./sdddddaaaerrtaetata" ,8);
										$res = $Kernel->easyPaid($bid,$arrivemoney,$ordernum,$bankinfo);
										if($res['status'] == 1 || $res['status'] == 2){
											adLog($bid,'订单号:' . $ordernum . '提现成功,等待系统打款到银行卡,提现金额:[' . $money . '元]',true);
											//app端接收的数据
											//$json = '{"money":"' . $money . '","msg":"提现成功,等待系统打款到银行卡","so":"' . $ordernum . '","atime":"' . dateFormat($data['drawcash_time'],4) . '","ctime":""}';
											//parent::JpushSend("您有一笔新的交易",$bid,"drawcash",$json);
											//添加消息
											//parent::addMsg($bid,"提现进度更新提醒","您有一笔提现到银行账号(" . $bankinfo['name'] . "尾号:" . $bankinfo['wh'] . ")" . $money . "元的交易已经到账",1,1,$ordernum);
											//提现红包
											//parent::addDrawcashRedPacket($bid,$ordernum,$money);
										}else{
											//代付没跑成功 加入审核列表
											adLog($bid,'订单号:' . $ordernum . '提现失败,原因：' . $res['msg'] . ',已加入审核列表,提现金额:[' . $money . '元]',false);
											$Kernel->changeOrderStatus($bid,$ordernum,1);
											self::updateWithdraw(array(
												"bid" => $bid, 
												"payType" => $storageType, 
												"money" => $arrivemoney, 
												"bankId" => $params['bankId'], 
												"ordernum" => $ordernum, 
												"remark" => empty($res['msg']) ? "提现失败" : $res['msg'], 
												"platform_id" => $pid
											));
											// 											$this->checkAudit($arrivemoney);
										}
									}else{
										//file_put_contents("./sdddddaaaerrtaetata" ,7);
										//不在提现时间提现 加入审核列表
										adLog($bid,'订单号:' . $ordernum . '在非提现时间内提现,已加入审核列表,提现金额:[' . $money . '元]',false);
										$Kernel->changeOrderStatus($bid,$ordernum,1);
										$uwd = self::updateWithdraw(array(
											"bid" => $bid, 
											"payType" => $storageType, 
											"money" => $arrivemoney, 
											"bankId" => $params['bankId'], 
											"ordernum" => $ordernum, 
											"remark" => empty($res['msg']) ? "非提现时间内提现" : $res['msg'], 
											"platform_id" => $pid
										));
										drawcashRemind($arrivemoney);
										// 										$this->checkAudit($arrivemoney);
// 										if($uwd && $pid == 3){
// 											$wmid = M("wd_manage")->where(array("bid"=>$bid,"ordernum"=>$ordernum))->getField("id");
// 											if($wmid){
// 												curlRequest(BASEURL."/index.php/Admin/AutoDrawcash/DoReview/ids/".$wmid.",".$bid);
// 											}
// 										}
									}
									$status = array(
										'status' => 1, 
										'msg' => '提现成功', 
									);
								}else{
									//余额扣除失败
									$status = array(
										"status" => 0, 
										"msg" => "提现失败"
									);
								}
							}
						}
					}else{
						$status = array(
							'status' => 0, 
							'msg' => '您的账户提现已被冻结!'
						);
					}
				}else{
					$status = array(
						'status' => 0, 
						'msg' => '提现失败用户数据不存在!'
					);
				}
			}else{
				$status = array(
					'status' => 0, 
					'msg' => parent::getVal("paidCloseTip")
				);
			}
			echo json_encode($status);
		}else{
			return false;
		}
	}
	/**
	 * 商户余额
	 * @date: 2017年6月15日 下午3:26:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	private static function busBalance($bid, $payType, $pid){
		$money = 0;
		if(!empty($bid)){
			$getBusBalance = M(T_BIMP)->where("bus_id=" . $bid . " and payType='" . $payType . "'")->getField("total_amount");
			if($getBusBalance){
				$total = RSAcode($getBusBalance,"DE");
				switch($payType){
					case 'bind':
						if(parent::drawCaltStat("jjjhkg",$pid) == 'yj'){
							$money = self::CanWithdraw($bid,$payType,$total,$pid);
						}else{
							$money = $total;
						}
						break;
					case 'agent':
						if(parent::drawCaltStat("sjdlskg",$pid) == 'yj'){
							$money = self::CanWithdraw($bid,$payType,$total,$pid);
						}else{
							$money = $total;
						}
						break;
					case 'trade':
						if(parent::drawCaltStat("jykg",$pid) == 'yj'){
							$money = self::CanWithdraw($bid,$payType,$total,$pid);
						}else{
							$money = $total;
						}
						break;
				}
				//解码
			}
		}
		return $money;
	}
	/**
	 * D+30提现金额 统计
	 * @param  $bid 商户ID
	 * @param $type 返现类型
	 * @param $total 总余额
	 */
	protected static function CanWithdraw($bid, $type, $total, $pid){
		if(empty($bid)){
			return false;
		}else{
			//验证提现时间  yj 月结 mj当日结
			$checkDrawTime = parent::checkTimeToOffer($type,$pid);
			if($checkDrawTime['status'] == 1){
				//获取当月第一天
				$date = date("Y-m-01");
				//当月余额
				$monthBalance = self::StatisticsCannotPresentedBalance($date,$bid,$type);
				//减去当月返现余额
				$dateDay = date("d");
				$total -= $monthBalance;
				if(intval($dateDay) >= $checkDrawTime['drawTime']){
					//可以提现上月的 不减去上月
					$total -= 0;
				}else{
					//获取上月第一天
					$previousMonth = date("Y-m-01",strtotime("-1 month"));
					//获取上月最后一天
					$endDate = date('Y-m-d',strtotime("$previousMonth +1 month -1 day"));
					//上月余额
					$lastMonthBalance = self::getLastMonthBalane($previousMonth,$endDate,$bid,$type);
					//减去上月
					$total -= $lastMonthBalance;
				}
			}
			return parent::subDecimals($total);
		}
	}
	/**
	 * 获取上月返现余额
	 * @date: 2017年6月30日 上午10:51:18
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: 
	 * @return:
	 */
	protected static function getLastMonthBalane($startDate, $endDate, $bid, $type){
		if(empty($startDate) || empty($endDate) || empty($bid) || empty($type)){
			return false;
		}else{
			$endDate = date("Y-m-d",strtotime("$endDate +1 day"));
			$sql = "select sum(changeAmount) gtsum  from " . PREFIX . T_CAPC . " where bid = " . $bid . " and storageType = '" . $type . "' and ( FROM_UNIXTIME(createTime ,'%Y-%m-%d') >= '" . $startDate . "' and FROM_UNIXTIME(createTime ,'%Y-%m-%d') < '" . $endDate . "' ) and `status` = 'Y' and changeType = 'P'";
			$sum = 0;
			$resArray = M()->query($sql);
			foreach($resArray as $v){
				$sum = $v['gtsum'] ? $v['gtsum'] : 0;
			}
			return $sum;
		}
	}
	/**
	 * 统计当月  余额
	 * @param  $date 日期
	 * @param  $bid  商户  ID
	 * @param  $type 支付类型
	 */
	protected static function StatisticsCannotPresentedBalance($date, $bid, $type){
		if(empty($date) || empty($bid) || empty($type)){
			return false;
		}else{
			$sql = "select sum(changeAmount) gtsum  from " . PREFIX . T_CAPC . " where bid = " . $bid . " and storageType = '" . $type . "' and  FROM_UNIXTIME(createTime ,'%Y-%m-%d') >=  '" . $date . "' and `status` = 'Y' and changeType = 'P'";
			$sum = 0;
			$resArray = M()->query($sql);
			foreach($resArray as $v){
				$sum = $v['gtsum'] ? $v['gtsum'] : 0;
			}
			return $sum;
		}
	}
	/**
	 * 更新提现管理
	 * @date: 2017年3月9日 上午10:55:55
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	private static function updateWithdraw($params){
		//file_put_contents("./sdddddaasdfdasgfyfgaer",var_export($params,true));
		if(parent::checkParams($params,array(
			"bid", 
			"payType", 
			"money", 
			"bankId", 
			"ordernum", 
			"remark", 
			"platform_id"
		))){
			$data = array(
				"bid" => $params['bid'], 
				"payType" => $params['payType'], 
				"money" => $params['money'], 
				"bankId" => $params['bankId'], 
				"ordernum" => $params['ordernum'], 
				"status" => 1, 
				"reviewStatus" => 2, 
				"createTime" => time(), 
				"remark" => $params['remark'], 
				"platform_id" => $params['platform_id']
			);
			//file_put_contents("./sdddddaaadssssssaaaaer",var_export($data,true));
			if(!aRec("wd_manage",$data)){
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
	//获取银行卡信息
	private static function getBankInfo($bid, $id){
		$info = fRec(T_BNK,'id=' . $id . '||bid=' . $bid . '||status=1','id,card_number,name,bank_name');
		$row = fRec(T_BUS,"id=" . $bid,"idCard,realName");
		if($info){
			$bank = @explode("-",$info['bank_name']);
			return [
				'id' => $info['id'], 
				'name' => $row['realName'], 
				'idCard' => $row['idCard'], 
				'bankName' => $bank[0], 
				'bankCardNum' => $info['card_number'], 
				'wh' => substr($info['card_number'],strlen($info['card_number']) - 3)
			];
		}
		return false;
	}
}
?>
