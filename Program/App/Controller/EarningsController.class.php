<?php
namespace App\Controller;

use Think\Controller;

class EarningsController extends BaseController
{
	/**
	 * 省钱
	 *
	 * @param [type] $params
	 * @return void
	 * {requestType:earnings,requestKeywords:savemoney, platformID:’3’,userID:’x’,userPhone:’x’}
	 */
	public function savemoney($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array("phone" => $phone, "plat" => $pid));
			$res = M(T_CABL)->field("ifnull(TRUNCATE(sum(cashMoney),2),'0.00') sum")->where(array("outputAN" => $bid, "receiveAN" => $bid))->select();
			$sum = '0.00';
			if ($res) {
				$sum = $res[0]['sum'];
			}
			$ret = array("responseStatus" => 1, "sum" => $sum);
		}
		return $ret;
	}
	/**
	 * 月交易对比统计
	 *
	 * @return void
	 */
	//{requestType: Earnings,requestKeywords:resultsperson, platformID:’3’,userID:’x’,userPhone:’x’,proid:x}
	public function tradestat($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$years = date('Y');
			$startMonth = date("Y-01");
			$endMonth = date("Y-m");
			if ($params) {
				if ($params['keywords']) {
					$years = $params['keywords'];
					if (date('Y') != $years) {
						$startMonth = $years . '-01';
						$endMonth = $years . '-12';
					}

				}
			}
			$suffix = $this->get_rul($params['proid']);
			if ($suffix) {
				$num = $this->time_difference($startMonth, $endMonth);
				$table = "posdata_" . $suffix;
				$data = array();
				$i = 0;
				while ($i <= $num) {
					$date = date("Y-m", strtotime("+" . $i . " month", strtotime($startMonth)));
					$where = array(
						"tradeTime" =>
							array(
							array("egt", strtotime($date)), array("lt", strtotime("$date +1 month"))
						),
						"processStatus" => 2
					);
					$result = M($table)->field("ifnull(TRUNCATE(sum(tradeAmt),2),'0.00') sum,ifnull(COUNT(*),0) con")->where($where)->select();
					$j = 0;
					do {
						$data[$i]['con'] = $result[$j]['con'];
						$data[$i]['sum'] = $result[$j]['sum'];
						$data[$i]['dates'] = $date;
						$j++;
					} while ($j < count($result));
					$i++;
				}
				$ret = array("responseStatus" => 1, "data" => $data);
			} else {
				$ret = array("responseStatus" => 300);
			}
		}
		return $ret;
	}
	/**
	 * 时间差
	 *
	 * @param [开始] $startMonth
	 * @param [结束] $endMonth
	 * @return void
	 */
	public function time_difference($startMonth, $endMonth)
	{
		$num = 0;
		if (!empty($startMonth) && !empty($endMonth)) {
			$tags = "-";
			$start = explode($tags, $startMonth);
			$end = explode($tags, $endMonth);
			$num = abs($start[0] - $end[0]) * 12 + abs($start[1] - $end[1]);
		}
		return $num;
	}
	/**
	 * Undocumented function
	 *
	 * @param [type] $params
	 * @return void
	 */
	// {requestType: Earnings,requestKeywords:resultsperson, platformID:’3’,userID:’x’,userPhone:’x’,types:x (个人 perl ,当日 part),level:x商户等级}
	public function partner($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$where = array();
			$date = date("Y-10");
			$endDate = date("Y-m", strtotime("$date +1 month"));
			$where = array(
				"status" => 1,
				"regisTime" => array(
					array("egt", strtotime($date)), array("lt", strtotime($endDate))
				)
			);
			switch ($params['types']) {
				case perl:
					$where['parent'] = $bid;
					$query = cRec(T_BUS, $where);
					echo M()->_sql();
					break;
				case part:
					break;
			}
			$ret = array("responseStatus" => 1, "data" => $query);
		}
		dump($ret);
	}

	/**
	 * 绑机器列表
	 *
	 * @return void
	 *  {requestType: earnings,requestKeywords:actlist, platformID:’3’,userID:’x’,userPhone:’x’,page:x,keywords:x (选填),isActive :x(选填 no 未激活 yes 已激活 )}
	 */
	public function actlist($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$where = "";
			if ($params['isActive']) {
				$where .= " and  isActive = " . $params['isActive'];
			}
			if ($params['keywords']) {
				$ids = self::get_bus_ids($params['keywords'], $pid);
				$where .= " and  bid in (" . $ids . ")";
			}
			$page = $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$offset = ($page - 1) * $limit;
			$sql = "select  product,busname,phone,terminal,isAct,isActive,updateTime from (SELECT updateTime,(select commodityName from " . PREFIX . T_COMMODITY . " where id = proid) product,bid,(SELECT `busname` FROM p_user WHERE id = bid ) busname,(SELECT phone FROM p_user WHERE id = bid ) phone,terminal,case isActive when 1 then '未激活' when 2 then '已激活' end isAct ,isActive ,(select parent from p_user where id = bid) pid FROM p_terminal_manage where plat = {$pid}  {$where} ) a where pid = {$bid} limit {$offset},{$limit}";
			$query = M()->query($sql);
			if ($query) {
				$ret = array("responseStatus" => 1, "data" => $query);
			} else {
				$ret['responseStatus'] = 300;
			}
		}
		return $ret;
	}
	/**
	 * 商户个人业绩
	 *
	 * @return void
	 * {requestType: Earnings,requestKeywords:resultsperson, platformID:’3’,userID:’x’,userPhone:’x’,types:x (昨日 OnDay ,当日 InDay),level:x商户等级,checkType:x(必传 [直营] directly [拓展]  expand)}
	 */
	public function resultsperson($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$level = $params['level'];
			switch ($params['types']) {
				case OnDay:
					$startDate = date("Y-m-d");
					$endDate = date("Y-m-d", strtotime("{$startDate} -1 day"));
					break;
				case InDay:
					$startDate = date("Y-m");
					$endDate = date("Y-m", strtotime("{$startDate} +1 month"));
					break;
			}
			//激活数
			$actCon = $this->get_active_con($startDate, $endDate, $pid, $bid, $level, $params['checkType']);
			//邀请数
			$inviCon = $this->get_invite_con($startDate, $endDate, $pid, $bid, $params['checkType']);
			//下属交易金额
			$tradeMoney = $this->get_trade_total($startDate, $endDate, $pid, $bid, $level, $params['checkType']);
			$ret = array("responseStatus" => 1, "inviCon" => $inviCon, "actCon" => $actCon, "tradeMoney" => $tradeMoney);
		}
		// dump($ret);
		return $ret;
	}
	/**
	 * 交易总额
	 *
	 * @param [开始时间(string)] $startDate
	 * @param [结束时间(string)] $endDate
	 * @param [平台ID(int)] $pid
	 * @param [商户ID(int)] $bid
	 * @return void
	 */
	public function get_trade_total($startDate, $endDate, $pid, $bid, $level, $checkType)
	{
		$sum = "0.00";
		if (!empty($startDate) && !empty($endDate) && !empty($pid) && !empty($bid)) {
			$terminal = self::getTerminals($bid, $pid, $level, $checkType);
			if ($terminal) {
				$i = 0;
				while ($i < count($terminal)) {
					$where = array(
						"terminalNo" => $terminal[$i]['terminal'],
						"processStatus" => 2,
						"tradeTime" => array(
							array("egt", strtotime($startDate)), array("lt", strtotime($endDate))
						)
					);
					$trade = M("posdata_" . $terminal[$i]['ruleList'])->field("ifnull(sum(tradeAmt),'0.00') sum ,ifnull(count(*),0) con")->where($where)->group("terminalNo")->select();
					// echo M()->_sql() . ";<br />";
					if ($trade) {
						$sum += $trade[0]['sum'];
					}
					$i++;
				}
			}
		}
		return $sum;
	}
	/**
	 * 获取下级商户终端号
	 *
	 * @param [type] $parentID
	 * @param [type] $plat
	 * @param [type] $proid
	 * @return void 
	 * @todo proid 产品ID
	 */
	protected static function getTerminals($parentID, $plat, $level, $sqlTypes, $startDate = '', $endDate = '')
	{
		if (!empty($parentID) && !empty($plat) && !empty($level)) {
			$where = "";
			if (!empty($startDate) || !empty($endDate)) {
				$where = " and (updateTime >= '{$startDate}' and updateTime < '{$endDate}')";
			}
			switch ($sqlTypes) {
				case directly:
				 	#直营团队
					$sql = "select * from (SELECT proid,bid,terminal,(select parent from p_user where id = bid ) pid FROM p_terminal_manage where plat = {$plat}  and isActive = 2 {$where} ) a where pid = {$parentID} or bid = {$parentID};";
					break;
				case expand:
					#拓展团队	
					$sql = "select * from (SELECT proid,bid,terminal,(select getAgentVipID(bid,'{$level}')) pid FROM p_terminal_manage where plat = {$plat}  and isActive = 2  " . $where . ") a where pid = {$parentID}";
					break;
			}
			$query = M()->query($sql);
			if ($query) {
				$data = array();
				$i = 0;
				do {
					$data[$i]['ruleList'] = self::get_rul($query[$i]['proid']);
					$data[$i]['terminal'] = $query[$i]['terminal'];
					$i++;
				} while ($i < count($query));
			}
			return $data;
		}
		return false;
	}
	/**
	 * 获取数据表后缀
	 *
	 * @param [type] $proid
	 * @return void
	 */
	protected static function get_rul($proid)
	{
		if (!empty($proid)) {
			$res = M(T_COMMODITY)->field("(select  SUBSTRING_INDEX(ruleList,'/', -1) from " . PREFIX . T_COMM_CATE . " where id = category_id) ruleList")->where(array("id" => $proid))->find();
			if ($res) {
				return $res['ruleList'];
			}
		}
		return false;
	}
	//
	/**
	 * 邀请数
	 *
	 * @param [开始时间(string)] $startDate
	 * @param [结束时间(string)] $endDate
	 * @param [平台ID(int)] $pid
	 * @param [商户ID(int)] $bid
	 * @return void
	 */
	public function get_invite_con($startDate, $endDate, $pid, $bid, $checkType)
	{
		$con = 0;
		if (!empty($startDate) && !empty($endDate) && !empty($pid) && !empty($bid)) {
			$where = "";
			switch ($checkType) {
				case directly:
					 #直营团队
					$sql = "(select parent from p_user ub where ub.id = ua.id ) pid";
					break;
				case expand:
					#拓展团队	
					$sql = "(select getAgentVipID(id,'dls')) pid";
					break;
			}
			$where = " and (regisTime >= " . strtotime($startDate) . " and  regisTime < " . strtotime($endDate) . " ) ";
			$sql = "select count(*) con from (select {$sql} from p_user ua where plat = {$pid} {$where} ) a where pid =  {$bid}";
			$inviRet = M()->query($sql);
			if ($inviRet) {
				$con = $inviRet[0]['con'];
			}
		}
		return $con;
	}
	/**
	 * 下属激活数
	 *
	 * @param [开始时间(string)] $startDate
	 * @param [结束时间(string)] $endDate
	 * @param [平台ID(int)] $pid
	 * @param [商户ID(int)] $bid
	 * @return void
	 * @todo 产品ID筛选 proid = 2
	 * select con from (select * from (select count(*) con,pid from (SELECT *,(select getAgentVipID(bid,'dls')) pid FROM p_terminal_manage where proid = 2 and (updateTime >= '2018-11-01' and updateTime < '2018-11-15') and  plat = 203 and isActive = 2) a where pid = 1  group by pid ) x ) over
	 */
	public function get_active_con($startDate, $endDate, $pid, $bid, $level, $checkType)
	{
		$con = 0;
		if (!empty($startDate) && !empty($endDate) && !empty($pid) && !empty($bid)) {
			$where = "";
			switch ($checkType) {
				case directly:
					 #直营团队
					$sql = "(select parent from p_user where id = bid)";
					$where .= " or bid = {$bid}";
					break;
				case expand:
					#拓展团队	
					$sql = "(select getAgentVipID(bid,'{$level}'))";
					break;
			}
			$sql = "select con from (select * from (select count(*) con,pid from (SELECT *,{$sql} pid FROM p_terminal_manage where  (updateTime >= '{$startDate}' and updateTime < '{$endDate}') and  plat = {$pid} and isActive = 2) a where pid = {$bid} {$where} ) x ) over";
			$actRet = M()->query($sql);
			if ($actRet) {
				$con = $actRet[0]['con'];
			}
		}
		return $con;
	}
	/**
	 * 个人排行
	 * {requestType: earnings,requestKeywords:personal, platformID:’3’,userID:’x’,userPhone:’x’}
	 * @param [type] $params
	 * @return void
	 */
	public function personal($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$sql = "select * from (select truncate(money,2) money,busName,phone,( @rowNo := @rowNo + 1 ) rank,receiveAN from (SELECT sum(cashMoney) money, (SELECT `busname` FROM p_user WHERE id = receiveAN ) busName,( SELECT `level` FROM p_user WHERE id = receiveAN ) `level`,(SELECT phone FROM p_user WHERE id = receiveAN ) phone,receiveAN FROM p_cash_back_log WHERE  plat = {$pid}  GROUP BY receiveAN ORDER BY money DESC) a, ( SELECT @rowNo := 0 x ) b ) x  where receiveAN  =  " . $bid;
			$res = M()->query($sql);
			if ($res) {
				$ret = array("responseStatus" => 1, "data" => $res);
			}
		}
		// dump($ret);
		return $ret;
	}
	/**
	 * 冠军榜
	 *
	 * @param [type] $params
	 * @return void
	 * {requestType: earnings,requestKeywords:championship, platformID:’3’,userID:’x’,userPhone:’x’,types: ( 必填全部传 all  当月 mons ) ,level:x}
	 */
	public function championship($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$level = $params['level'];
			if (cRec(T_ICOMEDPM, array("pinyin" => $level, "plat" => $pid))) {
				$data = array();
				$i = 0;
				$mids = M(T_ICOMEDPM)->where(array("pinyin" => $level, "plat" => $pid))->getField("cashID");
				$casharrs = M(T_BCMCLASS)->where(array("id" => array("in", $mids), "status" => 1))->select();
				$j = 0;
				while ($j < count($casharrs)) {
					$rank = $this->tjRanking($casharrs[$j]['plat'], $level, $casharrs[$j]['englishname'], $params['types']);
						// if ($rank) {
					$data[$j]['dataLists'] = $rank;
					$data[$j]['cashType'] = $casharrs[$j]['classname'];
						// }
					$j++;
				}
				$ret = array("responseStatus" => 1, "data" => $data);
			} else {
				$ret['responseStatus'] = 300;
			}
			// $arrs = M(T_BLMCS)->where(array("plat" => $pid, "status" => 1, "englishname" => $params['level']))->select();
			// if ($arrs) {
			// 	$data = array();
			// 	$i = 0;
			// 	while ($i < count($arrs)) {
			// 		$data[$i]['title'] = $arrs[$i]['classname'];
			// 		$casharrs = M(T_BCMCLASS)->where(array("plat" => $arrs[$i]['plat'], "status" => 1))->select();
			// 		$j = 0;
			// 		while ($j < count($casharrs)) {
			// 			$rank = $this->tjRanking($casharrs[$j]['plat'], $arrs[$i]['englishname'], $casharrs[$j]['englishname'], $params['types']);
			// 			// if ($rank) {
			// 			$data[$j]['dataLists'] = $rank;
			// 			$data[$j]['cashType'] = $casharrs[$j]['classname'];
			// 			// }
			// 			$j++;
			// 		}
			// 		$i++;
			// 	}
			// 	$ret = array("responseStatus" => 1, "data" => $data);
			// }
		} else {
			$ret['responseStatus'] = 102;
		}
		// dump($ret);
		return $ret;
	}
	/**
	 * 计算排行前三
	 *
	 * @return void
	 */
	public function tjRanking($pid, $level, $cashType, $types = 'mons')
	{
		if (!empty($pid) && !empty($level) && !empty($cashType)) {
			$where = "";
			if ($types == 'mons') {
				$date = date("Y-m");
				$where .= " and (cashTime  >= " . strtotime($date) . "  and  cashTime < " . strtotime("$date +1 month") . ") ";
			}
			$sql = "SELECT * FROM ( SELECT TRUNCATE (money, 2) money, busName, concat_ws( '****', substring(phone, 1, 3), substring(phone, 8, 4)) phone, ( @rowNo := @rowNo + 1 ) rank FROM ( select * from (SELECT sum(cashMoney) money, ( SELECT busname FROM p_user WHERE id = receiveAN ) busName, ( SELECT `phone` FROM p_user WHERE id = receiveAN ) phone, ( SELECT `level` FROM p_user WHERE id = receiveAN ) `level` FROM p_cash_back_log WHERE cashType = '" . $cashType . "' and  plat = " . $pid . " AND isAddWallet = 1 " . $where . " GROUP BY receiveAN  ) c where level = '" . $level . "' ORDER BY money DESC ) a, ( SELECT @rowNo := 0 x ) b ) xx where rank < 4";
			$list = M()->query($sql);
			if ($list) {
				return $list;
			}
		}
		return false;
	}
	/**
	 * {requestType: 'earnings',requestKeywords:'ranking',platformID:x,userID:x,userPhone:x,level:x 商户等级,cashType:x收益类型,proid:x商品id(选填 不传全部),types:x 选填 全部 传All  当月 不传,page:x,limit:,} 排行
	 *  {requestType: ’list’,requestKeywords:’montranking’, platformID:’3’,userID:’x’,userPhone:’x’,page:x,limit:x,types:x 选填 全部 传All  当月 不传}
	 * @param [type] $params
	 * @return void
	 */
	public function ranking($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$offset = ($page - 1) * $limit;
			$where = "";
			if (empty($params['types'])) {
				$date = date("Y-m");
				$where .= " and (cashTime  >= " . strtotime($date) . "  and  cashTime < " . strtotime("$date +1 month") . ") ";
			}
			if ($params['proid']) {
				$where .= " and proid = " . $params['proid'];
			}
				// $sql = "select * from (SELECT truncate(money,2) money,busName,concat_ws('****',substring(phone, 1, 3), substring(phone, 8, 4)) phone, ( @rowNo := @rowNo + 1 ) rank FROM (SELECT sum( cashMoney ) money,( SELECT busname FROM p_user WHERE id = receiveAN ) busName,( SELECT `phone` FROM p_user WHERE id = receiveAN ) phone FROM p_cash_back_log WHERE plat = " . $pid . " and isAddWallet = 1 " . $where . " GROUP BY receiveAN ORDER BY money DESC ) a, ( SELECT @rowNo := 0 x ) b) xx limit {$offset},{$limit}";
			$sql = "SELECT * FROM ( SELECT TRUNCATE (money, 2) money, busName, concat_ws( '****', substring(phone, 1, 3), substring(phone, 8, 4)) phone, ( @rowNo := @rowNo + 1 ) rank FROM ( select * from (SELECT sum(cashMoney) money, ( SELECT busname FROM p_user WHERE id = receiveAN ) busName, ( SELECT `phone` FROM p_user WHERE id = receiveAN ) phone, ( SELECT `level` FROM p_user WHERE id = receiveAN ) `level` FROM p_cash_back_log WHERE cashType = '" . $params['cashType'] . "' and  plat = " . $pid . " AND isAddWallet = 1 " . $where . " GROUP BY receiveAN  ) c where level = '" . $params['level'] . "' ORDER BY money DESC ) a, ( SELECT @rowNo := 0 x ) b ) xx limit {$offset},{$limit}";
			$list = M()->query($sql);
			if ($list) {
				$ret = array(
					"responseStatus" => 1,
					"data" => $list,
					"counts" => count($list)
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		// dump($ret);
		return $ret;
	}
	/**
	 * 我的收益
	 *
	 * @return void
	 * {requestType: 'earnings',requestKeywords:'myincome',platformID:x,userID:x,userPhone:x} //我的收益(首页)
	 */
	public function myincome($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$level = M(T_BUS)->where(array("id" => $bid))->getField("level");
			if (cRec(T_ICOMEDPM, array("pinyin" => $level, "plat" => $pid))) {
				$mids = M(T_ICOMEDPM)->where(array("pinyin" => $level, "plat" => $pid))->getField("cashID");
				$query = M(T_BCMCLASS)->field("classname,englishname")->where(array("id" => array("in", $mids)))->select();
				$i = 0;
				$data = array();
				while ($i < count($query)) {
					$data[$i]['name'] = $query[$i]['classname'];
					$data[$i]['money'] = $this->total_tj_income($bid, $query[$i]['englishname'], $pid);
					$i++;
				}
				$ret = array("responseStatus" => 1, "status" => 1, "data" => $data);
			} else {
				$ret = array("responseStatus" => 1, "status" => 0);
			}
		}
		return $ret;
	}
	/**
	 * 首页统计
	 *
	 * @param [type] $bid   商户ID
	 * @param [type] $class 返现类型
	 * @param [type] $plat  平台ID
	 * @return void
	 */
	public function total_tj_income($bid, $class, $plat = 0)
	{
		$sum = '1.00';
		if (!empty($bid) && !empty($class)) {
			$where = array(
				"bid" => $bid, "status" => 'Y',
				"storageType" => $class, "changeType" => 'Z'
			);
			if (!empty($plat))
				$where['plat'] = $plat;
			$result = M(T_CAPC)->field("ifnull(sum(changeAmount),'0.00') sum")->where($where)->select();
			if ($result) {
				$sum = parent::subDecimals($result[0]['sum'], 2);
			}
		}
		return $sum;
	}
}