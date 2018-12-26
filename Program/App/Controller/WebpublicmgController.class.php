<?php
/**
 * +-------------------------------------------
 * | Description: 网页版接口公共管理
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年6月19日 下午12:09:34
 * +-----------------------------------------------------
 * | Filename: WebpublicmgController.class.php
 * +-----------------------------------------------------
 */
namespace App\Controller;
use Think\Controller;
class WebpublicmgController extends BaseController{
	/**
	 * 资讯页面头部
	 * @date: 2018年6月15日 下午3:42:43
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function load_infor_header(){
		$str = "";
		$str .= "<!DOCTYPE html>";
		$str .= '<html lang="en">';
		$str .= '	<head>';
		$str .= '  		<meta charset="UTF-8">';
		$str .= '  		<meta name="viewport" content="maximum-scale=1.0,minimum-scale=1.0,user-scalable=0,initial-scale=1.0,width=device-width"/>';
		$str .= '  		<meta name="format-detection" content="telephone=no,email=no,date=no,address=no">';
		$str .= '  		<meta http-equiv="X-UA-Compatible" content="ie=edge">';
		$str .= '  		<title>天天刷</title>';
		$str .= '  		<link rel="stylesheet" type="text/css" href="' . BASEURL . '/Public/infor/css/reset.css" />';
		$str .= '  		<link rel="stylesheet" href="' . BASEURL . '/Public/infor/css/common.css?1321321">';
		$str .= '  		<link rel="stylesheet" href="' . BASEURL . '/Public/infor/css/style.css">';
		$str .= '	</head>';
		$str .= '	<body style="background:#f1f1f1;">';
		$str .= '   	<header>';
		$str .= '    		<a class="headerLeftBtn" href="javascript:history.go(-1)">';
		$str .= '       		<img src="' . BASEURL . '/Public/infor/images/backIcon.png" alt="返回">';
		$str .= '   		</a>';
		$str .= '   		资讯';
		$str .= '   	</header>';
		return $str;
	}
	/**
	 * 资讯页面底部
	 * @date: 2018年65日 下午3:42:22
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function load_infor_footer(){
		$str = "";
		$str .= '	</body>';
		$str .= "</html>";
		$str .= '<script src="' . BASEURL . '/Public/infor/js/jquery-1.8.3.min.js"></script>';
		return $str;
	}
}