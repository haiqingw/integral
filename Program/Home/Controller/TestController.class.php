<?php
namespace Home\Controller;
use Think\Controller;
class TestController extends Controller{
	public function index(){
		$this->display();
	}
	/**
	 * 评论
	 * @date: 2018年3月21日 下午4:24:49
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function doComment(){
		$params = I("post.");
		$params['bid'] = 2;
		$params['pid'] = 1;
		$params['score'] = 4;
		$params['platformID'] = 1;
		if(!checkParams($params,array(
			"bid", 
			"content", 
			"score", 
			"pid", 
			"platformID"
		))){
			$ret = array(
				"status" => 0, 
				"msg" => "缺少参数"
			);
		}else{
			$data = array(
				"content" => $params['content'], 
				"bid" => $params['bid'], 
				"productid" => $params['pid'], 
				"createTime" => time(), 
				"status" => 2, 
				"score" => $params['score'], 
				"plat" => $params['platformID']
			);
			/**Todo
			 * 1.验证商户状态
			 * 2.检查订单是否已完成（未完成不能评论）
			 * 3.平台产品是否存在
			 */
			if(aRec("comment",$data)){
				$ret = array(
					"status" => 1, 
					"msg" => "评论完成"
				);
			}else{
				$ret = array(
					"status" => 0, 
					"msg" => "评论失败"
				);
			}
		}
		echo json_encode($ret);
	}
}