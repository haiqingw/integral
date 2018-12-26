<?php
namespace Home\Controller;
use Think\Controller;
class CenterController extends CommonController{
	public function bindCard(){
		$row = fRec("business","id=" . $this->mid,"name");
		$field = array(
			"bank_name bankName", 
			"card_number cardType", 
			"concat('**** **** **** ' ,right(card_number,4)) bankCard"
		);
		$info = M("bankcard_list")->field($field)->where("bid=" . $this->mid . " and status=1")->find();
		if($info){
			$info['cardType'] = parent::checkCard($info['cardType']);
			$status = array(
				"status" => 1, 
				"data" => $info
			);
		}else{
			$status = array(
				"status" => 0, 
				"msg" => "未绑卡"
			);
		}
		$this->assign("cardStatus",$status);
		$this->assign("name",$row['name']);
		$this->display();
	}
	/**
	 * 发送验证信息
	 */
	public function sendVerifyMessage(){
		$flag = true;
		$params = I("post.");
		if(checkParams($params,array(
			"phone"
		))){
			$platformID = parent::getPid();
			$phone = trim($params['phone']);
			$expire = session("bindexpire_" . $phone);
			if(isset($expire) && (time() - $expire) < 60){
				$flag = false;
				$message = "验证码已发送不能重复";
			}else{
				$verify = rand(1000,9999);
				$result = parent::phoneVerifyCode($phone,$verify,"rgpartner",$platformID);
				session("bindcode_" . $phone,$verify);
				session("bindexpire_" . $phone,time());
				if(!$result){
					$flag = false;
					$message = "验证码发送失败";
				}
			}
		}else{
			$flag = false;
			$message = "缺少参数";
		}
		if($flag){
			$status = array(
				"status" => 1, 
				"msg" => "验证码发送成功"
			);
		}else{
			$status = array(
				"status" => 0, 
				"msg" => $message
			);
		}
		echo json_encode($status);
	}
	public function getBName(){
		if(IS_AJAX){
			$code = I("post.code");
			$status = array(
				"status" => 0
			);
			if(!empty($code)){
				$name = parent::checkCard($code,"bank");
				if($name){
					$status['status'] = 1;
					$status['msg'] = $name;
					$status['code'] = $code;
				}
			}
			echo json_encode($status);
		}
	}
	/**
	 * 添加银行卡
	 * @date: 2017年6月7日 下午2:50:08
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public function doBankCardInfo(){
		$flag = true;
		$params = I("post.");
		if(parent::checkParams($params,array(
			"phone", 
			"cardNum", 
			"bankName", 
			"verify", 
			"idCard"
		))){
			$bid = $this->mid;
			$phone = trim($params['phone']);
			$row = fRec("business","id=" . $bid,"name,platform_id");
			$pid = $row['platform_id'];
			//验证码
			$verifyCode = session("bindcode_" . $phone);
			$expire = session("bindexpire_" . $phone);
			if(!isset($expire) || (time() - $expire) > 60){
				session("bindexpire_" . $phone,null);
				session("bindcode_" . $phone,null);
				$flag = false;
				$message = "验证码已失效请重新发送";
			}else{
				if($params['verify'] != $verifyCode){
					$flag = false;
					$message = "验证码错误";
				}else{
					$cc = array(
						"bid" => $bid, 
						"pid" => $pid, 
						"accountNo" => $params['cardNum'], 
						"bankPreMobile" => $phone, 
						"idCard" => $params['idCard']
					);
					$checkCard = A("CheckBankCark");
					$result = $checkCard->VerifyBankCardInfo($cc);
					if($result['status'] == 1){
						if(cRec("bankcard_list",array(
							"bid" => $bid, 
							"card_number" => trim($params['cardNum']), 
							"status" => 1
						))){
							$flag = false;
							$message = "卡号已存在不能重复";
						}else{
							$name = $row['name'];
							$data = array(
								"bid" => $bid, 
								"card_number" => $params['cardNum'], 
								"city" => "中国大陆", 
								"opening_bank" => "中国银行新华东街支行", 
								"name" => $name, 
								"phoneNum" => $phone, 
								"bank_name" => $params['bankName'], 
								"addtime" => time(), 
								"platform_id" => $pid
							);
							if(!aRec("bankcard_list",$data)){
								$flag = false;
								$message = "添加失败";
							}
						}
					}else{
						$flag = false;
						$message = $result['msg'];
					}
				}
			}
		}else{
			$flag = false;
			$message = "信息不完整";
		}
		if($flag){
			uRec("business","idCard=" . $params['idCard'],"id=" . $bid);
			$status = array(
				"status" => 1, 
				"msg" => "保存成功"
			);
		}else{
			$status = array(
				"status" => 0, 
				'msg' => $message
			);
		}
		echo json_encode($status);
	}
	public function myQrcode(){
		$qr = A("Qrcode");
		$this->assign("img",$qr->index());
		$this->display();
	}
	public function logout(){
		session('mid',null);
		$this->redirect("Index/index");
	}
	public function index(){
		$info = getVal("hanyin");
		$this->assign("info",$info);
		$bid = $this->mid;
		$balance = self::busBalance($bid);
		$this->assign("balance",$balance);
		$sumSy = M('changes_funds')->where(array(
				"bid" => $bid,
				"status" => "Y",
				"changeType" => "P"
		))->sum("changeAmount");
		$this->assign("qun",getVal('qunQrcode'));
		$this->assign("balance",parent::subDecimals($balance));
		$this->assign("sumSy",parent::subDecimals(empty($sumSy)?'0':$sumSy));
		$this->display();
	}
	/**
	 *钱包明细
	 * @date: 2017年6月16日 下午6:13:26
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 type = income payType = all  全部收益
	 type = drawcash payType = all  全部提现
	 type = income payType = bind 绑定收益
	 type = income payType = agent 升级代理收益
	 type = income payType = trade 交易收益
	 */
	public function CommissionRecord(){
		$bid = $this->mid;
		$type = I("get.type");
		if(empty($type)){
			$type = "P";
		}
		$this->assign("type",$type);
		$page = I("get.page");
		if(empty($page)){
			$page = 1;
		}
		$limit = 20;
		$offset = ($page - 1) * $limit;
		$field = array(
			"remark", 
			"ordernum nm", 
			//"case changeType when 'P' then (select concat(substring(name,1,1),'*',substring(name,-1,1)) name from e_business where id = (SELECT outputAN FROM `e_cash_back_log` where uniqueIndex = nm and receiveAN = ".$bid.")) when 'T' then '' end fxName", 
			"case changeType when 'P' then (select agentLevel FROM `e_cash_back_log` where uniqueIndex = nm and receiveAN = ".$bid." limit 1) when 'T' then '1' end level", 
			//"case changeType when 'P' then (select payMoney FROM `e_hy_tradelist` where orderNum = nm) when 'T' then '' end pmoney", 
			"changeAmount", 
			"changeType", 
			"case changeType when 'P' then '交易返现' when 'T' then '提现' end typeName", 
			"FROM_UNIXTIME(createTime,'%Y/%m/%d %H:%i:%s') createTime"
		);
		$where = array(
			"bid" => $bid, 
			"status" => "Y", 
			"changeType" => $type
		);
		$counts = M('changes_funds')->field($field)->where($where)->count();
		$pageCount = ceil($counts/$limit);
		if($page <= 1){
			$prev = "javascript:;";
		}else{
			$p .= $page - 1;
			$prev = "?type=".$type."&page=".$p;
		}
		if($page < $pageCount){
			$p = $page + 1;
			$next = "?type=".$type."&page=".$p;
		}else{
			$next = "javascript:;";
		}
		$this->assign("prev",$prev);
		$this->assign("next",$next);
		$res = M('changes_funds')->field($field)->where($where)->order("createTime DESC")->limit($limit.",".$offset)->select();
		$this->assign("list",$res);
		$balance = self::busBalance($bid);
		$this->assign("balance",$balance);
		$sumSy = M('changes_funds')->where(array(
			"bid" => $bid, 
			"status" => "Y", 
			"changeType" => "P"
		))->sum("changeAmount");
		$this->assign("balance",parent::subDecimals($balance));
		$this->assign("sumSy",parent::subDecimals(empty($sumSy)?'0':$sumSy));
		$this->display();
	}
	public function getRecordList(){
		$params = I('post.');
		if(IS_AJAX){
			$ret = array(
				"status" => 0
			);
			$bid = $this->mid;
			$page = $params['page'];
			$type = $params['type'];
			if(!empty($page) && !empty($type)){
				$limit = 10;
				$offset = ($page - 1) * $limit;
				$field = array(
					"remark", 
					"changeAmount", 
					"changeType", 
					"case changeType when 'P' then '交易返现' when 'T' then '提现' end typeName", 
					"FROM_UNIXTIME(createTime,'%Y/%m/%d %H:%i:%s') createTime"
				);
				$where = array(
					"bid" => $bid, 
					"status" => "Y", 
					"changeType" => $type
				);
				$res = M('changes_funds')->field($field)->where($where)->limit("{$offset},{$limit}")->order("createTime DESC")->select();
				if($res){
					$ret = array(
						"status" => 1, 
						"data" => $res
					);
				}
			}
			echo json_encode($ret);
		}
	}
	public function withdraw(){
		$bid = $this->mid;
		$field = array(
			"id",
			"name", 
			"concat(bank_name,'(尾号',right(card_number,4),')') bankCard"
		);
		$info = M("bankcard_list")->field($field)->where("bid=" . $this->mid . " and status=1")->find();
		if(!$info){
			$this->redirect("Center/bindCard");
		}else{
			$this->assign("info",$info);
			$balance = self::busBalance($bid);
			$this->assign("balance",$balance);
			$this->display();
		}
	}
	public function withdrawSuccess(){
		$g = I('get.');
		if(empty($g['mn']) || empty($g['bk'])){
			$this->redirect("Center/index");
		}else{
			$this->assign("g",$g);
			$this->display();
		}
	}
	/**
	 * 商户余额
	 * @date: 2017年6月15日 下午3:26:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	private static function busBalance($bid, $type = "trade"){
		$money = 0;
		if(!empty($bid)){
			$row = fRec("important","bus_id=" . $bid . "||payType=" . $type,"total_amount");
			return RSAcode($row['total_amount'],"DE") ? RSAcode($row['total_amount'],"DE") : 0;
		}
		return false;
	}
	public function useDesc(){
		$info = getVal("about");
		$this->assign("info",$info);
		$this->display();
	}
	public function team(){
		$bid = $this->mid;
		$this->assign("oneCts",$this->getTeamOneLevel($bid, 'ct'));
		$this->assign("twoCts",$this->getTeamTwoLevel($bid, 'ct'));
		$this->assign("threeCts",$this->getTeamThreeLevel($bid, 'ct'));
		$this->display();
	}
	public function teamLevelOne(){
		$bid = $this->mid;
		$data = $this->getTeamOneLevel($bid, 'dt');
		$this->assign("oneCts",$this->getTeamOneLevel($bid, 'ct'));
		$this->assign("result",json_encode($data));
		$this->display();
	}
	public function teamLevelTwo(){
		$bid = $this->mid;
		$data = $this->getTeamTwoLevel($bid, 'dt');
		$this->assign("twoCts",$this->getTeamTwoLevel($bid, 'ct'));
		$this->assign("result",json_encode($data));
		$this->display();
	}
	public function teamLevelThree(){
		$bid = $this->mid;
		$data = $this->getTeamThreeLevel($bid, 'dt');
		$this->assign("threeCts",$this->getTeamThreeLevel($bid, 'ct'));
		$this->assign("result",json_encode($data));
		$this->display();
	}
	protected function getTeamOneLevel($id,$type){
		$where = "parentId=".$id."||status=1";
		if($type == "ct"){
			return cRec("business",$where);
		}elseif($type == "dt"){
			return sRec("business",$where,"regTime desc","","","concat(substring(name,1,1),'*',substring(name,-1,1)) teamName,concat(substring(phone,1,3),'****',substring(phone,-4,4)) teamTelPhone,phone truePhone,(select count(*) from e_hy_merchantlist where bid = e_business.id) + (select count(*) from e_hy_merchantlist where shopName = e_business.name and phone = teamTelPhone) isReg");
		}else{
			return false;
		}
	}
	protected function getTeamTwoLevel($id,$type){
		$where = array("_string"=>"status = 1 and parentId in (select id from e_business where parentId = ".$id.")");
		if($type == "ct"){
			return cRec("business",$where);
		}elseif($type == "dt"){
			return sRec("business",$where,"regTime desc","","","concat(substring(name,1,1),'*',substring(name,-1,1)) teamName,concat(substring(phone,1,3),'****',substring(phone,-4,4)) teamTelPhone");
		}else{
			return false;
		}
	}
	protected function getTeamThreeLevel($id,$type){
		$where = array("_string"=>"status = 1 and parentId in (select id from e_business where parentId in (select id from e_business where parentId = ".$id."))");
		if($type == "ct"){
			return cRec("business",$where);
		}elseif($type == "dt"){
			return sRec("business",$where,"regTime desc","","","concat(substring(name,1,1),'*',substring(name,-1,1)) teamName,concat(substring(phone,1,3),'****',substring(phone,-4,4)) teamTelPhone");
		}else{
			return false;
		}
	}
	public function settingPassword(){
		$bid = $this->mid;
		$phone = M('business')->where('id='.$bid)->getField("phone");
		$this->assign("phone",$phone);
		$this->display();
	}
	public function sendChangePassVerifyMessage(){
		$flag = true;
		$params = I("post.");
		if(checkParams($params,array(
				"phone"
		))){
			$platformID = parent::getPid();
			$phone = trim($params['phone']);
			$expire = session("cpexpire_" . $phone);
			if(isset($expire) && (time() - $expire) < 60){
				$flag = false;
				$message = "验证码已发送不能重复";
			}else{
				$info = M("business")->field("id,status")->where("phone=" . $phone . " and status = 1 and platform_id=" . $platformID)->find();
				if($info){
					$verify = rand(1000,9999);
					$result = parent::phoneVerifyCode($phone,$verify,"rgpartner",$platformID);
					session("cpcode_" . $phone,$verify);
					session("cpexpire_" . $phone,time());
					if(!$result){
						$flag = false;
						$message = "验证码发送失败";
					}
				}else{
					$flag = false;
					$message = "您的账号尚未注册";
				}
			}
		}else{
			$flag = false;
			$message = "缺少参数";
		}
		if($flag){
			$status = array(
					"status" => 1,
					"msg" => "验证码发送成功"
			);
		}else{
			$status = array(
					"status" => 0,
					"msg" => $message
			);
		}
		echo json_encode($status);
	}
	public function AccountChangePass(){
		if(IS_AJAX){
			$flag = true;
			$params = I("post.");
			$phone = trim($params['phone']);
			$code = trim($params['verify']);
			$pass = trim($params['pass']);
			if(empty($phone) || empty($code) || empty($pass)){
				$flag = false;
				$message = "非法参数";
			}else{
				$verifyCode = session("cpcode_" . $phone);
				$expire = session("cpexpire_" . $phone);
				if(!isset($expire) || (time() - $expire) > 60){
					session("cpexpire_" . $phone,null);
					session("cpcode_" . $phone,null);
					$flag = false;
					$message = "验证码已失效请重新发送";
				}else{
					if($code != $verifyCode){
						$flag = false;
						$message = "验证码错误";
					}else{
						if(!uRec("business",array("password"=>md5($pass)),array(
								"phone" => $phone,
								"status" => 1
						))){
							$flag = false;
							$message = "修改失败，新旧密码不能一致";
						}
					}
				}
			}
			if($flag){
				$status = array(
						"status" => 1,
						"msg" => "修改成功"
				);
			}else{
				$status = array(
						"status" => 0,
						"msg" => $message
				);
			}
			echo json_encode($status);
		}else{
			return false;
		}
	}
}
?>
