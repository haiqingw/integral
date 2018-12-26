<?php

/**
 * +-------------------------------------------
 * | Description: 商户管理 
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年3月28日 下午2:50:48
 * +-----------------------------------------------------
 * | Filename: BusinessController.class.php
 * +-----------------------------------------------------
 */
namespace App\Controller;

use Think\Controller;

define("DEFAULT_DRAW_METHOD", "秒结");
define("DEFAULT_DRAW_TYPE", 2);
define("PLAT_BLC", "plat_balance");
class BusinessController extends BaseController
{
	/**
	 * 商户总余额
	 *
	 * @param [type] $bid
	 * @return void
	 */
	public function get_buse_balance_list($plat, $bid)
	{
		$data = array();
		if (!empty($bid) && !empty($plat)) {
			$cashlist = M(T_BCMCLASS)->where(array("plat" => $plat, "status" => 1))->select();
			if ($cashlist) {
				$i = 0;
				do {
					$data[$i]['classname'] = $cashlist[$i]['classname'];
					$balance = parent::getBusinessBalance($bid, $cashlist[$i]['englishname']);
					$data[$i]['money'] = $balance;
					$i++;
				} while ($i < count($cashlist));
			}
		}
		return $data;
	}
	/**
	 * 商户总余额
	 *
	 * @param [type] $bid
	 * @return void
	 */
	public function get_buse_total_balance($bid)
	{
		$balance = 0;
		if (!empty($bid)) {
			$bal = M(T_IMPOR)->field("total_amount")->where(array("bus_id" => $bid))->select();
			if ($bal) {
				$i = 0;
				do {
					$balance += RSAcode($bal[$i]['total_amount'], "DE");
					$i++;
				} while ($i < count($bal));
			}
		}
		return $balance;
	}
	/**
	 * 平台余额更新
	 *
	 * @return void
	 */
	public function updatePlatBalance($plat, $money)
	{
		if (!empty($plat) || !empty($money)) {
			if (cRec(PLAT_BLC, array("plat" => $plat))) {
				bcscale(2);
				$balance = self::balances($plat);
				$balance = bcadd($money, $balance);
				$data = array(
					"modify_time" => time(),
					"total_amount" => RSAcode($balance, "EN")
				);
				uRec(PLAT_BLC, $data, array("plat" => $plat));
			} else {
				$data = array(
					"plat" => $plat,
					"update_date" => time(),
					"status" => 1,
					"total_amount" => RSAcode($money, "EN")
				);
				aRec(PLAT_BLC, $data);
			}
		}
	}
	/**
	 * 平台余额
	 *
	 * @param [type] $plat
	 * @return void
	 */
	protected static function balances($plat)
	{
		$balance = 0;
		if (!empty($plat)) {
			$bal = M(PLAT_BLC)->where(array("plat" => $plat))->getField("total_amount");
			if ($bal) {
				$balance = RSAcode($bal, "DE");
			}
		}
		return $balance;
	}
	/**
	 * 商户可提现余额显示
	 *
	 * @param [type] $bid
	 * @param [type] $plat
	 * @return void
	 */
	public function displayBalance($bid, $plat)
	{
		$data = array();
		if (empty($bid) || empty($plat)) {
			$data = array("ktx" => '0.00', "money" => '0.00', "method" => "秒结");
		} else {
			$level = $this->level($bid);
			if ($level) {
				$can_carry = '0.00';
				$draw = DEFAULT_DRAW_METHOD;
				$draw_method = self::set_method($plat, $level);
				if ($draw_method) {
					$draw = $draw_method['settm'];
				}
				$level = M(T_BUS)->where(array("id" => $bid))->getField("level");
				if (cRec(T_ICOMEDPM, array("pinyin" => $level, "plat" => $plat))) {
					$mids = M(T_ICOMEDPM)->where(array("pinyin" => $level, "plat" => $plat))->getField("cashID");
					$cashlist = M(T_BCMCLASS)->where(array("id" => array("in", $mids), "status" => 1))->select();
					$data = array();
					$i = 0;
					do {
						$balance = parent::getBusinessBalance($bid, $cashlist[$i]['englishname']);
						$data[$i]['cashname'] = $cashlist[$i]['classname'];
						$data[$i]['balance'] = $balance;
						$data[$i]['cashType'] = $cashlist[$i]['englishname'];
						$data[$i]['ktx'] = $this->canWithdraw($bid, $plat, $balance, $draw_method['setType'], $cashlist[$i]['englishname']);
						$data[$i]['methed'] = $draw;
						$i++;
					} while ($i < count($cashlist));
				}
			}
		}
		return $data;
	}
	/**
	 * 获取商户等级
	 *
	 * @param [type] $bid
	 * @return void
	 */
	public function level($bid)
	{
		return parent::getValue(T_BUS, "id", $bid, "level");
	}
	/**
	 * 提现金额 统计
	 * @param  $bid 商户ID
	 * @param $type 返现类型
	 * @param $total 总余额
	 */
	public function canWithdraw($bid, $plat, $total, $draw_method, $cashType)
	{
		if (empty($bid)) {
			return false;
		} else {
			//验证提现时间  yj 月结 mj当日结
			if ($draw_method == 1) {
				//获取当月第一天
				$date = date("Y-m-01");
				//当月余额
				$monthBalance = self::statistics_cannot_presente_balance($date, $bid, $cashType);
				//减去当月返现余额
				$dateDay = date("d");
				$total -= $monthBalance;
				if (intval($dateDay) >= 2) {
					//可以提现上月的 不减去上月
					$total -= 0;
				} else {
					//获取上月第一天
					$previousMonth = date("Y-m-01", strtotime("-1 month"));
					//获取上月最后一天
					$endDate = date('Y-m-d', strtotime("$previousMonth +3 month -1 day"));
					//上月余额
					$lastMonthBalance = self::getLastMonthBalane($previousMonth, $endDate, $bid, $cashType);
					//减去上月
					$total -= $lastMonthBalance;
				}
				if ($total < 0) {
					$total = '0.00';
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
	protected static function getLastMonthBalane($startDate, $endDate, $bid, $cashType)
	{
		if (empty($startDate) || empty($endDate) || empty($bid)) {
			return false;
		} else {
			// $type = self::formatType($type, 2);
			$endDate = date("Y-m-d", strtotime("$endDate +1 day"));
			$sql = "select sum(changeAmount) gtsum  from " . PREFIX . T_CAPC . " where storageType = '" . $cashType . "' and bid = " . $bid . "  and ( FROM_UNIXTIME(createTime ,'%Y-%m-%d') >= '" . $startDate . "' and FROM_UNIXTIME(createTime ,'%Y-%m-%d') < '" . $endDate . "' )";
			$sum = 0;
			$resArray = M()->query($sql);
			foreach ($resArray as $v) {
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
	protected static function statistics_cannot_presente_balance($date, $bid, $cashType)
	{
		if (empty($date) || empty($bid)) {
			return false;
		} else {
			$sql = "select sum(changeAmount) gtsum  from " . PREFIX . T_CAPC . " where storageType = '" . $cashType . "' and  bid = " . $bid . " and  FROM_UNIXTIME(createTime ,'%Y-%m-%d') >=  '" . $date . "'";
			$sum = 0;
			$resArray = M()->query($sql);
			foreach ($resArray as $v) {
				$sum = $v['gtsum'] ? $v['gtsum'] : 0;
			}
			return $sum;
		}
	}
	/**
	 * 获取结算方式
	 * @date: 2017年12月20日 下午5:35:11
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function set_method($plat, $level)
	{
		if (empty($plat) || empty($level)) {
			return false;
		} else {
			$field = array(
				"case setMethod when 1 then '月结' when 2 then '秒结'  end settm",
				"setMethod setType"
			);
			$info = M(T_DRAW_SET)->field($field)->where(array("plat" => $plat, "userLevel" => $level))->find();
			$data = array();
			if ($info) {
				return $info;
			}
		}
	}
}