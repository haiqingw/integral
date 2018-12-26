<?php
namespace Common\Api;
/**
 * +----------------------------------------------------------------------
 * | 短信验证码发送接口
 * +----------------------------------------------------------------------
 * | @author HaiQing.Wu <398994668@qq.com>
 * +----------------------------------------------------------------------
 * | last Time : 2016/09/20
 * +----------------------------------------------------------------------
 */
header("Content-Type: text/html; charset=UTF-8");
define("SUBMIT_URL","http://web.cr6868.com/asmx/smsservice.aspx"); //提交地址
define("USER_ACCOUNT","18748106798"); //用户账号
define("INTERFACE_PASSWORD","41F6253C5F416D2D35406DD778AA"); //接口密码
define("SMST_T","smstemplates"); //短信模板管理表
define("SMSR_T","smsrecord"); //短信记录表
class Message{
	protected $model;
	protected $sendSuccess;
	protected $sendFailed;
	public function __construct(){
		$this->sendSuccess = "发送成功";
		$this->sendFailed = "发送失败";
		$this->model = new \Think\Model();
	}
	/**
	 * 手机验证码发送(公共)
	 * @param string $mobile    手机号码
	 * @param int $verify	    验证码
	 * @param string  $sendType 发送类型
	 * @return boolean
	 */
	public function SendVerifyCode($mobile, $verify, $sendType, $pid){
		$flag = true;
		if(empty($mobile) || empty($verify) || empty($sendType)){
			$flag = false;
		}else{
			$mobile = trim($mobile);
			$checkTemplate = self::getTemplate($sendType);
			if(!$checkTemplate){
				$flag = false;
			}else{
				//可选参数，扩展码，用户定义扩展码，只能为数字
				$content = preg_replace("/{\D}/",$verify,$checkTemplate['smstemplatesContent']);
				$i = 0;
				$params = ''; //要post的数据
				$argv = array(
					'name' => USER_ACCOUNT,  //必填参数。用户账号
					'pwd' => INTERFACE_PASSWORD,  //必填参数。（web平台：基本资料中的接口密码）
					'content' => $content,  //必填参数。发送内容（1-500 个汉字）UTF-8编码
					'mobile' => $mobile,  //必填参数。手机号码。多个以英文逗号隔开
					'stime' => '',  //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
					'sign' => '',  //必填参数。用户签名。
					'type' => 'pt',  //必填参数。固定值 pt
					'extno' => ''
				);
				foreach($argv as $key => $value){
					if($i != 0){
						$params .= "&";
						$i = 1;
					}
					$params .= $key . "=";
					$params .= urlencode($value); // urlencode($value);
					$i = 1;
				}
				$url = SUBMIT_URL . "?" . $params; //提交的url地址
				$con = substr(file_get_contents($url),0,1); //获取信息发送后的状态
				if($con != '0'){
					$flag = false;
				}
			}
		}
		$data['SmsRecordMobile'] = $mobile;
		$data['SmsRecordSentTime'] = date("Y-m-d H:i:s");
		$data['platform_id'] = $pid;
		$data['SmsRecordSendType'] = $sendType;
		if($flag){
			$data['SmsRecordStatus'] = $this->sendSuccess;
			$data['SmsRecordContent'] = $content;
		}else{
			$data['SmsRecordStatus'] = $this->sendFailed;
			$data['SmsRecordContent'] = "随机验证：[" . $verify . " ]发送失败";
		}
		aRec(SMSR_T,$data);
		return $flag;
	}
	/**
	 * 获取模板信息
	 * @param string $type 模板类型
	 */
	private static function getTemplate($type = "phone"){
		$flag = true;
		if(empty($type)){
			$flag = false;
		}else{
			$res = fRec(SMST_T,array(
				"smstemplatesType" => $type
			));
			if(!$res){
				$flag = false;
			}
		}
		if($flag){
			return $res;
		}else{
			return false;
		}
	}
}
