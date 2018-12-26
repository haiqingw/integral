<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2017年12月15日 下午2:02:56
# Filename: GetwayController.class.php
# Description: 瀚银手付通App入口
#================================================
namespace App\Controller;

use Think\Controller;

class GetwaysController extends BaseController
{
	public function routeFromSmallProgram()
	{
		$params = I("post.data");
		$params = json_decode($params, true);
		$saveDir = "./Uploads/RouteSmallProgram/";
		if (!is_dir($saveDir)) mkdir($saveDir);
		file_put_contents($saveDir . date("Y-m-d") . ".txt", var_export($params, true) . "\n", FILE_APPEND);
		//$params = array('requestType'=>'system','requestKeywords'=>'getsystem','platformID'=>3,'type'=>'1');
		$type = ucfirst(strtolower($params['requestType']));
		$keyword = strtolower($params['requestKeywords']);
		$requireParams = self::mergeCheckParams($type, $keyword);
		if ($requireParams && self::checkParams($params, $requireParams)) {
			$class = A($type);
			if (method_exists($class, $keyword)) {
				$ret = $class->$keyword($params);
			} else {
				$ret['responseStatus'] = 100;
			}
		} else {
			$ret['responseStatus'] = 101;
		}
		//dump($ret);
		echo json_encode($ret);
	}
	public function route()
	{
		$params = I("post.");
		// $params = array(
		// 	'requestType' => 'personal',
		// 	'requestKeywords' => 'jiangfeilv',
		// 	"userID" => md5(203),
		// 	"userPhone" => self::encode('13704751958'),
		// 	"platformID" => 175,
		// 	// "types" => 'All'
		// 	// "ordernum" => "YWY_2018080672859",
		// 	// "money" => "1.00"
		// );
		$params = array(
			'requestType' => 'earnings',
			'requestKeywords' => 'myincome',
			'platformID' => 203,
			'userID' => md5(1),
			'userPhone' => parent::encode(13354872086),
			// 'page' => 1,
			// 'isReceipt' => 'All'
			// "orderinfo" => '[{"proid":1,"nums":10},{"proid":2,"nums":5}]',
			// "productinfo" => '[{"proid":1,"money":1000},{"proid":2,"money":500}]',
			// "money" => 1500,
			// "sid" => 1
			// "childID" => 560,
			// "machineID" => 464,
			// "terminal" => "01180425075802578717"
			// 'money' => '10',
			// 'types' => 'ACT'
		);
		dump($params);
		// exit();
		// userID,platformID,userPhone
		$saveDir = "./Uploads/Route/";
		if (!is_dir($saveDir))
			mkdir($saveDir);
		file_put_contents($saveDir . date("Y-m-d") . ".txt", var_export($params, true) . "\n", FILE_APPEND);
		//$params = array('requestType'=>'system','requestKeywords'=>'getsystem','platformID'=>3,'type'=>'1');
		$type = ucfirst(strtolower($params['requestType']));
		$keyword = strtolower($params['requestKeywords']);
		$requireParams = self::mergeCheckParams($type, $keyword);
		if ($requireParams && self::checkParams($params, $requireParams)) {
			$class = A($type);
			if (method_exists($class, $keyword)) {
				$ret = $class->$keyword($params);
			} else {
				$ret['responseStatus'] = 100;
			}
		} else {
			$ret['responseStatus'] = 101;
		}
		echo json_encode($ret);
	}
	/**
	 * 检测元素是否正确
	 * 只能检测一维数组
	 * @param $arr 被检测的数组
	 * @param $repar $arr中必要的元素(键名)
	 * @return boolean
	 */
	protected static function checkParams($arr, $repar = array())
	{
		if (!is_array($arr)) {
			return false;
		}
		if (!count($arr)) {
			return false;
		}
		// 判断每个元素是否存在
		if (count($repar)) {
			foreach ($repar as $val) {
				if (!in_array($val, array_keys($arr))) {
					return false;
					break;
				}
			}
		}
		// 判断每个元素的值是否正确
		foreach ($arr as $val) {
			if ($val == "" || $val == false || $val == null || $val == "undefined" || $val == "null") {
				return false;
				break;
			}
		}
		return true;
	}
	/**
	 * 合并检测参数数组
	 * @param unknown $type
	 * @param unknown $keyword
	 * @return array|boolean
	 */
	protected static function mergeCheckParams($type, $keyword)
	{
		if (!empty($type) && !empty($keyword)) {
			$require = C('requireParams');
			$check = C('checkParams')[$type][$keyword];
			if (!empty($check)) {
				return array_merge(@explode(',', $require), @explode(',', $check));
			}
		}
		return false;
	}
}