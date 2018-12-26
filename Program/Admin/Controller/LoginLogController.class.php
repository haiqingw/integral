<?php
namespace Admin\Controller;
/**
 * +----------------------------------------------------------------------
 * | Login Log Management
 * +----------------------------------------------------------------------
 * | @author HaiQing.Wu <398994668@qq.com>
 * +----------------------------------------------------------------------
 * | last Time : 2016/09/30
 * +----------------------------------------------------------------------
 */
class LoginLogController extends CommonController{
	public function index(){
		if(!IS_AJAX){
			return false;
		}else{
			$params = I("post.");
			if(empty($params)){
				$startDate = date("Y-m-d");
				$endDate = date("Y-m-d");
			}else{
				$startDate = $params['startLoginTime'];
				$endDate = $params['endLoginTime'];
				$params['loginKeywords'] ? $where['bid'] = array(
					"IN", 
					self::getBusIds($params['loginKeywords'])
				) : "";
			}
			$where['LoginTime'] = array(
				array(
					"egt", 
					strtotime($startDate)
				), 
				array(
					"lt", 
					strtotime("$endDate + 1 day")
				)
			);
			$page = I('pageNum') ? I('pageNum') : 1; //第几页
			$limit = I('numPerPage') ? I('numPerPage') : 25;
			$offset = ($page - 1) * $limit;
			$field = array(
				PREFIX . LL_T . ".*", 
				PREFIX . U_T . ".e_name", 
				PREFIX . U_T . ".e_tel", 
				PREFIX . U_T . ".e_businessname"
			);
			$order = "LoginTime DESC";
			$join = array(
				"LEFT JOIN " . PREFIX . U_T . " ON " . PREFIX . LL_T . ".bid = " . PREFIX . U_T . ".e_id"
			);
			$resArray = M(LL_T)->field($field)->join($join)->where($where)->order($order)->limit($offset,$limit)->select();
			for($i = 0;$i < count($resArray);$i++){
				$resArray[$i]['loginTime'] = date("Y-m-d H:i:s",$resArray[$i]['loginTime']);
			}
			$totalCount = M(LL_T)->join($join)->where($where)->count();
			$this->assignAll([
				"totalCount" => $totalCount, 
				"logArray" => $resArray, 
				"numPerPage" => $limit, 
				"page" => $page, 
				"startLoginTime" => $startDate, 
				"endLoginTime" => $endDate, 
				"params" => $params
			]);
			$this->display();
		}
	}
	public function log(){
		if(!IS_AJAX){
			return false;
		}else{
			$params = I("post.");
			if(empty($params)){
				$startDate = date("Y-m-d");
				$endDate = date("Y-m-d");
			}else{
				$startDate = $params['startLoginTime'];
				$endDate = $params['endLoginTime'];
				$params['loginKeywords'] ? $where['uid'] = array(
					"IN", 
					self::getBusIds($params['loginKeywords'])
				) : "";
				$params['ordernum'] ? $where['content'] = array(
					"LIKE", 
					"%" . $params['ordernum'] . "%"
				) : "";
			}
			$where['addtime'] = array(
				array(
					"egt", 
					$startDate
				), 
				array(
					"lt", 
					date('Y-m-d',strtotime("$endDate + 1 day"))
				)
			);
			$page = I('pageNum') ? I('pageNum') : 1; //第几页
			$limit = I('numPerPage') ? I('numPerPage') : 30;
			$offset = ($page - 1) * $limit;
			$field = array(
				PREFIX . L_T . ".*", 
				PREFIX . BUS_T . ".realName e_name", 
				PREFIX . BUS_T . ".phone e_tel", 
				PREFIX . BUS_T . ".name  e_businessname"
			);
			$join = array(
				"LEFT JOIN " . PREFIX . BUS_T . " ON " . PREFIX . L_T. ".uid = " . PREFIX . BUS_T . ".id"
			);
			$resArray = M(L_T)->field($field)->join($join)->where($where)->limit($offset,$limit)->order("addtime DESC")->select();
			$totalCount = M(L_T)->join($join)->where($where)->count();
			$this->assignAll([
				"totalCount" => $totalCount, 
				"numPerPage" => $limit, 
				"page" => $page, 
				"startLoginTime" => $startDate, 
				"endLoginTime" => $endDate, 
				"params" => $params, 
				're' => $resArray
			]);
			$this->display();
		}
	}
	/**
	 *  获取商户ID
	 */
	private static function getBusIds($keywords, $type = 1){
		$str = 0;
		if(empty($keywords)){
			return $str;
		}else{
			switch($type){
				case 1:
					$where['e_tel|e_name'] = array(
						"LIKE", 
						"%" . $keywords . "%"
					);
					break;
				case 2:
					$where['e_businessname'] = array(
						"LIKE", 
						"%" . $keywords . "%"
					);
					break;
			}
			$res = M(U_T)->field("e_id")->where($where)->select();
			if($res){
				$ids = array();
				foreach($res as $val){
					$ids[] = $val['e_id'];
				}
				$str = implode(",",$ids);
			}
			return $str;
		}
	}
	public function inter(){
		$params = I("post.");
		$table = "interface_log";
		if(empty($params)){
			$startDate = date("Y-m-d");
			$endDate = date("Y-m-d");
		}else{
			$startDate = $params['startLoginTime'];
			$endDate = $params['endLoginTime'];
		}
		$where['addtime'] = array(
			array(
				"egt", 
				strtotime($startDate)
			), 
			array(
				"lt", 
				strtotime("$endDate + 1 day")
			)
		);
		$page = I('pageNum') ? I('pageNum') : 1; //第几页
		$limit = I('numPerPage') ? I('numPerPage') : 20;
		$offset = ($page - 1) * $limit;
		$resArray = M($table)->where($where)->limit($offset,$limit)->order("addtime DESC")->select();
		$totalCount = M($table)->where($where)->count();
		$this->assignAll([
			"totalCount" => $totalCount, 
			"numPerPage" => $limit, 
			"page" => $page, 
			"startLoginTime" => $startDate, 
			"endLoginTime" => $endDate, 
			"params" => $params, 
			're' => $resArray
		]);
		$this->display();
	}
}
