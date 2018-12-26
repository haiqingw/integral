<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2017年12月20日 上午10:35:30
# Filename: ListdetailController.class.php
# Description: 列表详情类
#================================================
namespace App\Controller;

use Think\Controller;
use Common\Api\ImageManage;

class ListdetailController extends BaseController
{
	/**
	 * 资讯详情
	 * @date: 2018年3月28日 上午10:39:58
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function infordetail($params)
	{
		// 		$params = array("id" => 5);
		$info = M(T_TIPS)->field("e_h_title title,e_h_content content,from_unixtime(e_h_times,'%Y.%m.%d %H:%i') createTime,views")->where("e_h_id=" . $params['id'])->find();
		if ($info) {
			M(T_TIPS)->where(array(
				"e_h_id" => $params['id']
			))->setInc("views");
			//$info['content'] = parent::replacePicUrl($info['content'],BASEURL);
			$ret = array(
				"responseStatus" => 1,
				"data" => $info
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		return $ret;
	}
	/**
	 * 活动详情
	 * @date: 2018年3月28日 上午10:05:04
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function actdetail($params)
	{
		// 		$paramas = array("id" => 1);
		$info = M(T_ACTI)->field("title,content,from_unixtime(createTime,'%Y.%m.%d %H:%i') createTime,preNum")->where("id=" . $params['id'])->find();
		if ($info) {
			M(T_TIPS)->where(array(
				"id" => $params['id']
			))->setInc("preNum");
			$info['content'] = parent::replacePicUrl(htmlspecialchars_decode($info['content']), BASEURL);
			$ret = array(
				"responseStatus" => 1,
				"data" => $info
			);
		} else {
			$ret['responseStatus'] = 300;
		}
		dump($ret);
	}
	/**
	 * 帮助详情
	 * @date: 2017年12月16日 下午3:38:33
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function helpDetail($params)
	{
		$id = $params['id'];
		$field = array(
			"title",
			"FROM_UNIXTIME(createTime ,'%Y/%m/%d %H:%i') createTime",
			"content", "visits"
		);
		$info = M(T_UHM)->field($field)->where(array(
			"id" => $id
		))->find();
		if (!$info) {
			$ret['responseStatus'] = 300;
		} else {
			M(T_UHM)->where(array("id" => $id))->setInc("visits");
			$info['content'] = parent::replacePicUrl(htmlspecialchars_decode($info['content']), BASEURL);
			$ret = array(
				"responseStatus" => 1,
				"data" => $info
			);
		}
		return $ret;
	}
	public function helpDetailbak()
	{
		header("Content-Type: text/html; charset=utf-8");
		$params = I("get.");
		if (!checkParams($params, array("id"))) {
			$status = array(
				"status" => 0,
				"msg" => "缺少参数"
			);
		} else {
			$field = array(
				"title",
				"FROM_UNIXTIME(createTime ,'%Y/%m/%d %H:%i') createTime",
				"content", "visits"
			);
			$info = M(T_UHM)->field($field)->where(array(
				"id" => $params['id']
			))->find();
			$str = "";
			$accessUrl = "";
			//样式引入
			// 			$str .= self::PublicHtmlCss();
			if ($info) {
				$count = parent::getField(T_UHM, "id", $params['id'], "visits");
				if (!$count) {
					$count = 1;
				} else {
					$count++;
				}
				$data['visits'] = $count;
				uRec(T_UHM, $data, array(
					"id" => $params['id']
				));
				//问题列表
				$str .= '<section class="subpageMain"><div class="detailContainer">';
				$str .= '<div class="detailTitleMain line_bottom">';
				$str .= '<h3>' . $info['title'] . '</h3>';
				$str .= '<div>';
				$str .= '<time>TIME:' . $info['createTime'] . '</time>';
				$str .= '<span><img src="' . BASEURL . '/Public/images/pageViewIcon.png"/>' . $info['visits'] . '次</span>';
				$str .= '</div>';
				$str .= '</div>';
				$str .= '<div class="detailContent">' . parent::replacePicUrl(htmlspecialchars_decode($info['content']), BASEURL) . '</div>';
				$str .= '</div></section>';
				//写入Html文件
				$savePath = parent::HelpPath() . "helpDetail.html";
				@file_put_contents($savePath, $str);
				$accessUrl = BASEURL . ltrim($savePath, ".");
			}
			$str .= '<script>window.location.href="' . $accessUrl . '";</script>';
		}
		echo $str;
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
	 * 商品详情
	 * @date: 2018年3月19日 上午9:47:34
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function productdetail($params)
	{
		$field = array(
			"id", "rate", "sold",
			"originalPrice", "nowPrice",
			"commodityTitle title",
			"commodityName", "imgPath",
			"(select name from " . PREFIX . T_COMM_CATE . " cc where cc.id = cd.category_id) name",
			"videoPath", "commodityDetail detail",
			"deposit"
		);
		$detail = M(T_COMMODITY . " cd")->field($field)->where("id=" . $params['id'])->find();
		if ($detail) {
			//图片路径
			$detail['imgPath'] = $this->imagePath($detail['imgPath']);
			if ($detail['videoPath']) {
				//视频路径
				$detail['videoPath'] = BASEURL . $detail['videoPath'];
			}
			$detail['reviewNum'] = $this->commentCon($detail['id']);
			$ret = array(
				"responseStatus" => 1,
				"data" => $detail
			);
		} else {
			$ret['responseStatus'] = 302;
		}
		return $ret;
	}
	/**
	 * 评论数
	 * @date: 2018年4月10日 下午2:51:15
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $id 产品ID
	 */
	public function commentCon($id)
	{
		if (!empty($id)) {
			$field = array(
				"count(*) con",
				"(sum(score)/count(*)) ave"
			);
			$re = M(T_COMMENT_BUS)->field($field)->where("productid=" . $id . " && status=1")->find();
			if ($re) {
				return $re;
			}
		}
		return false;
	}
	/**
	 * 系统消息详情
	 * @param unknown $params
	 * @return number
	 */
	public function sysmsg($params)
	{
		// 		$params = array("id" => 2);
		$id = $params['id'];
		$field = array(
			"title", "visits",
			"from_unixtime(sendTime,'%Y-%m-%d %H:%i:%s') sendTime",
			"content"
		);
		$where = array("id" => $id);
		$dao = M("busmessage");
		$info = $dao->field($field)->where($where)->find();
		if ($info) {
			$info['content'] = parent::replacePicUrl(htmlspecialchars_decode($info['content']), BASEURL);
			$dao->where($where)->setInc("visits");
			$ret = array(
				"responseStatus" => 1,
				"data" => $info
			);
		} else {
			$ret['responseStatus'] = 302;
		}
		return $ret;
	}
	public function sysmsgbak($params)
	{
		$pid = $params['platformID'];
		$bid = parent::busID(array(
			"phone" => $phone,
			"platform_id" => $pid
		));
		$list = fRec("message", "message_id=" . $params['id'], "message_id id,message_title title,message_detail detail,from_unixtime(message_time) atime,'瀚银官方' author,views");
		if ($list) {
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
	 * 帮助中心详情
	 * @param unknown $params
	 * @return number
	 */
	public function helpcenter($params)
	{
		// 		$params = array("id" => 4);
		$id = $params['id'];
		$field = array(
			"title", "content",
			"from_unixtime(createTime,'%Y-%m-%d %H:%i:%s') createTime"
		);
		$where = array("id" => $id);
		$dao = M("helpmanage");
		$info = $dao->field($field)->where($where)->find();
		if ($info) {
			$info['content'] = parent::replacePicUrl(htmlspecialchars_decode($info['content']), BASEURL);
			$dao->where($where)->setInc("visits");
			$ret = array(
				"responseStatus" => 1,
				"data" => $info
			);
		} else {
			$ret['responseStatus'] = 302;
		}
		return $ret;
	}
	public function helpcenterbak($params)
	{
		$pid = $params['platformID'];
		$list = fRec("helpcenter", "e_h_id=" . $params['id'], "e_h_title title,e_h_content content,FROM_UNIXTIME(e_h_times,'%Y/%m/%d %H:%i:%s') addTime,views");
		if ($list) {
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
	 * 锦囊详情
	 * @param unknown $params
	 * @return number
	 */
	public function tips($params)
	{
		$pid = $params['platformID'];
		$list = fRec("tips", "e_h_id=" . $params['id'], "e_h_title title,e_h_content content,FROM_UNIXTIME(e_h_times,'%Y/%m/%d %H:%i:%s') addTime,views");
		if ($list) {
			$ret = array(
				"responseStatus" => 1,
				"data" => $list
			);
		} else {
			$ret['responseStatus'] = 302;
		}
		return $ret;
	}
}