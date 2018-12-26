<?php

/**
 * +-------------------------------------------
 * | Description: 服务费管理 
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年6月23日 下午2:02:38
 * +-----------------------------------------------------
 * | Filename: SystemRachargeController.class.php
 * +-----------------------------------------------------
 */
namespace App\Controller;

use Think\Controller;
use Boris\DumpInspector;

define("SER_REN_MAN", "service_renewal_manage");
define("SER_REC_TYPE", "service_recharge_type");
define("SER_FEE_SET", "service_fee_set");
define("SER_FEE_CYC_TEM", "cycle_templates");
class ServicefeeController extends BaseController
{

    /**
     * 支付完成页
     *
     * @param [array] $params
     * @return void
     *  {requestType: 'servicefee',requestKeywords:'complatetip', platformID:x,userID:x,userPhone:x,ordernum:x} 支付完成页
     */
    public function complatetip($params)
    {
        $pid = $params['platformID'];
        $phone = parent::decode($params['userPhone']);
        $ordernum = $params['ordernum'];
        $ret = array("responseStatus" => 102);
        if (parent::check($phone, $params['userID'], $pid)) {
            $bid = parent::busID(array("phone" => $phone, "plat" => $pid));
            $info = M(SER_REN_MAN)->where(array("ordernum" => $ordernum, "bid" => $bid))->find();
            if ($info) {
                $data = array();
                $data['subDays'] = parent::get_days_name($info['rerType'], $info['payPeriod']);
                $data['expDate'] = date("Y-m-d", $info['endDate']);
                $data['rMoney'] = $info['payMoney'];
                $ret = array(
                    "responseStatus" => 1,
                    "data" => $data
                );
            } else {
                $ret = array("responseStatus" => 300);
            }
        }
        return $ret;
    }
    /**
     * 订单支付回调修改订单状态
     * @date: 2018年4月13日 下午3:22:28
     * @author: HaiQing.Wu <398994668@qq.com>d
     */
    public function servicePay($params)
    {
        //  $params = array(
        //     "appid" => $params['appid'],
        //     "attach" => $params['attach'],
        //     "transaction_id" => $params['transaction_id'],
        //     "out_trade_no" => $params['out_trade_no'],
        //     "bank_type" => $params['bank_type'],
        //     "fee_type" => $params['fee_type'],
        //     "is_subscribe" => $params['is_subscribe'],
        //     "mch_id" => $params['mch_id'],
        //     "nonce_str" => $params['nonce_str'],
        //     "openid" => $params['openid'],
        //     "result_code" => $params['result_code'],
        //     "return_code" => $params['return_code'],
        //     "sign" => $params['sign'],
        //     "time_end" => $params['time_end'],
        //     "total_fee" => $params['total_fee'],
        //     "trade_type" => $params['trade_type']
        // );
        $paramsKey = array_keys($params);
        $colums = self::get_table_cols('p_wxpaycord');
        if ($colums) {
            $data = array();
            foreach ($colums as $key => $val) {
                if (in_array($val['name'], $paramsKey)) {
                    $data[$val['name']] = $params[$val['name']];
                }
            }
            $where = array(
                "ordernum" => $data['out_trade_no'],
                "bid" => $data['attach']
            );
            if (cRec(SER_REN_MAN, $where)) {
                $plat = M(T_BUS)->where(array("id" => $data['attach']))->getField("plat");
                $last_re_id = $this->get_last_recharge_overdue_id($plat, $data['attach'], 2);
                if ($last_re_id > 0) {
                    uRec(SER_REN_MAN, array("status" => 2), array("id" => $last_re_id));
                }
                $update_isPay = array("isPay" => 2);
                uRec(SER_REN_MAN, $update_isPay, $where);
            }
            //支付记录
            $this->WxPayrecord($data);
        }
    }
    private static function get_table_cols($table)
    {
        if (!empty($table)) {
            $sql = "select COLUMN_NAME name from information_schema.COLUMNS where table_name = '" . $table . "' and  table_schema = '" . DB_TTP_DB_NAME . "'";
            $query = M()->query($sql);
            if ($query) {
                return $query;
            }
        }
        return false;
    }
    /**
     * 微信支付记录
     * @date: 2018年4月17日 下午3:10:34
     * @author: HaiQing.Wu <398994668@qq.com>
     */
    protected function WxPayrecord($data)
    {
        aRec("wxpaycord", $data);
    }
    /**
     * 平台充值类型  服务费过期查看
     *
     * @param [array] $params
     * @return void
     *  {requestType: 'servicefee',requestKeywords:'checkplatfee', platformID:x} 公司充值类型  服务费是否过期查看
     */
    public function checkplatfee($params)
    {
        $pid = $params['platformID'];
        $where = array("plat" => $pid, "isPay" => 2, "status" => 1, "bid" => 0);
        $ret = array("responseStatus" => 604);
        if (cRec(SER_REN_MAN, $where)) {
			//获取上次充值ID
            $sql = "select max(id) id from " . PREFIX . SER_REN_MAN . " where plat=" . $pid . " and  status = 1 and isPay = 2 and bid = 0";
            $query = M()->query($sql);
            if ($query) {
                $where = array(
                    "id" => $query[0]['id'],
                    "plat" => $pid,
                );
                $ser_fee_info = M(SER_REN_MAN)->where($where)->find();
                if ($ser_fee_info) {
						//查看是否过期
                    if ($ser_fee_info['status'] == 2) {
                        $ret = array("responseStatus" => 603);
                    } else {
                        if ($ser_fee_info['endDate'] < time()) {
                            $ret = array("responseStatus" => 603);
                        } else {
                            $ret = array("responseStatus" => 1);
                        }
                    }
                } else {
                    $ret = array("responseStatus" => 604);
                }
            } else {
                $ret = array("responseStatus" => 604);
            }
        }
        return $ret;
    }
    /**
     * 服务费续费列表
     *
     * @param [type] $params
     * @return 
     * {requestType: 'servicefee',requestKeywords:reclist, platformID:x,userID:’x’,userPhone:’x’,page:X，limit：X} 服务费续费列表
     * ["responseStatus"] => int(1)
     * ["data"] => array(2) {
     * [0] => array(12) {
     *      ["id"] => string(2) "56"                    表ID
     *      ["isPay"] => string(1) "1"                  支付状态 1 未支付 2 已支付
     *      ["status"] => string(1) "1"                 使用状态 1 正常 2 已过期
     *      ["payPeriod"] => string(4) "1天"            购买周期
     *      ["numDays"] => string(1) "1"                这次购买天数
     *      ["lastRemain"] => string(1) "1"             上期购买剩余天数
     *      ["totalDays"] => string(1) "2"              这次购买总天数   numDays + lastRemain
     *      ["rerType"] => string(1) "2"                充值类型  1 公司类型 2 业务员充值
     *      ["rechageMoney"] => string(4) "1.00"        服务费充值金额
     *      ["chargeDate"] => string(10) "2018.08.01"   充值时间
     *      ["expDate"] => string(10) "2018.08.03"      到期时间
     *      ["payStatus"] => string(9) "未付款"          
     *      ["expStatus"] => string(6) "正常"
     *  }
     */
    public function reclist($params)
    {
        $pid = $params['platformID'];
        $phone = parent::decode($params['userPhone']);
        $ret = array("responseStatus" => 102);
        if (parent::check($phone, $params['userID'], $pid)) {
            $bid = parent::busID(array("phone" => $phone, "plat" => $pid));
            $page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
            $limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
            $field = array(
                "id", "isPay", "status", "numDays",
                "payPeriod", "lastRemain", "totalDays", "rerType",
                "ifnull(TRUNCATE(payMoney,2),'0.00') rechageMoney ",
                "from_unixtime(startDate,'%Y.%m.%d') chargeDate",
                "from_unixtime(endDate,'%Y.%m.%d') expDate",
                "case isPay when 1 then '" . NOT_PAY . "' when 2 then '" . HAVE_PAY . "' end payStatus",
                "case status when 1 then '" . NORMAL_S . "' when 2 then '" . EXPIRED_S . "' end expStatus",
            );
            $offset = ($page - 1) * $limit;
            $where = array(
                "bid" => $bid, "plat" => $pid,
                "rerType" => 2, "isPay" => 2
            );
            $arrays = M(SER_REN_MAN)->field($field)->where($where)->limit($offset, $limit)->order("id DESC")->select();
            if ($arrays) {
                for ($i = 0; $i < count($arrays); $i++) {
                    if ($arrays[$i]['rerType'] == 1) {
                        $arrays[$i]['payPeriod'] = parent::get_days_name($arrays[$i]['rerType'], $arrays[$i]['payPeriod']);
                    }
                    if ($arrays[$i]['rerType'] == 2) {
                        $arrasy[$i]['payPeriod'] = $arrays[$i]['payPeriod'] . "天";
                    }
					//$arrays[$i]['rechageMoney'] = 0.01;
                }
                $tptalCount = M(SER_REN_MAN)->where($where)->count();
                $ret = array(
                    "responseStatus" => 1,
                    "data" => $arrays, "count" => $tptalCount
                );
            } else {
                $ret = array("responseStatus" => 300);
            }
        }
        //dump($ret);
        return $ret;
    }
    /**
     * 服务费充值
     *
     * @param [type] $params
     * @return void
     * {requestType: 'servicefee',requestKeywords:recharge, platformID:x,userID:’x’,userPhone:’x’,cycle:X 周期（模板列表中days,money:X 充值金额}
     */
    public function recharge($params)
    {
        $pid = $params['platformID'];
        $phone = parent::decode($params['userPhone']);
        $cycle = $params['cycle'];
        $ret = array("responseStatus" => 102);
        if (parent::check($phone, $params['userID'], $pid)) {
            $bid = parent::busID(array("phone" => $phone, "plat" => $pid));
            //Verify wether it is a salesman
            if (cRec(T_BUS, array("id" => $bid, "level" => 2))) {
                //订单号
                $ordernum = "YWY_" . parent::generate_order_number();
                $startDate = date("Y-m-d");
				//file_put_contents("./aaaaad2", $cycle);
                $timeInterval = date("Y-m-d", strtotime("+" . $cycle . " day", strtotime($startDate)));
				//file_put_contents("./aaaaad3", $timeInterval);
                //当前购买天数
                $current_buy_days = $this->daysbetweendates($startDate, $timeInterval);
				//file_put_contents("./aaaaad5", $current_buy_days);
                //上期剩余天数
                $previous_id = $this->get_last_recharge_overdue_id($pid, $bid, 2);
				//file_put_contents("./aaaaad1", $previous_id);
                $last_remain = $this->last_remain($previous_id, $bid, $pid, $startDate);
                //总计充值天数
                $total_days = $last_remain + $cycle;
                //过期时间
                $endDate = strtotime("$startDate +" . $total_days . " day");
                $data = array(
                    "startDate" => strtotime($startDate),
                    "endDate" => $endDate,
                    "numDays" => $current_buy_days,
                    "lastRemain" => $last_remain,
                    "totalDays" => $total_days,
                    "status" => 1,
                    "plat" => $pid,
                    "bid" => $bid,
                    "payPeriod" => $cycle,
                    "payMoney" => $params['money'],
                    "ordernum" => $ordernum,
                    "isPay" => 1,
                    "rerType" => 2,
                );
                //重复订单验证
                if (cRec(SER_REN_MAN, array("ordernum" => $data['ordernum']))) {
                    $ret = array("responseStatus" => 105);
                } else {
                    //添加订单
                    $id = aRec(SER_REN_MAN, $data);
                    if ($id) {
                        $ret = array(
                            "responseStatus" => 1,
                            "id" => $id,
                            "ordernum" => $ordernum
                        );
                    } else {
                        $ret = array("responseStatus" => 2002);
                    }
                }
            } else {
                $ret = array("responseStatus" => 607);
            }
        }
        return $ret;
    }
    /**
     * 剩余时间计算
     *
     * @param [int] $id
     * @param [int] $bid
     * @param [int] $plat
     * @param [string] $date
     * @return void
     */
    private function last_remain($id, $bid, $plat = 0, $date)
    {
        $days = 0;
        if (!empty($id) || !empty($bid) || !empty($date)) {
            $where = array("id" => $id, "bid" => $bid);
            if ($plat) {
                $where['plat'] = $plat;
            }
            $field = array("(TO_DAYS(from_unixtime( endDate, '%Y.%m.%d' ))- TO_DAYS('" . $date . "')) remain");
            $res = M(SER_REN_MAN)->field($field)->where($where)->find();
			//file_put_contents("./aaaaa1",M()->_sql());
			//file_put_contents("./aaaaa4",var_export($res,true));
            if ($res['remain'] > 0) {
                $days = $res["remain"];
            }
            // $info = M(SER_REN_MAN)->field("endDate")->where($where)->find();
            // if ($info) {
            //     if (strtotime($date) < $info['endDate']) {
            //         $days = $this->daysbetweendates(date("Y-m-d", $info['endDate']), $date);
            //         if ($days >= 0) {
            //             $days = $days;
            //         }
            //     }
            // }
        }
        return $days;
    }
    // protected static function get_last_remain_numdays($plat)
	// {
	// 	$remain = 0;
	// 	if (!empty($plat)) {
	// 		if (cRec(SER_REN_MG_T, array(
	// 			"plat" => $plat, "isPay" => 2, "bid" => 0
	// 		))) {
	// 			$sql = "select max(id) id from " . PREFIX . SER_REN_MG_T . " where plat=" . $plat . " and isPay = 2 and bid = 0";
	// 			$query = M()->query($sql);
	// 			if ($query) {
	// 				$field = array("(TO_DAYS(from_unixtime( endDate, '%Y.%m.%d' ))- TO_DAYS(now())) remain");
	// 				$ser_fee_info = M(SER_REN_MG_T)->field($field)->where(array("id" => $query[0]['id']))->find();
	// 				if ($ser_fee_info['remain'] > 0) {
	// 					$remain = $ser_fee_info['remain'];
	// 				}
	// 			}
	// 		}
	// 	}
	// 	return $remain;
	// }
    /**
     * 查看充值类型
     * @return void
     * {requestType: 'servicefee',requestKeywords:checkrertype, platformID:x} 服务费充值类型验证接口  rerType 1 公司充值 2 自己充值
     */
    public function checkrertype($params)
    {
        $pid = $params['platformID'];
        $ret = array("responseStatus" => 605);
        $ser_rer_type = M("service_recharge_type")->where(array("plat" => $pid))->select();
        if ($ser_rer_type) {
            if (cRec("service_recharge_type", array("plat" => $pid, "isUse" => 1))) {
                $rerType = 0;
                foreach ($ser_rer_type as $val) {
                    if ($val['rerType'] == 1 && $val['isUse'] == 1) {
                        $rerType = 1;
                        break;
                    }
                    if ($val['rerType'] == 2 && $val['isUse'] == 1) {
                        $rerType = 2;
                        break;
                    }
                }
                $ret = array("responseStatus" => 1, "rerType" => $rerType);
            } else {
                $ret = array("responseStatus" => 606);
            }
        }
        return $ret;
    }
    /**
     * 获取充值类型
     *
     * @param [int] $plat
     * @return void
     */
    private function get_recharge_type_info($plat)
    {
        if (!empty($plat)) {
            $info = M(SER_REC_TYPE)->where(array("plat" => $plat, "isUse" => 1))->find();
            if ($info) {
                return $info;
            }
        }
        return false;
    }
    /**
     * 充值模板列表
     * @return array
     *  {requestType: 'servicefee',requestKeywords:templatelist, platformID:x} 服务费续费模板列表
     */
    public function templatelist($params)
    {
        $pid = $params['platformID'];
        $phone = parent::decode($params['userPhone']);
        $ret = array("responseStatus" => 102);
        if (parent::check($phone, $params['userID'], $pid)) {
            $bid = parent::busID(array("phone" => $phone, "plat" => $pid));
            $ret = array("responseStatus" => 300);
            $array = M(SER_FEE_SET)->where(array("plat" => $pid, "status" => 1, "rerType" => 2))->select();
            if ($array) {
                $cyc_list = M(SER_FEE_CYC_TEM)->where(array("status" => 1, "rerType" => 2))->select();
                if ($cyc_list) {
                    //获取最后一次支付订单ID
                    $last_id = $this->get_last_recharge_overdue_id($pid, $bid, 2);
                    $expDays = 0;
                    $now = date("Y-m-d");
                    if ($last_id) {
                        $expDate = parent::getField(SER_REN_MAN, "id", $last_id, "endDate");
                        if ($expDate < strtotime($now)) {
                            $expDays = $this->daysbetweendates(date("Y-m-d", $expDate), $now);
                        }
                    } else {
                        //获取升级业务员时间
                        $upgradeTime = parent::getField(T_BUS, "id", $bid, "upgradeTime");
                        $expDays = $this->daysbetweendates(date("Y-m-d", $upgradeTime), $now);
                    }
                    $data = array();
                    bcscale(2);
                    foreach ($array as $key => $set) {
                        foreach ($cyc_list as $val) {
                            // if ($val['days'] == $set['days']) {
                            $data[$key]['subDays'] = $set['days'] + $expDays . "天";
                            // }
                        }
                        $data[$key]['money'] = bcadd($set['serviceFee'], bcmul(1, $expDays));
                        $data[$key]['days'] = $set['days'] + $expDays;
						//$data[$key]['money'] = 0.01;
                    }
                    $ret = array("responseStatus" => 1, "data" => $data);
                }
            }
        }
        return $ret;
    }
    /**
     * 充值提示(业务员充值类型)
     * status   1  未过期 2 已过期 3 已到期 
     * daysNum  天数
     * {requestType: 'servicefee',requestKeywords:’rechargenotic’, platformID:x,userID:’x’,userPhone:’x’} 服务费提示
     * 返回  status  三个状态 （1 未到期 daysNum 到期天数 expirationDate 过期时间）  （2 已过期  daysNum 过期天数 ）（3 未充值服务费 daysNum从系统注册到当前时间 补充天数）
     */
    public function rechargenotic($params)
    {
        $pid = $params['platformID'];
        $phone = parent::decode($params['userPhone']);
        $ret = array("responseStatus" => 102);
        if (parent::check($phone, $params['userID'], $pid)) {
            $bid = parent::busID(array("phone" => $phone, "plat" => $pid));
            $expiration = '';
            $now = date("Y-m-d");
            //是否有充值成功记录
            if (cRec(SER_REN_MAN, array("bid" => $bid, "plat" => $pid, "isPay" => 2))) {
                //获取最后一次充值记录
                $lastRec = self::get_last_recharge_overdue_id($pid, $bid, 2);
                if ($lastRec) {
                    //过期时间
                    $expiration = parent::getValue(SER_REN_MAN, "id", $lastRec, "endDate");
                    if (strtotime($now) < $expiration) {
                        //未过期
                        $expDays = $this->daysbetweendates(date("Y-m-d", $expiration), $now);
                        $ret = array(
                            "responseStatus" => 1,
                            "status" => 1,
                            "daysNum" => $expDays,
                            "expirationDate" => date("Y-m-d", $expiration)
                        );
                    } else {
                        //已过期返回
                        $expDays = $this->daysbetweendates($now, date("Y-m-d", $expiration));
                        $ret = array(
                            "responseStatus" => 603,
                            "status" => 2,
                            "daysNum" => $expDays,
                            "expirationDate" => date("Y-m-d", $expiration)
                        );
                    }
                } else {
                    //未付服务费
                    //升级业务员时间
                    $expiration = date("Y-m-d", parent::getValue(T_BUS, "id", $bid, "upgradeTime"));
                    $expDays = $this->daysbetweendates($now, $expiration);
                    $ret = array(
                        "responseStatus" => 604,
                        "status" => 3,
                        "daysNum" => $expDays,
                        "expirationDate" => $expiration
                    );
                }
            } else {
                $upgrade = date("Y-m-d", parent::getValue(T_BUS, "id", $bid, "upgradeTime"));
                if ($this->daysbetweendates($now, $upgrade) < DEFAULT_TRIAL_TIME) {
                    $sy = DEFAULT_TRIAL_TIME - $this->daysbetweendates($now, $upgrade);
                    //试用时间
                    $ret = array(
                        "responseStatus" => 1,
                        "status" => 1,
                        "daysNum" => $sy,
                        "expirationDate" => date("Y-m-d", strtotime("+" . $sy . " day", strtotime($now)))
                    );
                } else {
                    //未付服务费
                    $expDays = $this->daysbetweendates($now, $upgrade);
                    $ret = array(
                        "responseStatus" => 604,
                        "status" => 3,
                        "daysNum" => $expDays,
                        "expirationDate" => $upgrade
                    );
                }
            }
        }
        return $ret;
    }
    /**
     *计算两个日期这间的间隔天数
     *
     * @param [string] $startDate
     * @param [string] $endDate
     * @return void
     */
    protected function daysbetweendates(string $date1, string $date2)
    {
        $timestamp1 = strtotime($date1);
        $timestamp2 = strtotime($date2);
        if ($timestamp1 < $timestamp2) {
            $tmp = $timestamp2;
            $timestamp2 = $timestamp1;
            $timestamp1 = $tmp;
        }
        $diff_days = ($timestamp1 - $timestamp2) / 86400;
        return $diff_days;
    }
    /**
     * 获取上期购买信息ID
     *
     * @param [int] $plat
     * @param [int] $bid
     * @param [int] $rerType
     * @return int
     */
    private static function get_last_recharge_overdue_id($plat, $bid, $rerType)
    {
        $id = 0;
        if (!empty($plat) || !empty($bid) || !empty($rerType)) {
            $where = array(
                "plat" => $plat,
                "rerType" => $rerType,
                "isPay" => 2,
                "status" => array("neq", 3)
            );
            switch ($rerType) {
                case 2:
                    $where['bid'] = $bid;
                    break;
            }
            if (cRec(SER_REN_MAN, $where)) {
                $strWhere = "";
                $strWhere .= " where plat = " . $plat . " and isPay = 2 and status != 3 and rerType = " . $rerType;
                if ($rerType == 2) {
                    $strWhere .= " and bid =" . $bid;
                }
                $sql = "select max(id) id from " . PREFIX . SER_REN_MAN . $strWhere;
                $query = M()->query($sql);
                if ($query) {
                    $id = $query[0]['id'];
                }
            }
        }
        return $id;
    }
    /**
     * 函数用途描述
     * @date: 2018年6月25日 上午11:07:21
     * @author: HaiQing.Wu <398994668@qq.com>
     * @param: $table 表名带表前缀
     * @return:
     */
    private function table_colums($table)
    {
        if (!empty($table)) {
            $sql = "select COLUMN_NAME name from information_schema.COLUMNS where table_name = '" . $table . "'  and table_schema ='" . DB_NAME_TTP . "'";
            $query = M()->query($sql);
            if ($query) {
                return $query;
            }
        }
        return false;
    }
}