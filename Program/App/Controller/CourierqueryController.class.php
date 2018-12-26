<?php
namespace App\Controller;
use Think\Controller;
class CourierqueryController extends BaseController{
	/*
	 * 网页内容获取方法
	 */
	private function getcontent($url){
		if(function_exists("file_get_contents")){
			$file_contents = file_get_contents($url);
		}else{
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
			$file_contents = curl_exec($ch);
			curl_close($ch);
		}
		return $file_contents;
	}
	/*
	 * 获取对应名称和对应传值的方法
	 */
	private function expressname($order){
		$name = json_decode($this->getcontent("http://www.kuaidi100.com/autonumber/auto?num={$order}"),true);
		$result = $name[0]['comCode'];
		if(empty($result)){
			return false;
		}else{
			return $result;
		}
	}
	/*
	 * 返回$data array      快递数组查询失败返回false
	 * @param $order        快递的单号
	 * $data['ischeck'] ==1 已经签收
	 * $data['data']        快递实时查询的状态 array
	 */
	public function getorder($order, $id){
		$keywords = $this->expressname($order);
		if(!$keywords){
			return false;
		}else{
			$data = array();
			$info = M(T_WAY)->field("content")->where(array(
				"orderid" => $id))->find();
			if($info){
				$content = json_decode($info['content'],true);
				if($content['ischeck'] == 1){
					$data = $content;
				}else{
					$result = $this->getcontent("http://www.kuaidi100.com/query?type={$keywords}&postid={$order}");
					file_put_contents("./resultaaaaaa1" ,$result);
					$res = $this->updateWayBill($id,$result);
					if($res){
						$info = M(T_WAY)->field("content")->where(array(
							"orderid" => $id))->find();
						$data = json_decode($info['content'],true);
					}
				}
			}else{
				$result = $this->getcontent("http://www.kuaidi100.com/query?type={$keywords}&postid={$order}");
				file_put_contents("./resultaaaaaa2" ,$result);
				$res = $this->updateWayBill($id,$result);
				if($res){
					$info = M(T_WAY)->field("content")->where(array(
						"orderid" => $id))->find();
					$data = json_decode($info['content'],true);
				}
			}
			return $data;
		}
	}
	/**
	 * 更新物流信息
	 * @date: 2018年4月24日 下午4:38:19
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $id 订单ID $plat 平台ID $content 内容
	 * @return:
	 */
	protected function updateWayBill($id, $content){
		$flag = true;
		$jsonDate = json_decode($content,true);
		$where = array("orderid" => $id);
		$data = array("content" => $content);
		$info = M(T_WAY)->where($where)->find();
		if(cRec(T_WAY,$where)){
			$content = json_decode($info['content'],true);
			if($content['ischeck'] != 1){
				$data['modifyTime'] = time();
				if(!uRec(T_WAY,$data,$where)){
					$flag = false;
				}
			}
		}else{
			$data['orderid'] = $id;
			$data['createTime'] = time();
			if(!aRec(T_WAY,$data)){
				$flag = false;
			}
		}
		return $flag;
	}
}