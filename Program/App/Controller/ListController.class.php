<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2017年12月20日 上午9:44:50
# Filename: ListController.class.php
# Description: 列表类
#================================================
namespace App\Controller;

use Think\Controller;
use Common\Api\ImageManage;

class ListController extends BaseController
{

	/**
	 * 专享模块
	 * {requestType: 'list',requestKeywords:'exclvlist',platformID:x,userID:x,userPhone:x}
	 * @return void
	 */
	public function exclvlist($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$level = M(T_BUS)->where(array("id" => $bid))->getField("level");
			if (cRec(T_EXDISPM, array("pinyin" => $level, "plat" => $pid))) {
				// $sql = "select name title,picUrl,htmlUrl from " . PREFIX . T_EXL_MOMU . " where id in (select mid from " . PREFIX . T_EXDISPM . " where pinyin = '" . $level . "' and plat = {$pid})";
				// echo $sql;
				// $query = M()->query($sql);
				$mids = M(T_EXDISPM)->where(array("pinyin" => $level, "plat" => $pid))->getField("mid");
				$query = M(T_EXL_MOMU)->field("name title,picUrl,htmlUrl")->where(array("id" => array("in", $mids)))->select();
				// dump($query);
				if ($query) {
					foreach ($query as $key => $val) {
						$query[$key]['picUrl'] = BASEURL . $val['picUrl'];
					}
					$ret = array(
						"responseStatus" => 1,
						"status" => 1,
						"data" => $query
					);
				}
			} else {
				$ret = array(
					"responseStatus" => 1,
					"status" => 0,
				);
			}
		}
		return $ret;
	}
	/**
	 * 收货地址列表
	 * @date: 2018年4月11日 下午3:42:25
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function shippingaddress($params)
	{
		$bid = $params['userID'];
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$field = array(
				"id", "name",
				"concat(substring(phone,1,3),'****',substring(phone,8,4)) phone",
				"province", "city", "area",
				"address detailedaddress",
				"defaultState"
			);
			$array = M(T_SPP_ADDRE)->field($field)->where("bid=" . $bid . " && status=1")->order("defaultState ASC")->select();
			if ($array) {
				$ret = array(
					"responseStatus" => 1,
					"data" => $array
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 活动分类
	 * @todo
	 * @date: 2018年3月27日 下午4:13:07
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 */
	public function actclass($params)
	{
		// 		$params = array("platformID" => 1);
		$where = array(
			"plat" => $params["platformID"],
			"status" => 1
		);
		$field = array("id cid", "name");
		$array = M(T_ACTI_CLA)->field($field)->where($where)->select();
		if ($array) {
			$ret = array(
				"responseStatus" => 1,
				"data" => $array
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	/**
	 * 活动列表
	 * @date: 2018年3月27日 上午11:58:50
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function activity($params)
	{
		$where = array(
			"plat" => $params['platformID'],
			"status" => 1
		);
		if ($params['cid']) {
			$where['classify'] = $params['cid'];
		}
		$field = array(
			"*",
			"from_unixtime(createTime,'%Y.%m.%d %H:%i') createTime"
		);
		$array = M(T_ACTI)->field($field)->where($where)->order("hotSort ASC")->select();
		if ($array) {
			$data = array();
			for ($i = 0; $i < count($array); $i++) {
				$data[$i]['id'] = $array[$i]['id'];
				$data[$i]['title'] = $array[$i]['title'];
				$data[$i]['preNum'] = $array[$i]['preNum'];
				$data[$i]['imgUrl'] = BASEURL . $array[$i]["picUrl"];
			}
			$ret = array(
				"responseStatus" => 1,
				"data" => $data
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	/**
	 * 资讯分类列表
	 * @date: 2018年3月27日 上午10:37:03
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: platformID(平台ID)
	 */
	public function inforclass($params)
	{
		// 		$params = array("platformID" => 1);
		$where = array(
			"plat" => $params["platformID"],
			"status" => 1
		);
		$array = M(T_CLA_TIPS)->where($where)->select();
		if ($array) {
			$data = array();
			for ($i = 0; $i < count($array); $i++) {
				$data[$i]['cid'] = $array[$i]['id'];
				$data[$i]['title'] = $array[$i]['title'];
			}
			$ret = array(
				"responseStatus" => 1,
				"data" => $data
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}

	/**
	 * 资讯列表
	 * @date: 2018年3月27日 上午10:13:57
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param platformID(平台ID),cid(资讯ID)
	 * {requestType: list,requestKeywords:informa, platformID:’3’}
	 */
	public function informa($params)
	{
		$where = array(
			"platform_id" => $params['platformID'],
			"e_h_status" => 1
		);
		if ($params['cid']) {
			$where['classify'] = $params['cid'];
		}
		$field = array(
			"*",
			"from_unixtime(e_h_times ,'%Y.%m.%d %H:%i') e_h_times"
		);
		$array = M(T_TIPS)->field($field)->where($where)->order("e_h_times  desc")->limit(0, 1)->select();
		if ($array) {
			$data = array();
			for ($i = 0; $i < count($array); $i++) {
				$data['cid'] = $array[$i]['e_h_id'];
				$data['title'] = $array[$i]['e_h_title'];
				$data['views'] = $array[$i]['views'];
				$data['time'] = $array[$i]['e_h_times'];
				$data['imgurl'] = BASEURL . $array[$i]['e_h_imgurl'];
			}
			$ret = array(
				"responseStatus" => 1,
				"data" => $data,
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	public function information($params)
	{
		$where = array(
			"platform_id" => $params['platformID'],
			"e_h_status" => 1
		);
		if ($params['cid']) {
			$where['classify'] = $params['cid'];
		}
		$page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
		$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
		$offset = ($page - 1) * $limit;
		$field = array(
			"*",
			"from_unixtime(e_h_times ,'%Y.%m.%d %H:%i') e_h_times"
		);
		$array = M(T_TIPS)->field($field)->where($where)->order("e_h_sortnum ASC,e_h_times desc")->limit($offset, $limit)->select();
		if ($array) {
			$data = array();
			for ($i = 0; $i < count($array); $i++) {
				$data[$i]['cid'] = $array[$i]['e_h_id'];
				$data[$i]['title'] = $array[$i]['e_h_title'];
				$data[$i]['views'] = $array[$i]['views'];
				$data[$i]['time'] = $array[$i]['e_h_times'];
				$data[$i]['imgurl'] = BASEURL . $array[$i]['e_h_imgurl'];
			}
			$ret = array(
				"responseStatus" => 1,
				"data" => $data,
				"counts" => count($data)
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	/**
	 * 商户评论
	 * @date: 2018年4月10日 上午11:24:47
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function probuscomment($params)
	{
		// 				$params = array("id" => 1, 
		// 					 "page" => 1);
		$proid = $params['id']; //商品ID
		$page = $params['page'];
		$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
		$offset = ($page - 1) * $limit;
		$field = array(
			"id", "content",
			"concat('**',substring((select busname from " . PREFIX . T_BUS . " b where b.id = c.bid), -1)) busname",
			"from_unixtime(createTime ,'%Y.%m.%d %H:%i:%s') createTime",
			"score", "replyid"
		);
		$where = array(
			"productid" => $proid,
			"status" => 1
		);
		$array = M(T_COMMENT_BUS . " c")->field($field)->where($where)->limit($offset, $limit)->order("createTime DESC")->select();
		if ($array) {
			$data = array();
			foreach ($array as $key => $val) {
				$data[$key]['busname'] = $val['busname'];
				$data[$key]['content'] = $val['content'];
				$data[$key]['comTime'] = $val['createTime'];
				$data[$key]['score'] = $val['score'];
				$comArray = M(T_COMMENT_BUS . " c")->field($field)->where("replyid=" . $val['id'])->select();
				if ($comArray) {
					for ($i = 0; $i < count($comArray); $i++) {
						$data[$key]['reply'][$i]['busname'] = $comArray[$i]['busname'];
						$data[$key]['reply'][$i]['content'] = $comArray[$i]['content'];
						$data[$key]['reply'][$i]['comTime'] = $comArray[$i]['createTime'];
					}
				}
			}
			$totalCount = M(T_COMMENT_BUS)->where($where)->count();
			$ret = array(
				"responseStatus" => 1,
				"data" => $data,
				"count" => $totalCount
			);
		} else {
			$ret['responseStatus'] = 302;
		}
		return $ret;
	}
	/**
	 * 帮助列表
	 * @date: 2018年4月9日 下午4:17:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function apphelp($params)
	{
		$plat = $params['platformID'];
		$class = M(T_UHC)->where(array("status" => 1, "plat" => $plat))->select();
		if (!$class) {
			$ret['responseStatus'] = 300;
		} else {
			$data = array();
			$array = array();
			foreach ($class as $key => $val) {
				$array = M(T_UHM)->where(array("cid" => $val['id']))->select();
				if ($array) {
					$data[$key]['class'] = $val['title'];
					$data[$key]['classUrl'] = BASEURL . $val['picUrl'];
					for ($i = 0; $i < count($array); $i++) {
						$data[$key]['list'][$i]['id'] = $array[$i]['id'];
						$data[$key]['list'][$i]['title'] = $array[$i]['title'];
						$data[$key]['list'][$i]['helpUrl'] = BASEURL . $array[$i]['picUrl'];
					}
				}
			}
			if ($data) {
				$ret = array(
					"responseStatus" => 1, "data" => $data
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		}
		return $ret;
	}
	/**
	 * 用户帮助
	 * @date: 2017年12月16日 下午3:33:21
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function userHelp($params)
	{
		$plat = $params['platformID'];
		$class = M(T_UHC)->where(array("status" => 1, "plat" => $plat))->select();
		if ($class) {
			$data = array();

			foreach ($class as $key => $v) {
				$data[$key]['data'] = M(T_UHM)->where(array("cid" => $v['id']))->select();
				if ($data[$key]['data']) {
					for ($i = 0; $i < count($data[$key]['data']); $i++) {

					}
				}
			}
			$ret = array(
				"responseStatus" => 1,
				"data" => $data
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	/**
	 * 首页模块列表
	 * @date: 2018年3月19日 上午11:36:36
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function module()
	{
		$field = array(
			"hname",
			"hControllerUrl access", "hpicUrl",
			"hHtmlUrl"
		);
		$list = M(T_HMO)->field($field)->where("hstatus=1")->order("hsort ASC")->select();
		if ($list) {
			foreach ($list as $key => $val) {
				$list[$key]['hpicUrl'] = BASEURL . $val['hpicUrl'];
			}
			$ret = array(
				"responseStatus" => 1,
				"data" => $list
			);
		} else {
			$ret['responseStatus'] = 302;
		}
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
	/**
	 * 商品列表
	 * @date: 2018年3月17日 上午10:41:03
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $params 参数
	 */
	public function productlists($params)
	{
		$page = $params['page'];
		$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
		//推荐类型搜索
		$type = empty($params['type']) ? DEFAULT_RECOMMENDED : $params['type'];
		$type = $type;
		if ($type == 2) {
			$type = array(
				"neq",
				DEFAULT_RECOMMENDED
			);
		}
		$where = array(
			"plat" => $params['platformID'],
			"status" => 2, "type" => $type
		);
		$field = array(
			"*",
			"(select name from " . PREFIX . T_COMM_CATE . " cc where cc.id = cd.category_id) name"
		);
		$offset = ($page - 1) * $limit;
		$resArray = M(T_COMMODITY . " cd")->field($field)->where($where)->limit($offset, $limit)->order("stock DESC,addtime")->select();
		$data = array();
		if ($resArray) {
			foreach ($resArray as $key => $val) {
				$data[$key]['commodityName'] = $val['commodityName'];
				$data[$key]['id'] = $val['id'];
				$data[$key]['name'] = $val['name'];
				$data[$key]['sold'] = $val['sold'];
				$data[$key]['rate'] = $val['rate'];
				$data[$key]['inventory'] = $val['stock'];
				$data[$key]['nowPrice'] = $val['nowPrice'];
				$data[$key]['imageData'] = $this->imagePath($val['imgPath']);
			}
			$ret = array(
				"responseStatus" => 1,
				"data" => $data,
				"count" => count($data)
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	/**
	 * 系统消息列表
	 * @param unknown $params
	 * @return number
	 */
	public function sysmsg($params)
	{
		// 		 = array("platformID" => 1, 
		// 			"page" => 1);
		$pid = $params['platformID'];
		$page = $params['page'];
		$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
		$field = array(
			"id", "title", "picUrl",
			"from_unixtime(sendTime,'%Y-%m-%d %H:%i:%s') sendTime"
		);
		$where = array(
			"sender" => $pid,
			"status" => 1
		);
		$offset = ($page - 1) * $limit;
		$array = M("busmessage")->field($field)->where($where)->limit($offset, $limit)->order("sendTime DESC")->select();
		if ($array) {
			foreach ($array as $key => $val) {
				$array[$key]['picUrl'] = BASEURL . $val['picUrl'];
			}
			$totalCount = M("busmessage")->where($where)->count();
			$ret = array(
				"responseStatus" => 1,
				"data" => $array,
				"count" => $totalCount
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	public function sysmsgbak($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$page = $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$fields = "message_id id,message_title title,from_unixtime(message_time) atime,platform_id";
			$offset = ($page - 1) * $limit;
			//单独查询单发信息
			$recordfieds = M('messagerecord')->where('bus_id = ' . $bid)->getField('messageid');
			$recordfiedtr = empty($recordfieds) ? '00' : $recordfieds;
			//单独查询单发信息
			$sql = "select " . $fields . " from (select * from " . PREFIX . "message where message_type=3 and send_status = 1 union select * from " . PREFIX . "message where message_id in (" . $recordfiedtr . ")) e_message where platform_id = " . $pid . " order by message_time desc limit " . $offset . "," . $limit;
			//$list = sRec(T_M,'send_status=1||message_type=3','message_time desc',$params['page'],$limit,$fields);
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
		return $ret;
	}
	/**
	 * 帮助中心列表
	 * @param unknown $params
	 * @return number
	 */
	public function helpcenter($params)
	{
		$pid = $params['platformID'];
		$page = $params['page'];
		$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
		$offset = ($page - 1) * $limit;
		$where = array(
			"plat" => $pid,
			"status" => 1
		);
		$array = M(T_UHM)->field("id,picUrl,title")->where($where)->limit($offset, $limit)->select();
		if ($array) {
			foreach ($array as $key => $va) {
				$array[$key]['picUrl'] = BASEURL . $va['picUrl'];
			}
			$totalCount = M(T_UHM)->where($where)->count();
			$ret = array(
				"responseStatus" => 1,
				"data" => $array,
				"counts" => $totalCount
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	/**
	 * 锦囊列表
	 * @param unknown $params
	 * @return number
	 */
	public function tips($params)
	{
		$pid = $params['platformID'];
		$page = $params['page'];
		$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
		$list = sRec("tips", "e_h_status=2||platform_id=" . $pid, "e_h_times DESC", $page, $limit, "e_h_id id,e_h_title title,FROM_UNIXTIME(e_h_times,'%Y/%m/%d %H:%i:%s') addTime,e_h_imgurl imgurl");
		if ($list) {
			$ret = array(
				"responseStatus" => 1,
				"data" => $list,
				"counts" => count($list)
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	public function advertis($params)
	{
		$pid = $params['platformID'];
		$type = $params['types'];
		$id = $this->getAdverClass($type);
		if ($id) {
			$where = array(
				"displayStatus" => 'Y',
				"status" => 'Y', "classify" => $id
			);
			if ($type != 'ggl') {
				if ($pid) {
					$where['plat'] = $pid;
				}
			}
			if ($id) {
				$array = M(T_ADVER)->field("id,picUrl,accessUrl url")->where($where)->select();
				if ($array) {
					for ($i = 0; $i < count($array); $i++) {
						$array[$i]['picUrl'] = BASEURL . $array[$i]['picUrl'];
					}
					$ret = array(
						"responseStatus" => 1,
						"data" => $array
					);
				} else {
					$ret['responseStatus'] = 300;
				}
			} else {
				$ret['responseStatus'] = 505;
			}
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	protected function getAdverClass($types)
	{
		$id = M(T_ADVER_CLA)->where("abb_name='" . $types . "'")->getField("id");
		if ($id) {
			return $id;
		} else {
			return false;
		}
	}
	/**
	 * banner图列表
	 * @param unknown $params
	 * @return number
	 */
	public function banner($params)
	{
		$pid = $params['platformID'];
		$page = $params['page'];
		$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
		$list = sRec("homemodule", "hstatus=1||platform_id=" . $pid, "hsort asc", $page, $limit, "hid id,hHtmlUrl url,FROM_UNIXTIME(hcreateTime,'%Y/%m/%d %H:%i:%s') addTime,hpicUrl imgurl");
		if ($list) {
			$ret = array(
				"responseStatus" => 1,
				"data" => $list,
				"counts" => count($list)
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	/**
	 * 返现详情
	 * @date: 2018年5月16日 下午6:15:48
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’list’,requestKeywords:’teamdetail’, platformID:’3’,userID:’x’,userPhone:’x’,page:’1’,limit:’20’,'id':X}
	 */
	public function teamdetail($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$field = array(
				"cashMoney",
				"case cashType when 'trade' then '" . TRADE_BACK . "' when 'active' then '" . ACT_REWARDS_BACK . "' end remark",
				"from_unixtime(cashTime ,'%Y/%m/%d %H:%i:%s') cashTime"
			);
			$page = $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$offset = ($page - 1) * $limit;
			$array = M("cash_back_log")->field($field)->where(array(
				"outputAN" => $params['id'],
				"receiveAN" => $bid,
				"isAddWallet" => 1
			))->limit($offset, $limit)->order("cashTime DESC")->select();
			$data = array();
			if ($array) {
				foreach ($array as $key => $val) {
					$data[$key]['remark'] = $val['remark'];
					$data[$key]['money'] = $val['cashMoney'];
					$data[$key]['times'] = $val['cashTime'];
				}
				$ret = array(
					"responseStatus" => 1,
					"listData" => $data
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 我的团队人数 / 领取数  / 返现总金额
	 * @param unknown $params
	 * @return number[]|boolean[]
	 */
	//{requestType: ’list’,requestKeywords:’lowerdata’, platformID:’3’,userID:’x’,userPhone:’x’}
	public function lowerdata($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$ret = array(
				"responseStatus" => 1,
				"teamnum" => $this->getteamnum($bid),
				"getnum" => $this->getnum($bid),
				"rewardsnum" => $this->rewardsnum($bid)
			);

		} else {
			$ret['responseStatus'] = 102;
		}
		// 		dump($ret);
		return $ret;
	}
	/**
	 * 获取当前登录商户上级
	 *
	 * @param [type] $params
	 * @return void
	 * {requestType: list,requestKeywords:parentinfo, platformID:’3’,userID:’x’,userPhone:’x’}
	 */
	public function parentinfo($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array('responseStatus' => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$field = array(
				"(select busname from p_user p where p.id = u.parent ) pname",
				"(select phone from p_user p where p.id = u.parent ) pphone"
			);
			$res = M(T_BUS . " u")->field($field)->where(array("id" => $bid))->select();
			if ($res) {
				$data = array();
				$i = 0;
				while ($i < count($res)) {
					$data['name'] = $res[$i]['pname'];
					$data['phone'] = $res[$i]['pphone'];
					$i++;
				}
				$ret = array(
					"responseStatus" => 1,
					"data" => $data
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		}
		return $ret;
	}
	/**
	 * 我的团队
	 * @param unknown $params
	 * @return number
	 */
	public function team($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$page = $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$where = array(
				"parent" => $bid,
				"status" => 1
			);
			$field = array(
				"id", "busname",
				"from_unixtime(regisTime ,'%Y/%m/%d') regisTime",
				"phone",
				// "concat_ws('****', substring(phone, 1, 3),substring(phone, -4, 4)) phone",
				"(select classname from " . PREFIX . T_BLMCS . " where englishname = level and plat = {$pid}) level",
				"parent"
			);
			$offset = ($page - 1) * $limit;
			$list = M(T_BUS)->field($field)->where($where)->limit($offset, $limit)->order("id DESC")->select();
			if ($list) {
				$data = array();
				for ($i = 0; $i < count($list); $i++) {
					$get = self::getNumber($list[$i]['id']);
					$data[$i]['id'] = $list[$i]['id'];
					$data[$i]['phone'] = $list[$i]['phone'];
					$data[$i]['name'] = $list[$i]['busname'];
					$data[$i]['regtime'] = $list[$i]['regisTime'];
					$data[$i]['getNumber'] = $get;
					$data[$i]['level'] = $list[$i]['level'];
					$data[$i]['rewards'] = self::getRewards($list[$i]['id'], $list[$i]['parent']);
				}
				$totalCount = M(T_BUS)->where($where)->count();
				$ret = array(
					"responseStatus" => 1,
					"data" => $list,
					"counts" => $totalCount
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
	 * 返现金额
	 * @date: 2018年5月16日 下午6:31:36
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected static function getRewards($bid, $parentid)
	{
		$sum = 0;
		if ($bid) {
			$sql = "select ifnull(sum(cashMoney),'0.00') sum from p_cash_back_log where outputAN = " . $bid . " and receiveAN = " . $parentid . " and isAddWallet = 1 and agentLevel = 2";
			$query = M()->query($sql);
			if ($query) {
				$sum = self::subDecimals($query[0]['sum'], 2);
			}
		}
		return $sum;
	}
	/**
	 * 领取数
	 * @date: 2018年4月20日 下午2:46:33
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected static function getNumber($bid)
	{
		$num = 0;
		if ($bid) {
			$where = array("bid" => $bid, "isActive" => 2);
			$re = M("terminal_manage")->where($where)->count();
			if ($re) {
				$num = $re;
			}
		}
		return $num;
	}
	/**
	 * 我的交易列表
	 * @param unknown $params
	 * @return number
	 */
	public function tradelist($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone,
				"platform_id" => $pid
			));
			$page = $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$field = array(
				"remark",
				"ordernum nm",
				"case changeType when 'P' then (select agentLevel FROM `e_cash_back_log` where uniqueIndex = nm and receiveAN = " . $bid . " limit 1) when 'T' then '1' end level",
				"changeAmount", "changeType",
				"case changeType when 'P' then '交易返现' when 'T' then '提现' end typeName",
				"FROM_UNIXTIME(createTime,'%Y/%m/%d %H:%i:%s') createTime"
			);
			$where = array(
				"bid" => $bid,
				"status" => "Y",
				"changeType" => "P"
			);
			$list = sRec("changes_funds", $where, "createTime desc", $page, $limit, $field);
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
		return $ret;
	}
	/**
	 * 交易类型
	 *
	 * @return void
	 * {requestType: ’list’,requestKeywords:'tradeclass', platformID:x}
	 */
	public function tradeclass($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		$ret = array("responseStatus" => 102);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$level = $params['level'];
			if (cRec(T_ICOMEDPM, array("pinyin" => $level, "plat" => $pid))) {
				$mids = M(T_ICOMEDPM)->where(array("pinyin" => $level, "plat" => $pid))->getField("cashID");
				$lists = M(T_BCMCLASS)->field("classname,englishname")->where(array("id" => array("in",$mids), "status" => 1))->select();
				if ($lists) {
					$ret = array(
						"responseStatus" => 1,
						"data" => $lists,
					);
				} else {
					$ret['responseStatus'] = 300;
				}
			}
		}
		return $ret;
	}
	/**
	 * 升级介绍
	 *
	 * @return void
	 *  {requestType: 'list',requestKeywords:'upgradeintro',platformID:x} //升级介绍
	 */
	public function upgradeintro($params)
	{
		$pid = $params['platformID'];
		$idList = M(T_BLMCS)->field("id")->where(array("plat" => $pid))->select();
		if ($idList) {
			$ids = array();
			$i = 0;
			while ($i < count($idList)) {
				$ids[] = $idList[$i]['id'];
				$i++;
			}
			$where = array(
				"plat" => $pid, "isDisplay" => 1,
				"levelID" => array("in", implode(",", $ids))
			);
			$array = M(T_UPGI)->field("*,(select classname from " . PREFIX . T_BLMCS . " where id = levelID ) classname")->where($where)->select();
			$data = array();
			$j = 0;
			do {
				$data[$j]['picUrl'] = BASEURL . $array[$j]['iconimgUrl'];
				$data[$j]['title'] = $array[$j]['classname'];
				$data[$j]['rate'] = $array[$j]['rate'];
				$data[$j]['introduce'] = parent::removeLabel($array[$j]['introduce']);
				$j++;
			} while ($j < count($array));
			$ret = array(
				"responseStatus" => 1,
				"data" => $data,
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		// dump($ret);
		return $ret;
	}
	/**
	 * 提现列表
	 *
	 * @return void
	 * {requestType: 'list',requestKeywords:'drawlist',platformID:x,userID:x,userPhone:x,page:x} //提现列表
	 */
	public function drawlist($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$page = $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$offset = ($page - 1) * $limit;
			$where = array("bid" => $bid, "platform_id" => $pid, "status" => 1);
			$field = "reviewStatus,money dzMoney,ordernum,(select changeAmount from p_changes_funds cf where cf.ordernum = wm.ordernum) drawMoney,case reviewStatus when 1 then '已到账' when 2 then '未审核' when 3 then '审核中' when 4 then '未到账' end rests,from_unixtime(createTime,'%Y.%m.%d %H:%i:%s') drawTime,from_unixtime(reviewTime,'%Y.%m.%d %H:%i:%s') dzTime,(select classname from " . PREFIX . T_BCMCLASS . " where englishname = payType and plat = {$pid}) classname,(select remark from p_changes_funds cf where cf.ordernum = wm.ordernum) remark";
			$dao = M(T_WDM . " wm");
			$array = $dao->field($field)->where($where)->limit($offset, $limit)->order("createTime DESC")->select();
			// echo M()->_sql();
			if ($array) {
				$i = 0;
				while ($i < count($array)) {
					$array[$i]['drawMoney'] = parent::subDecimals($array[$i]['drawMoney'], 2);
					$i++;
				}
				$totalCount = $dao->where($where)->count();
				$ret = array(
					"responseStatus" => 1,
					"data" => $array,
				);
			} else {
				$ret['responseStatus'] = 300;
			}
		}
		// dump($ret);
		return $ret;
	}
	/**
	 * 支出支入明细列表
	 * @param unknown $params
	 * @return number
	 */
	public function incomelist($params)
	{
		/*
		 * 参数
		 * -----------------
		 * userID , userPhone , platformID
		 * storageType  提现支出 TX 
		 * types All 全部  Z 支入  T 支出   
		 *   {requestType: ’list’,requestKeywords:'incomelist', platformID:’3’,userID:’x’,userPhone:’x’,page:’1’,limit:’20’,types:x (All 全部 Z 支入 T 支出 ) ,storageType:'TX' ( 个人提现记录查看必传参数 ，支出支入明细不传 )}  支出支入明细（提现）列表
		 */
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$page = $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$offset = ($page - 1) * $limit;
			$where = array(
				"status" => 'Y',
				"bid" => $bid
			);
			//商户余额收入明细状态提示
			$field = array(
				"bid", "ordernum",
				"(select classname  from " . PREFIX . T_BCMCLASS . " where englishname = storageType and plat = " . $pid . ") storageName",
				"remark"
			);
			if ($params['types'] != 'All') {
				$where['changeType'] = $params['types'];
				//商户余额收入明细
				if ($params['types'] == 'Z') {
					$field["if(proid = 0,'" . OTHERS . "',(select commodityName from " . PREFIX . T_COMMODITY . " co where cf.proid = co.id))"] = "tradeName";
				}
			}
			$where['storageType'] = $params['storageType'];
			// 商户提现记录不包含手动扣款记录
			if ($params['storageType'] == 'TX') {
				$field = array(
					"(select classname from " . PREFIX . T_BCMCLASS . " where englishname = payType and plat = {$pid}) classname",
					"case changeType when 'T' then '" . THE_DRAW . "' end typeName",
					"remark"
				);
			}
			//变动金额
			$field['changeAmount'] = "changeAmount";
			//变动时间
			$field["FROM_UNIXTIME(createTime,'%Y/%m/%d %H:%i:%s')"] = "createTime";
			$dao = M(T_CAPC . " cf");
			$array = $dao->field($field)->where($where)->limit($offset, $limit)->order("createTime DESC")->select();
			if ($array) {
				$i = 0;
				while ($i < count($array)) {
					$array[$i]['changeAmount'] = parent::subDecimals($array[$i]['changeAmount']);
					if ($params['storageType'] != 'TX') {
						$info = $this->get_trade_amount($array[$i]['bid'], $array[$i]['ordernum']);
						$array[$i]['tradeInfo'] = $info;
					}
					$i++;
				}
				if ($params['storageType'] != 'TX') {
					foreach ($array as &$val) unset($val['bid']);
					foreach ($array as &$va) unset($va['ordernum']);
				}
				$totalCount = $dao->where($where)->count();
				$ret = array(
					"responseStatus" => 1, "data" => $array, "counts" => $totalCount
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
	 * 交易信息
	 *
	 * @param [type] $bid
	 * @param [type] $ordernum
	 * @return void
	 */
	public function get_trade_amount($bid, $ordernum)
	{
		$money = '0.00';
		$name = '无';
		$terminal = '无';
		if (!empty($bid) && !empty($ordernum)) {
			$logs = M(T_CASHB)->where(array("receiveAN" => $bid, "uniqueIndex" => $ordernum))->find();
			if ($logs) {
				$tradeData = M($logs['tableName'])->where(array("id" => $logs['tableid'], "tradeOrderNo" => $logs['uniqueIndex']))->find();
				if ($tradeData) {
					$money = $tradeData['tradeAmt'];
					$name = $tradeData['merchantName'];
					$terminal = $tradeData['terminalNo'];
				}
			}
		}
		return array("money" => $money, "name" => $name, "terminal" => $terminal);

	}
	/**
	 * 激活排行
	 *
	 * @return void
	 */
	public function actranking($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$level = parent::getField(T_BUS, "id", $bid, "level");
			if ($level == 1) {
				$ret['responseStatus'] = 300;
			} else {
				$page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
				$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
				$offset = ($page - 1) * $limit;
				$currentMonth = "";
				if (empty($params['types'])) {
					$currentMonth = " and updateTime like '" . date('Y-m') . "%'";
				}
				// $sql = "select rank,phone,busName,money from (select *,(select busname from p_user where id = pid) busName,(select concat_ws('****',substring(phone,1,3),substring(phone,-4,4)) from p_user where id = pid) phone,( @rowNo := @rowNo + 1 ) rank  from (select count(*) money,pid from (SELECT *,(select parent from p_user where id = bid) pid FROM p_terminal_manage where plat = {$pid} and isActive = 2{$currentMonth}) a group by pid order by money desc) x , (SELECT @rowNo := 0 x ) b) over where  phone != '' limit {$offset},{$limit}";
				$sql = "select rank,phone,busName,money from (select *,(select busname from p_user where id = pid) busName,(select concat_ws('****',substring(phone,1,3),substring(phone,-4,4)) from p_user where id = pid) phone,( @rowNo := @rowNo + 1 ) rank  from (select count(*) money,pid from (SELECT *,(select getParentVipID(bid)) pid FROM p_terminal_manage where plat = {$pid} and isActive = 2{$currentMonth}) a group by pid order by money desc) x , (SELECT @rowNo := 0 x ) b) over where  phone != '' limit {$offset},{$limit}";
				echo $sql;
				$rows = M()->query($sql);
				if (count($rows) > 0) {
					$ret = array("responseStatus" => 1, "data" => $rows);
				} else {
					$ret['responseStatus'] = 300;
				}
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		// dump($ret);
		return $ret;
	}
	/**
	 * 激活数
	 *
	 * @param [int] $bid   商户ID
	 * @param [int] $plat  平台ID
	 * @return int
	 */
	protected static function active_con($bid, $plat)
	{
		$con = 0;
		if (!empty($bid)) {
             //平台下所有普通商户
			$busLists = M(T_BUS)->where(array("plat" => $plat, "status" => 1, "level" => 1))->select();
            //获取下级商户ID
			$list = self::get_team_mens($busLists, $bid);
			if (!empty($list)) {
				foreach ($list as $k => $v) {
                    //获取已激活终端
					$terminal = getTerminal($v['id'], '', 2);
					if ($terminal) {
						$con++;
					}
				}
			}
		}
		return $con;
	}
	/**
	 * 获取某商户无限下级方法
	 * @date: 2018年5月18日 下午2:59:19
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $data是所有商户数据表,$bid 商户ID
	 * @return: array
	 */
	protected static function get_team_mens($data, $bid)
	{
		if (!empty($bid)) {
			if (!is_array($data)) {
				return false;
			}
			$teams = array(); //最终结果
			$mids = array($bid); //第一次执行时候的用户id
			do {
				$othermids = array();
				$state = false;
				foreach ($mids as $key => $valueone) {
					foreach ($data as $key => $valuetwo) {
						if ($valuetwo['parent'] == $valueone) {
							$teams[$key]['id'] = $valuetwo[id]; //找到我的下级立即添加到最终结果中
							$othermids[] = $valuetwo['id']; //将我的下级id保存起来用来下轮循环他的下级
							array_splice($members, $key, 1); //从所有会员中删除他
							$state = true;
						}
					}
				}
				$mids = $othermids; //foreach中找到的我的下级集合,用来下次循环
			} while ($state == true);
			return $teams;
		}
		return false;
	}
	/**
	 * 排行
	 * @date: 2018年5月22日 下午3:20:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’list’,requestKeywords:’montranking’, platformID:’3’,userID:’x’,userPhone:’x’,page:x,limit:x,types:x 选填 全部 传All  当月 不传}
	 */
	public function montranking($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$level = parent::getField(T_BUS, "id", $bid, "level");
			if ($level == 1) {
				$ret['responseStatus'] = 300;
			} else {
				$page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
				$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
				$offset = ($page - 1) * $limit;
				if (empty($params['types'])) {
					$date = date("Y-m");
					$where = " and (cashTime  >= " . strtotime($date) . "  and  cashTime < " . strtotime("$date +1 month") . ") ";
				}
				// $sql = "select * from (SELECT truncate(money,2) money,busName,concat_ws('****',substring(phone, 1, 3), substring(phone, 8, 4)) phone, ( @rowNo := @rowNo + 1 ) rank FROM (SELECT sum( cashMoney ) money,( SELECT busname FROM p_user WHERE id = receiveAN ) busName,( SELECT `phone` FROM p_user WHERE id = receiveAN ) phone FROM p_cash_back_log WHERE plat = " . $pid . " and isAddWallet = 1 " . $where . " GROUP BY receiveAN ORDER BY money DESC ) a, ( SELECT @rowNo := 0 x ) b) xx limit {$offset},{$limit}";
				$sql = "SELECT * FROM ( SELECT TRUNCATE (money, 2) money, busName, concat_ws( '****', substring(phone, 1, 3), substring(phone, 8, 4)) phone, ( @rowNo := @rowNo + 1 ) rank FROM ( select * from (SELECT sum(cashMoney) money, ( SELECT busname FROM p_user WHERE id = receiveAN ) busName, ( SELECT `phone` FROM p_user WHERE id = receiveAN ) phone, ( SELECT `level` FROM p_user WHERE id = receiveAN ) `level` FROM p_cash_back_log WHERE plat = " . $pid . " AND isAddWallet = 1 " . $where . " GROUP BY receiveAN  ) c where level = 2 ORDER BY money DESC ) a, ( SELECT @rowNo := 0 x ) b ) xx limit {$offset},{$limit}";
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
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 累计
	 * @date: 2018年5月22日 下午3:20:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * {requestType: ’list’,requestKeywords:’totalsum’, platformID:’3’,userID:’x’,userPhone:’x’,types:x 选填 全部 传All  当月 不传}
	 */
	public function totalsum($params)
	{
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid
			));
			$level = parent::getField(T_BUS, "id", $bid, "level");
			if ($level == 1) {
				$ret = array(
					"responseStatus" => 1,
					"sum" => '0'
				);
			} else {
				if (empty($params['types'])) {
					$date = date("Y-m");
					$where = " and (cashTime  >= " . strtotime($date) . "  and  cashTime < " . strtotime("$date +1 month") . ") ";
				}
				if ($params['types'] == 'ACT') {
					$sql = "select count(*) sum from p_terminal_manage where bid in (select id from p_user where plat = {$pid}) and isActive = 2 and plat = {$pid}";
				} elseif ($params['types'] == 'ACTAll') {
					$sql = "select count(*) sum from p_terminal_manage where bid in (select id from p_user where plat = {$pid}) and isActive = 2 and plat = {$pid} and updateTime like '" . date('Y-m') . "%'";
				} else {
					$sql = "select ifnull(sum(cashMoney),'" . DEFAULT_MONEY . "') sum from p_cash_back_log where  isAddWallet = 1 and plat = " . $pid . $where;
				}
				$query = M()->query($sql);
				$sum = DEFAULT_ACT_COUNT;
				if ($query) {
					$sum = parent::subDecimals($query[0]['sum'], 2);
				}
				$ret = array(
					"responseStatus" => 1,
					"sum" => $sum
				);
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		// 		dump($ret);
		return $ret;
	}
	// 获取用户的推荐激活数
	// protected function getRecommendActivationNum($bid, $plat)
	// {
	// 	$sql = "select count(*) sum from p_terminal_manage where bid in (select id from p_user where parent = {$bid} and  plat = {$plat}) and isActive = 2 and plat = {$plat}";
	// 	$rowCts = M()->query($sql);
	// 	return $rowCts[0]['sum'];
	// }
	/**
	 * 收益排行
	 * @param unknown $params
	 * @param: type  All 全部  Per 个人
	 * @return number
	 */
	public function rankingbak()
	{
		$params = parent::testParams();
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if (parent::check($phone, $params['userID'], $pid)) {
			$bid = parent::busID(array(
				"phone" => $phone,
				"platform_id" => $pid
			));
			$page = $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$offset = ($page - 1) * $limit;
			$date = date("Y-m");
			$where = " and (createTime  >= " . strtotime($date) . "  and  createTime < " . strtotime("$date +1 month") . ") ";
			if ($params['type'] != 'All') {
				$sql = "select * from (select truncate(money,2) money,name,phone,( @rowNo := @rowNo + 1 ) rank,bid from (SELECT sum(changeAmount) money, (SELECT `name` FROM " . PREFIX . T_BUS . " WHERE id = bid ) NAME,(SELECT `phone` FROM e_business WHERE id = bid ) phone,bid FROM e_changes_funds WHERE platform_id = " . $pid . " and changeType != 'J' and storageType != 'withdraw' " . $where . "  GROUP BY bid ORDER BY money DESC) a, ( SELECT @rowNo := 0 x ) b ) x where bid = " . $bid;
				//$sql = "select truncate(money,2) money,name,phone,rank from (SELECT sum(changeAmount) money, (SELECT `name` FROM e_business WHERE id = bid ) NAME,(SELECT `phone` FROM " . PREFIX . T_BUS . " WHERE id = bid ) phone,bid, ( @rowNo := @rowNo + 1 ) rank FROM e_changes_funds, ( SELECT @rowNo := 0 x ) b WHERE storageType != 'withdraw' " . $where . "  GROUP BY bid ORDER BY money DESC) a where bid = " . $bid;
			} else {
				//( SELECT concat(substring(name,1,1),'*',substring(name,-1,1)) name FROM " . PREFIX . T_BUS . " WHERE id = bid )
				$sql = "select * from (SELECT truncate(money,2) money,busName,concat_ws('****',substring(phone, 1, 3), substring(phone, 8, 4)) phone, ( @rowNo := @rowNo + 1 ) rank FROM ( SELECT sum( changeAmount ) money,( SELECT name FROM " . PREFIX . T_BUS . " WHERE id = bid ) busName,( SELECT `phone` FROM " . PREFIX . T_BUS . " WHERE id = bid ) phone FROM " . PREFIX . T_CAPC . " WHERE platform_id = " . $pid . " and changeType != 'J' and storageType != 'withdraw' and status = 'Y' " . $where . " GROUP BY bid ORDER BY money DESC ) a, ( SELECT @rowNo := 0 x ) b) xx limit {$offset},{$limit}";
			}
			$list = M()->query($sql);
			if ($list) {
				$ret = array(
					"responseStatus" => 1,
					"data" => $list,
					"counts" => count($list)
				);
			} else {
				if ($params['type'] != 'All') {
					$row = fRec("business", "id=" . $bid, "name,phone");
					$ret = array(
						"responseStatus" => 1,
						"data" => $row
					);
				} else {
					$ret['responseStatus'] = 300;
				}
			}
		} else {
			$ret['responseStatus'] = 102;
		}
		// dump($params);
		return $ret;
	}
}