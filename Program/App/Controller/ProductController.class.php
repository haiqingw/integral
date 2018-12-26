<?php
/**
 * +-------------------------------------------
 * | Description: 产品管理 
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年3月17日 上午10:40:22
 * +-----------------------------------------------------
 * | Filename: ProductController.class.php
 * +-----------------------------------------------------
 */
namespace App\Controller;
use Think\Controller;
use Common\Api\ImageManage;
class ProductController extends BaseController{
	
	/**
	 * 获取图片路径
	 * @date: 2018年3月17日 下午1:34:22
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:  $imgID 获取图片ID
	 * @return: array
	 */
	public function imagePath($imgID){
		$obj = new ImageManage();
		$res = $obj->getImagePathArray($imgID);
		return $res;
	}
	/**
	 * 列表
	 * @date: 2018年3月17日 上午10:41:03
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $params 参数
	 */
	public function lists(){
		$params = array(
			"plat" => 1
		);
		$field = array(
			"*", 
			"(select name from " . PREFIX . T_COMM_CATE . " cc where cc.id = cd.category_id) name"
		);
		$resArray = M(T_COMMODITY . " cd")->field($field)->where("plat=" . $params['plat'] . " && status=2")->select();
		$data = array();
		if($resArray){
			foreach($resArray as $key => $val){
				$data[$key]['id'] = $val['id'];
				$data[$key]['name'] = $val['name'];
				$data[$key]['sold'] = $val['sold'];
				$data[$key]['rate'] = $val['rate'];
				$data[$key]['imageData'] = $this->imagePath($val['imgPath']);
			}
		}
		$ret = array(
			"responseStatus" => 1, 
			"data" => $data
		);
		return $ret;
	}
}