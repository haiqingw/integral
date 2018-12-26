<?php
namespace Home\Controller;

use Think\Controller;
/**
 * +-------------------------------------------
 * | Description:商户注册 
 * +-------------------------------------------
 * | Author HaiQing.Wu <398994668@qq.com>
 * +-----------------------------------------------------
 * | Date :  2018年3月6日 上午11:04:40
 * +-----------------------------------------------------
 * | Filename: RegisterController.class.php
 * +-----------------------------------------------------
 */
class RegisterController extends BaseController
{
	public function protocol()
	{
		$val = getVal("xieyi");
		$this->assign("info", $val);
		$this->display();
	}
	/**
	 * 注册页
	 * @date: 2018年3月6日 下午2:30:12
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function register()
	{
		$this->display();
	}
	// public function success()
	// {
	// 	$this->display();
	// }
	/**
	 * 介绍页
	 * @date: 2018年3月6日 上午11:11:36
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * bid 上级商户ID
	 * resType  SM 扫码注册  ZJ 直接注册
	 */
	public function index()
	{
		$params = I("get.");
		if (!empty($params['code'])) {
			$de = self::decode("" . $params['code']);
			$ex = @explode(",", $de);
			if (count($ex) == 2) {
				foreach ($ex as $val) {
					$ex1 = @explode(":", $val);
					switch ($ex1[0]) {
						case "bid":
							$bid = $ex1[1];
							break;
						case "resType":
							$resType = $ex1[1];
							break;
						default:
								//404
							break;
					}
				}
				if (!empty($bid) && !empty($resType)) {
					$ret = M(T_BUS)->field("id,code")->where("id=" . $bid)->find();
					if ($ret) {
						$this->assignAll(["info" => $ret, "resType" => $resType]);
						$this->display();
					} else {
							//404
						$this->showImg();
					}
				} else {
						//404
					$this->showImg();
				}
			} else {
					//404
				$this->showImg();
			}
		} else {
				//404;
			$this->showImg();
		}
	}
	public function showImg()
	{
		$this->display('sorry');
	}
	/**
	 * 解密
	 * @param String $string 需要解密的字串
	 * @param String $skey 解密KEY
	 * @return String
	 */
	protected static function decode($string = '', $skey = "sjfwporwejclamlmerew")
	{
		$strArr = str_split(str_replace(array(
			'O0O0O',
			'o000o',
			'oo00o'
		), array(
			'=',
			'+',
			'/'
		), $string), 2);
		$strCount = count($strArr);
		foreach (str_split($skey) as $key => $value)
			$key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
		return base64_decode(join('', $strArr));
	}
	/**
	 * 商户注册操作
	 * @date: 2017年11月24日 下午4:07:15
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * id 上级商户ID
	 * name 商户名称
	 * phone 注册手机号
	 * verify 手机验证码
	 * resType 注册类型
	 * password 登录密码
	 */
	public function doRegister()
	{
		$params = I("post.");
		if (!parent::checkParams($params, array(
			"id", "name", "phone", "verify",
			"resType", "password"
		))) {
			$status = array(
				"status" => 0,
				"msg" => "信息不全注册失败"
			);
		} else {
			//上级代理商ID或者pos公司ID
			$id = $params['id'];
			$phone = trim($params['phone']);
			$ve = trim($params['verify']);
			$verify = session("code_" . $phone);
			$expire = session("expire_" . $phone);
			//验证码
			if (!isset($expire) || (time() - $expire) > 60) {
				session("expire_" . $phone, null);
				session("code_" . $phone, null);
				$status = array("status" => 0, "msg" => "验证码已失效请重新发送");
			} else {
				if ($ve != $verify) {
					$status = array("status" => 0, "msg" => "验证码不匹配");
				} else {
					$resType = $params['resType'];
					$plat_id = M("user")->where("id=" . $id)->getField("plat");
					$cec = M("user")->where(array("phone" => $phone))->count();
					if ($cec) {
						$status = array("status" => 0, "msg" => "该手机号已注册");
					} else {
						$level = M("bus_level_manage")->where(array("plat" => $plat_id, "nums" => 1))->getField("englishname");
						$data = array(
							"phone" => $phone,
							"busname" => $params['name'],
							"pwd" => md5(trim($params['password'])),
							"regisTime" => time(),
							"level" => $level,
							"code" => substr($phone, 3) . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT),
							"status" => 1
						);
						$data['parent'] = $id;
						$data['plat'] = $plat_id;
						$add = M("user")->data($data)->add();
						if ($add) {
							$this->addBusAudit($add);
							session("code_" . $phone, "");
							$status = array("status" => 1, "msg" => "注册成功", "url" => U("Register/success"));
						} else {
							$status = array("status" => 0, "msg" => "注册失败");
						}
					}
				}
			}
		}
		echo parent::json_encode_ex($status);
	}
	/**
	 * 添加审核
	 * @date: 2017年11月28日 上午10:56:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected function addBusAudit($bid)
	{
		$data = array(
			"bid" => $bid,
			"status" => 3, "createTime" => time()
		);
		M("cer_audit")->data($data)->add();
	}
	public function success()
	{
		$this->display();
	}
	/**
	 * 发送注册手机验证码o
	 * @date: 2017年6月5日 下午5:08:37
	 * @param: variable
	 * @return:
	 */
	public function sendVerify()
	{
		$flag = true;
		$params = I("post.");
		if (checkParams($params, array(
			"phone",
			"sendType", "resType", "id"
		))) {
			$id = $params['id'];
			$plat = M('user')->where("id=" . $id)->getField("plat");
			//file_put_contents("./kdlkdsjlljdsljfdljsljfls",$id);
			$phone = trim($params['phone']);
			$expire = session("expire_" . $phone);
			$sendType = trim($params['sendType']);
			if (isset($expire) && (time() - $expire) < 60) {
				$flag = false;
				$message = "验证码已发送不能重复";
			} else {
				$verify = rand(1000, 9999);
				$result = parent::phoneVerifyCode($phone, $verify, $sendType, $plat);
				session("code_" . $phone, $verify);
				session("expire_" . $phone, time());
				if (!$result) {
					$flag = false;
					$message = "验证码发送失败";
				}
			}
		} else {
			$flag = false;
			$message = "缺少参数";
		}
		if ($flag) {
			$status = array(
				"status" => 1,
				"msg" => "验证码发送成功"
			);
		} else {
			$status = array(
				"status" => 0,
				"msg" => $message
			);
		}
		echo parent::json_encode_ex($status);
	}
	/**
	 * 介绍页
	 * @date: 2018年3月6日 上午11:11:36
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	public function introduce()
	{
		if (!IS_GET) {
			return false;
		} else {
			$code = I("get.code");
			$de = self::decode("" . $code);
			$ex = @explode(",", $de);
			if ($ex) {
				foreach ($ex as $val) {
					$ex1 = @explode(":", $val);
					switch ($ex1[0]) {
						case "bid":
							$bid = $ex1[1];
							break;
						case "resType":
							$resType = $ex1[1];
							break;
						default:
							//404
							break;
					}
				}
				if (!empty($bid)) {
					$plat = M("user")->where(array("id" => $bid))->getField("plat");
					if ($plat) {
						if (cRec("regis_pic_manage", array("key" => "key_" . $plat))) {
							$plat = $plat;
						} else {
							$plat = 1;
						}
						$introduce = $this->get_picurl($plat, "value");
						$this->assignAll(["code" => $code, "introduce" => $introduce]);
						$this->display();
					} else {
						$this->showImg();
					}
				} else {
					$this->showImg();
				}
			} else {
				$this->showImg();
			}
		}
	}
	private function get_picurl($plat, $field)
	{
		return M("regis_pic_manage")->where(array("key" => "key_" . $plat))->getField($field);
	}
}