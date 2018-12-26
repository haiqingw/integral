<?php

/**
 * 用户重要信息管理
 */
namespace App\Controller;

use Think\Controller;

class FundsController extends BaseController
{
    /**
     * POS 商户交易统计
     *
     * @return void
     * {requestType: 'funds',requestKeywords:'merchandise',platformID:x,userID:x,userPhone:x,types:days 当日 mons 当月,page:x,limit:x}
     */
    public function merchandise($params)
    {
        $pid = $params['platformID'];
        $phone = parent::decode($params['userPhone']);
        if (parent::check($phone, $params['userID'], $pid)) {
            $bid = parent::busID(array("phone" => $phone, "plat" => $pid));
            $page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
            $limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
            //统计日期
            switch ($params['types']) {
                case 'days':
                    $startDate = date("Y-m-d");
                    $endDate = strtotime("+1 day", strtotime($startDate));
                    break;
                case 'mons':
                    $startDate = date("Y-m");
                    $endDate = strtotime("+1 month", strtotime($startDate));
                    break;
            }
            //业务员直属商户信息
            $field = array(
                "id", "busname",
                "concat_ws( '****', substring(phone, 1, 3), substring(phone, - 4, 4)) phone"
            );
            $child_ids = M(T_BUS)->field($field)->where(array("parent" => $bid, "status" => 1))->select();
            if (!$child_ids) {
                $ret['responseStatus'] = 300;
            } else {
                $data = array();
                $con = '0';
                $sum = '0.00';
                $j = 0;
                for ($i = 0; $i < count($child_ids); $i++) {
                    //获取已激活机具列表
                    $terminal_info = getTerminal($child_ids[$i]['id']);
                    if ($terminal_info) {
                        foreach ($terminal_info as $k => $v) {
                            if ($v['tableName'] && $v['terminalNo'] != '未选择') {
                                //查找交易数据
                                $field = array("ifnull(sum(tradeAmt),'0.00') sum", "ifnull(count(*),0) con");
                                $where = array(
                                    "terminalNo" => $v['terminalNo'],
                                    "processStatus" => 2,
                                    "tradeTime" => array(array("egt", strtotime($startDate)), array("lt", $endDate))
                                );
                                $list = M($v['tableName'])->field($field)->where($where)->group("terminalNo")->select();
                                if ($list) {
                                    //商户信息
                                    $data[$j]["busname"] = $child_ids[$i]['busname'];
                                    $data[$j]["phone"] = $child_ids[$i]['phone'];
                                    //终端号
                                    $data[$j]['terminal'] = $v['terminalNo'];
                                    $data[$j]['money'] = $list[0]['sum'] ? $list[0]['sum'] : '0.00';
                                    $data[$j]['con'] = $list[0]['con'] ? $list[0]['con'] : '0';
                                     //总
                                    $sum += $list[0]['sum'];
                                    $con += $list[0]['con'];
                                }
                            }
                            $j++;
                        }
                    }
                }
                if ($con) {
                    $arrayList = $this->arraySort($data);
                    $pageCon = count($arrayList);
                    $offset = ($page - 1) * $limit;
			        //分页显示
                    $pageList = $this->page_array($limit, $page, $arrayList, 0);
                    $ret = array("responseStatus" => 1, "data" => $pageList, "sum" => $sum, "pens" => $con, "pageCon" => $pageCon);
                } else {
                    $ret['responseStatus'] = 300;
                }
            }
        } else {
            $ret['responseStatus'] = 102;
        }
        return $ret;
    }
    /**
     * 数组分页函数
     * $limit  每页多少条数据
     * $page  当前第几页
     * $array  查询出来的所有数组
     * order 0 - 不变   1- 反序
     */
    public function page_array($limit, $page, $array, $order)
    {
        global $countpage; #定全局变量
        if (!empty($limit) || !empty($page) || !empty($array)) {
            $page = (empty($page)) ? '1' : $page; #判断当前页面是否为空 如果为空就表示为第一页面 
            $start = ($page - 1) * $limit; #计算每次分页的开始位置
            if ($order == 1) {
                $array = array_reverse($array);
            }
            $totals = count($array);
            $countpage = ceil($totals / $limit); #计算总页面数
            $pagedata = array();
            $pagedata = array_slice($array, $start, $limit);
            return $pagedata; #返回查询数据
        }
        return false;
    }

    /**
     * 二维数组排序
     * @param array $data 列表
     * @return type
     */
    public function arraySort($data)
    {
        if (is_array($data)) {
            $con = array();
            foreach ($data as $key => $v) {
                $con[] = $v['money'];
            }
            array_multisort($con, SORT_DESC, $data);
            return $data;
        }
        return false;
    }
    
    /**
     * 获取下级商户终端号
     *
     * @param [type] $parentID
     * @param [type] $plat
     * @param [type] $proid
     * @return void
     */
    protected static function getTerminal($bid, $tableName = '', $isActive = ''){
        $where = array('bid'=>$bid);
        if(!empty($isActive)){
            $where['isActive'] = $isActive;
        }
        $terminal = sRec('terminal_manage',$where,'','','');
        if($terminal){
            $newArr = array();
            $newArr1 = array();
            for($i=0;$i<count($terminal);$i++){
                $tName = M('commodity_category')->where('id=(select category_id from p_commodity where id = '.$terminal[$i]['proid'].')')->getField('ruleList');
                $newArr[$i]['tableName'] = str_replace("Cashback/","posdata_",$tName);
                $newArr[$i]['terminalNo'] = $terminal[$i]['terminal'];
                if($tableName == $newArr[$i]['tableName'] || $tableName == str_replace("Cashback/","",$tName)){
                    $newArr1 = $newArr[$i];
                }
            }
            if(!empty($tableName)){
                return $newArr1;
            }else{
                return $newArr;
            }
        }
        return false;
    }
    protected static function getTerminals($parentID, $plat, $proid, $startDate = '', $endDate = '')
    {

        if (!empty($parentID) || !empty($plat) || !empty($proid)) {
            $where = "";
            if (!empty($startDate) || !empty($endDate)) {
                $where = " and (updateTime >= '{$startDate}' and updateTime < '{$endDate}')";
            }
            $sql = "select * from (SELECT bid,terminal,(select getParentVipID(bid)) pid FROM p_terminal_manage where plat = {$plat}  and isActive = 2 and proid =   {$proid} " . $where . ") a where pid = {$parentID}";
            $query = M()->query($sql);
            return $query;
        }
        return false;
    }
}