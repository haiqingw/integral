<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2017年12月19日 下午1:34:56
# Filename: SystemController.class.php
# Description: 系统类
#================================================
namespace App\Controller;

use Think\Controller;

define("T_APP_SWI", "appswitch");
class SystemController extends BaseController
{
	protected $type = array(
		"tuiguangshuoming" => "hezuo",
		"shuakashuoming" => "about",
		"jianglizhengce" => "joinAgent",
		"zhuceshoukuan" => "hanyin",
		"taolunquntupian" => "qunQrcode",
		"hanyinjianjie" => "hysftbreif",
		"customerservicetelephone" => "vphone",
		"registerxieyi" => "xieyi",
		"appabout" => "about",
		"introduct" => "introduct"
	);
	//{requestType:’system’,requestKeywords:’getsystem’, platformID:’x’,type:’introduct’}  
	public function getsystem($params)
	{
		$key = $this->type[$params['type']];
		$key .= $params['platformID'];
		$content = "";
		switch ($params['type']) {
			//在线客服
			case 'customerservicetelephone':
				$vp = $this->get_online_service($params['platformID']);
				if ($vp) {
					$content = $vp['mobile'];
				}
				break;
			//关于我们
			case 'appabout':
				$vp = $this->get_about_us($params['platformID']);
				if ($vp) {
					$content = $vp['content'];
				}
				break;
			default:
				$content = getVal($key);
				break;
		}
		$ret = array(
			"responseStatus" => 1,
			"content" => $content
		);
		return $ret;
	}
	/**
	 * 获取关于我们
	 * @date: 2018年5月24日 上午11:33:02
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $plat 平台ID
	 * @return: array();
	 */
	private function get_about_us($plat)
	{
		if ($plat) {
			$re = M(T_ABOU_U)->field("content")->where(array(
				"plat" => $plat
			))->find();
			if ($re) {
				return $re;
			}
		}
		return false;
	}
	/**
	 * 获取在线客服电话
	 * @date: 2018年5月24日 上午11:33:02
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $plat 平台ID
	 * @return: array();
	 */
	private function get_online_service($plat)
	{
		if ($plat) {
			$re = M(T_ONCS)->field("landline,mobile")->where(array(
				"plat" => $plat
			))->find();
			if ($re) {
				return $re;
			}
		}
		return false;
	}
	/**
	 * 上架开关
	 */
	// {requestType:system,requestKeywords:appswitch, platformID:zx}
	public function appswitch()
	{
		$where = array("key" => "key_iphone");
		$status = 2;
		if (cRec(T_APP_SWI, $where)) {
			$status = M(T_APP_SWI)->where($where)->getField("value");
			if ($status) {
				$status = $status;
			}
		}
		$ret = array("responseStatus" => 1, "kaiguan" => $status);
		return $ret;
	}
}