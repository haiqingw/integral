<?php
namespace App\Controller;

use Think\Controller;
use Common\Api\ImageManage;

define("ONLINE_P", "online_order");
class OnlineorderController extends BaseController
{
    /**
     * 取消订单
     *
     * @param [type] $params
     * @return boolean
     *  {requestType: 'onlineorder',requestKeywords:'ordernumlimit',platformID:x,userID:x,userPhone:x,id：x 订单ID} //取消订单
     */
    public function cancelorder($params)
    {
        $pid = $params['platformID'];
        $phone = parent::decode($params['userPhone']);
        $ret['responseStatus'] = 102;
        if (parent::check($phone, $params['userID'], $pid)) {
            $bid = parent::busID(array(
                "phone" => $phone, "plat" => $pid
            ));
        }
        return $ret;
    }
    /**
     * 单商品限制数量
     *
     * {requestType: 'onlineorder',requestKeywords:'ordernumlimit',platformID:x ,proid：x 产品ID} //单商品限制数量
     * @return void
     */
    public function ordernumlimit()
    {
        return array("responseStatus" => 1, "minnums" => 10, "maxnums" => 50);
    }
    /**
     * {requestType: 'onlineorder',requestKeywords:'order',platformID:x,userID:x,userPhone:x,orderinfo:购买数量(json格式 {{proid:x产品ID(如闪POS),nums:x 订货数量},{proid:x产品ID(如星支付),nums:x 订货数量}......})二位数组),productinfo:商品金额(json格式 {{proid:x产品ID(如闪POS),money:x 单商品总金额},{proid:x产品ID(如星支付),money:x 单商品总金额}...........)二位数组),money:x订单总金额,sid:x收货地址ID} 订单
     *
     * @param [type] $params
     * @return void
     */
    public function order($params)
    {
        //dump($params);
        $pid = $params['platformID'];
        $phone = parent::decode($params['userPhone']);
        $ret['responseStatus'] = 102;
        if (parent::check($phone, $params['userID'], $pid)) {
            $bid = parent::busID(array(
                "phone" => $phone, "plat" => $pid
            ));
            //重复订单号验证
            $ordernum = parent::generate_order_number();
            $money = $params['money'];
            $remark = empty($params['remark']) ? "" : $params['remark'];
            if (intval($money) <= 0) {
                $ret['responseStatus'] = 109;
            } else {
                $arrs = json_decode($params['productinfo'], true);
                // dump($arrs);
                // while (count($arrs) > 0) {
                    
                // }
                // exit();
                if (cRec(ONLINE_P, array("ordernum" => $ordernum))) {
                    $ret['responseStatus'] = 2002;
                } else {
                    $data = array(
                        "ordernum" => $ordernum,
                        "plat" => $pid,
                        "bid" => $bid,
                        "orderTime" => time(),
                        "isPay" => 2,
                        "orderMoney" => $money,
                        "remark" => $remark,
                        "isShip" => 1,
                        "isOrder" => 1,
                        "productInfo" => $params['orderinfo'],
                        "productMoney" => $params['productinfo']
                    );
                    $shipp = parent::getShipp($params['sid']);
                    if (!$shipp) {
                        $ret['responseStatus'] = 602;
                    } else {
                        $data['consignee'] = $shipp['name'];
                        $data['consigneePhone'] = $shipp['phone'];
                        $data['province'] = $shipp['province'];
                        $data['city'] = $shipp['city'];
                        $data['area'] = $shipp['area'];
                        $data['address'] = $shipp['address'];
                        $id = aRec(ONLINE_P, $data);
                        if ($id) {
                            $ret = array(
                                "responseStatus" => 1, "id" => $id, "ordernum" => $data['ordernum']
                            );
                            // self::pay_success_order_sms_notify($data['ordernum'], $bid);
                        } else {
                            $ret['responseStatus'] = 2002;
                        }
                    }
                }
            }
        }
        return $ret;
    }
    /**
     * 函数用途描述
     * @date: 2018年6月13日 上午10:30:21
     * @author: HaiQing.Wu <398994668@qq.com>
     * @param: variable
     * @return:
     */
    protected static function pay_success_order_sms_notify($ordernum, $bid)
    {
        $check = false;
        if (!empty($ordernum) && !empty($bid)) {
            $sql = "select usertable_ID id,usertable_Phone phone from p_usertable where usertable_ID = (select plat from p_user where id = " . $bid . ") limit 1";
            $query = M()->query($sql);
            if ($query) {
                $sendType = "orderremind";
                $check = true;
            }
            if ($check) {
                parent::phoneVerifyCode($query[0]['phone'], $ordernum, $sendType, $query[0]['id']);
            }
        }
    }
    /**
     * 详情
     * @date: 2018年4月16日 上午11:34:43
     * @author: HaiQing.Wu <398994668@qq.com>
     * {requestType: 'onlineorder',requestKeywords:'detail',id:x订单ID} //订单详情
     */
    public function detail($params)
    {
        $id = $params['id'];
        $field = array(
            "id", "ordernum",
            "consignee", "consigneePhone",
            "concat(province,city,area,address) address",
            "orderMoney", "productInfo", "productMoney",
            "isPay", "isReceipt",
            "from_unixtime(payTime,'%Y-%m-%d %H:%i:%s') payTime",
            "from_unixtime(orderTime,'%Y-%m-%d %H:%i:%s') orderTime",
            "from_unixtime(receiptTime,'%Y-%m-%d %H:%i:%s') receiptTime",
            "if(isPay = 2,'" . NOT_PAY . "',case isReceipt when 1 then '" . ORDER_COMPLETED . "' when 2 then '" . BEEN_SHIP . "' when 3 then '" . TO_SEND . "' when 4 then '" . TO_EVALUATE . "' end) rstatus",
            "case isPay when 2 then '" . NOT_PAY . "' when 1 then '" . HAVE_PAY . "' end pay",
            "courierName",
            "waybillNumber"
        );
        $dao = M(ONLINE_P . " o");
        $info = $dao->field($field)->where("id=" . $id)->find();
        if ($info) {
            $info['productInfo'] = json_decode($info['productInfo'], true);
            $data = array();
            $sum = 0;
            foreach ($info['productInfo'] as $key => $val) {
                $es = M(T_COMMODITY)->where(array("id" => $val['proid']))->find();
                $data[$key]['product'] = $es['commodityName'];
                $data[$key]['price'] = $es['originalPrice'];
                $data[$key]['nums'] = $val['nums'];
                $data[$key]['imgPath'] = $this->imagePath(self::get_imagepath_id($val['proid']));
                $sum += $val['nums'];
            }
            $info['productInfo'] = $data;
            $info['totalNums'] = $sum;
            $ret = array(
                "responseStatus" => 1,
                "data" => $info
            );
        } else {
            $ret['responseStatus'] = 302;
        }
        // dump($ret);
        return $ret;
    }
    /**
     * 列表
     * @date: 2018年4月16日 上午9:47:00
     * @author: HaiQing.Wu <398994668@qq.com>
     * {requestType: 'onlineorder',requestKeywords:'olist', platformID:’x’,userID:’x’,userPhone:’x’ ,page:x(分页),isReceipt:x(发货状态 All全部 1 订单完成  2 已发货 3 等待发货  4  待支付)}
     */
    public function olist($params)
    {
        $pid = $params['platformID'];
        $phone = parent::decode($params['userPhone']);
        if (parent::check($phone, $params['userID'], $pid)) {
            $bid = parent::busID(array(
                "phone" => $phone, "plat" => $pid
            ));
            $page = $params['page'];
            $limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
            $dates = empty($params['dates']) ? DEFAULT_SEARCH_DATE : $params['dates'];
            $field = array(
                "id", "ordernum",
                "orderMoney", "productInfo", "productMoney",
                "isPay", "isReceipt", "isOrder",
                "from_unixtime(orderTime,'%Y-%m-%d %H:%i:%s') orderTime",
                "if(isPay = 2,'" . NOT_PAY . "', case isReceipt when 1 then '" . ORDER_COMPLETED . "' when 2 then '" . BEEN_SHIP . "' when 3 then '" . TO_SEND . "' when 4 then '" . TO_EVALUATE . "' end) rstatus",
                "case isPay when 2 then '" . NOT_PAY . "' when 1 then '" . HAVE_PAY . "' when 3 then '" . HAVE_REFUND . "' end pay",
            );
            $offset = ($page - 1) * $limit;
            $where = array(
                "bid" => $bid,
                "plat" => $pid,
                "isOrder" => array("neq", 3)
            );
            switch ($params['isReceipt']) {
                case 1:
                    $where['isReceipt'] = $params['isReceipt'];
                    break;
                case 2:
                    $where['isReceipt'] = $params['isReceipt'];
                    break;
                case 3:
                    $where['isReceipt'] = $params['isReceipt'];
                    $where['isPay'] = 1;
                    break;
                case 4:
                    $where['isPay'] = 2;
                    break;
            }
            $dao = M(ONLINE_P . " o");
            $array = $dao->field($field)->where($where)->limit($offset, $limit)->order("orderTime DESC")->select();
            // dump($array);
            $data = array();
            $arrs = array();

            if ($array) {
                foreach ($array as $key => $val) {
                    $data[$key]['id'] = $val['id'];
                    $data[$key]['ordernum'] = $val['ordernum'];
                    $data[$key]['isOrder'] = $val['isOrder'];
                    $data[$key]['orderMoney'] = $val['orderMoney'];
                    $data[$key]['orderTime'] = $val['orderTime']; //订单时间
                    $data[$key]['isPay'] = $val['isPay']; // 支付状态  1 未付款 2 已付款
                    $data[$key]['isReceipt'] = $val['isReceipt']; //发货状态提示   isPay  返回 1   receipt 提示未付款
                    $data[$key]['receipt'] = $val['rstatus']; //发货状态 1 订单完成  2 已发货 3 等待发货  4 待评价
                    $product = json_decode($val['productInfo'], true);
                    $nums = 0;
                    foreach ($product as $k => $v) {
                        $es = M(T_COMMODITY)->where(array("id" => $v['proid']))->find();
                        $arrs[$k]['product'] = $es['commodityName'];
                        $arrs[$k]['price'] = $es['originalPrice'];
                        $arrs[$k]['nums'] = $v['nums'];
                        $nums += $v['nums'];
                        $arrs[$k]['imgPath'] = $this->imagePath(self::get_imagepath_id($v['proid']));
                    }
                    $data[$key]['productInfo'] = $arrs;
                    $data[$key]['totalNums'] = $nums;
                }
                $totalCount = $dao->where($where)->count();
                $ret = array(
                    "responseStatus" => 1,
                    "data" => $data,
                    "count" => $totalCount
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
     * 获取图片路径
     * @date: 2018年3月17日 下午1:34:22
     * @author: HaiQing.Wu <398994668@qq.com>
     * @param:  $imgID 获取图片ID
     * @return: array
     */
    public function imagePath($imgID)
    {
        $obj = new ImageManage();
        $res = $obj->getImagePathArray($imgID);
        return $res;
    }
    protected static function get_imagepath_id($proid)
    {
        return parent::getValue(T_COMMODITY, "id", $proid, "imgPath");
    }
}