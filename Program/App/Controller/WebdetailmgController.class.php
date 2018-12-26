<?php
/**
 * +-------------------------------------------
 * | Description: 网页版接口详情页管理
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年6月19日 下午12:09:08
 * +-----------------------------------------------------
 * | Filename: WebdetailmgController.class.php
 * +-----------------------------------------------------
 */
namespace App\Controller;
use Think\Controller;
class WebdetailmgController extends BaseController{
	/**
	 * 资讯详情页
	 * @date: 2018年6月15日 下午3:41:59
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function detail(){
		$id = I("post.id");
		$field = array("e_h_title title", 
			"e_h_content content", 
			"from_unixtime(e_h_times,'%Y.%m.%d %H:%i') createTime", 
			"views");
		$info = M(T_TIPS)->field($field)->where("e_h_id=" . $id)->find();
		if($info){
			M(T_TIPS)->where(array(
				"e_h_id" => $id))->setInc("views");
			$str = "";
			$str .= '<section class="subPageSection">';
			$str .= '	<div class="newsBox">';
			$str .= '		<div class="newsHeader">';
			$str .= '			<h3>' . $info['title'] . '</h3>'; //标题
			$str .= '			<p>';
			$str .= '				<span>' . $info['createTime'] . '</span>'; //发布时间
			$str .= '				<em><img src="' . BASEURL . '/Public/infor/images/eyeIcon.png" alt="阅读次数">' . $info['views'] . '</em>'; //预览次数
			$str .= '			</p>';
			$str .= '		</div>';
			$str .= '		<div class="newsBody">';
			$str .= '			<div class="newsMain">' . $info['content'] . '</div>'; //内容
			$str .= '			<img src="' . BASEURL . '/Public/infor/images/occupiedImg.png" alt="占位图片">';
			$str .= '		</div>';
			$str .= '	</div>';
			$str .= '</section>';
			$ret = array("status" => 1, 
				"data" => $str);
		}else{
			$ret['status'] = 300;
		}
		echo json_encode($ret);
	}
}