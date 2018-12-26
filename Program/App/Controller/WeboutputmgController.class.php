<?php
/**
 * +-------------------------------------------
 * | Description: HTML 网页版接口输出管理
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年6月19日 上午10:04:01
 * +-----------------------------------------------------
 * | Filename: InformanageController.class.php
 * +-----------------------------------------------------
 */
namespace App\Controller;
use Think\Controller;
class WeboutputmgController extends BaseController{
	/**
	 * 资讯列表（ 页面body）
	 * @date: 2018年6月15日 上午10:42:32
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * {requestType: ’Weboutputmg’,requestKeywords:’lists’, platformID:x,userID:’x’,userPhone:’x’} //资讯列表
	 * @return:
	 */
	public function lists($params){
		$pid = $params['platformID'];
		$phone = parent::decode($params['userPhone']);
		if(parent::check($phone,$params['userID'],$pid)){
			$bid = parent::busID(array(
				"phone" => $phone, "plat" => $pid));
			$class_list = M(T_CLA_TIPS)->field("id,title")->where(array(
				"plat" => $pid, "status" => 1))->select();
			$str = "";
			if($class_list){
				$public = A("Webpublicmg");
				$str .= $public->load_infor_header();
				$str .= '<div class="classifyTap flex line_bottom">';
				foreach($class_list as $key => $val){
					$key = $key + 1;
					if($key == 1){
						$str .= '<a href="javascript:;" data-id="' . $val['id'] . '" class="line_right active">' . $val['title'] . '</a>';
					}else{
						$str .= '<a href="javascript:;" data-id="' . $val['id'] . '" class="line_right">' . $val['title'] . '</a>';
					}
				}
				$str .= '</div>';
				$str .= '<section class="newsListContainer">';
				$str .= '	<div class="newsListMain" id="newsListMain"></div>';
				$str .= "	<a href='javascript:;' class='loadingMoreBtn' style='display:block;width:30%;height:35px;line-height:36px;text-align:center;font-size:14px;color:#fff;background:#00aeff;border-radius:5px;margin:20px auto;'>加载更多</a>'";
				$str .= '</section>';
				$str .= '<div class="msgdetail" style="display:none;">';
				$str .= '	<header>';
				$str .= '		<a class="headerLeftBtn" id="headerLeftBtn" href="javascript:;"> <img src="' . BASEURL . '/Public/infor/images/backIcon.png" alt="返回"></a> 资讯详情';
				$str .= '	</header>';
				$str .= '	<div class="msgdetailBody"></div>';
				$str .= '</div>';
				$str .= $public->load_infor_footer();
				$js = A("Webjsmg");
				$str .= $js->loading_infor_js();
				$savePath = parent::get_public_file_Path(INFO_HTML_VIEW_FILE_URL,$pid,$bid) . "lists.html";
				@file_put_contents($savePath,$str);
				$accessUrl = BASEURL . ltrim($savePath,".");
				$ret = array(
					"responseStatus" => 1, 
					"accesUrl" => $accessUrl);
			}else{
				$ret['responseStatus'] = 300;
			}
		}else{
			$ret['responseStatus'] = 102;
		}
		return $ret;
	}
	/**
	 * 获取资讯列表
	 * @date: 2018年6月15日 下午3:41:45
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:
	 * @return:
	 */
	public function get_list_info(){
		$params = I("post.");
		$ret = array("status" => 0);
		if(!empty($params["id"])){
			$page = empty($params['page']) ? DEFAULT_PAGE : $params['page'];
			$limit = empty($params['limit']) ? DEFAULT_LIMIT : $params['limit'];
			$where['classify'] = $params['id'];
			$where['e_h_status'] = 1;
			$field = array("*", 
				"from_unixtime(e_h_times ,'%Y.%m.%d %H:%i') e_h_times");
			$offset = ($page - 1) * $limit;
			$array = M(T_TIPS)->field($field)->where($where)->order("e_h_sortnum ASC")->limit($offset,$limit)->select();
			if($array){
				$data = array();
				for($i = 0;$i < count($array);$i++){
					$data[$i]['cid'] = $array[$i]['e_h_id'];
					$data[$i]['title'] = $array[$i]['e_h_title'];
					$data[$i]['views'] = $array[$i]['views'];
					$data[$i]['time'] = $array[$i]['e_h_times'];
					$data[$i]['imgurl'] = BASEURL . $array[$i]['e_h_imgurl'];
				}
				$totalCount = M(T_TIPS)->where($where)->count();
				$ret = array("status" => 1, 
					"data" => $data, 
					"count" => $totalCount);
			}
		}
		echo json_encode($ret);
	}
}