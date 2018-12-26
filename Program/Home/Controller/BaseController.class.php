<?php
namespace Home\Controller;
use Think\Controller;
define("S_KEY","sjfwporwejclamlmerew");
class BaseController extends Controller{
	/**
	 * 对变量进行 JSON 编码
	 * @param mixed value 待编码的 value ，除了resource 类型之外，可以为任何数据类型，该函数只能接受 UTF-8 编码的数据
	 * @return string 返回 value 值的 JSON 形式
	 */
	protected static function json_encode_ex($value){
		if(version_compare(PHP_VERSION,'5.4.0','<')){
			$str = json_encode($value);
			$str = preg_replace_callback("#\\\u([0-9a-f]{4})#i",function ($matchs){
				return iconv('UCS-2BE','UTF-8',pack('H4',$matchs[1]));
			},$str);
			return $str;
		}else{
			return json_encode($value,JSON_UNESCAPED_UNICODE);
		}
	}
	/**
	 * 验证手机号是否正确
	 * @param number $mobile
	 */
	protected static function isMobile($mobile){
		if(!is_numeric($mobile)){
			return false;
		}
		return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#',$mobile) ? true : false;
	}
	public static function getPid(){
		$pid = session('pid');
		if(empty($pid)){
			$pid = 3;
		}
		return $pid;
	}
	public static function getTid(){
		$pid = session('tid');
		if(empty($pid)){
			return false;
		}
		return $pid;
	}
	/**
	 * 验证提现时间
	 * @date: 2017年6月30日 上午9:52:28
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $storageType  返现类型
	 * @return:
	 */
	protected static function checkTimeToOffer($storageType, $pid){
		if($storageType){
			switch($storageType){
				case 'bind':
					//推荐绑定返现
					$bindCheckDrawTime = self::drawCaltStat("jjjhkg",$pid);
					if($bindCheckDrawTime == 'yj'){
						$status = array(
							"status" => 1, 
							//日期一位数加0 2017-01-01
							"drawTime" => self::drawCaltStat("jjjhjs",$pid)
						); //str_pad(getVal("jjjhjs"),2,0,STR_PAD_LEFT)
					}
					if($bindCheckDrawTime == 'mj'){
						$status['status'] = 2;
					}
					break;
				case 'agent':
					//升级代理商返现
					$agentCheckDrawTime = self::drawCaltStat("sjdlskg",$pid);
					if($agentCheckDrawTime == 'yj'){
						$status = array(
							"status" => 1, 
							"drawTime" => self::drawCaltStat("sjdlsjs",$pid)
						); //str_pad(getVal("sjdlsjs"),2,0,STR_PAD_LEFT)
					}
					if($agentCheckDrawTime == 'mj'){
						$status['status'] = 2;
					}
					break;
				case 'trade':
					//POS机交易返现
					$tradeCheckDrawTime = self::drawCaltStat("jykg",$pid);
					if($tradeCheckDrawTime == 'yj'){
						$status = array(
							"status" => 1, 
							"drawTime" => self::drawCaltStat("jyjs",$pid)
						); //str_pad(getVal("jyjs"),2,0,STR_PAD_LEFT)
					}
					if($tradeCheckDrawTime == 'mj'){
						$status['status'] = 2;
					}
					break;
			}
			return $status;
		}
		return false;
	}
	/**
	 * 截取小数位
	 */
	protected static function subDecimals($decimals, $number = 2){
		$ex = @explode(".",$decimals);
		if(strlen($ex[1]) > $number){
			$c = substr($ex[1],0,$number);
			return $ex[0] . "." . $c;
		}else{
			return $decimals;
		}
	}
	/**
	 * 获取关闭详情
	 * @param string $type
	 * @param string $v
	 * @return true 关闭 false 不关闭
	 * $this->getCloseInfo('pay','wx|zfb|yl')
	 * $this->getCloseInfo('paid','all')
	 */
	protected static function getCloseInfo($type, $v){
		if(empty($type) || empty($v)){
			return false;
		}
		$week = getdate();
		$day = $week['wday'];
		$info = unserialize(self::getVal('week'));
		if($info){
			for($i = 0;$i < count($info);$i++){
				if($info[$i]['day'] == $day && !empty($info[$i][$type]) && strstr($info[$i][$type],$v) != false){
					return true;
				}
			}
		}
		return false;
	}
	/**
	 * 获取val
	 * @param string $key
	 */
	protected static function getVal($key = ""){
		$row = fRec('system','system_key=' . $key,'system_val val');
		if($row){
			return $row['val'];
		}
		return "";
	}
	/**
	 * 获取帮助详情
	 * @param unknown $type
	 */
	protected static function getHelpDetail($id){
		$val = fRec(T_HCEN,"e_h_id=" . $id,"e_h_title title,e_h_content content,FROM_UNIXTIME(e_h_times,'%Y/%m/%d %H:%i:%s') addTime");
		if($val){
			return $val;
		}
		return false;
	}
	/**
	 * 获取其他信息表信息
	 * @param unknown $type
	 */
	protected static function getSystem($type){
		$val = fRec(T_SYS,'system_key=' . $type,'system_val val');
		if($val){
			return $val['val'];
		}
		return false;
	}
	/**
	 * 可提现计算方式
	 * @date: 2017年8月1日 下午2:48:59
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $key 字段
	 * @param: $pid 平台ID
	 * @return:
	 */
	protected static function drawCaltStat($key, $pid){
		$val = M("back_level")->where(array(
			"platform_id" => $pid
		))->getField($key);
		if($val){
			return $val;
		}
		return "";
	}
	/**
	 * 获取商户ID
	 * @date: 2017年8月1日 下午2:39:37
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $where 获取BID条件
	 * @return int
	 */
	protected static function busID($where){
		if(!empty($where)){
			$id = M(T_BUS)->where($where)->getField("id");
			if($id){
				return $id;
			}
		}
		return "";
	}
	/**
	 * 验证 参数 bid 用户 
	 * @date: 2017年6月12日 上午9:07:19
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $phone 登录账号
	 * @param: $bid   商户ID
	 * @param: $pid   平台ID 
	 * @return:
	 */
	public static function check($phone, $bid, $pid){
		if(!empty($phone) && !empty($bid) && !empty($pid)){
			$where = array(
				"phone" => $phone, 
				"platform_id" => $pid
			);
			$id = M("business")->where($where)->getField("id");
			if($bid == md5($id)){
				return true;
			}
		}
		return false;
	}
	/**
	 * 获取字段值
	 * @date: 2017年6月12日 上午9:11:04
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public static function getValue($table, $colum, $value, $field){
		return M($table)->where($colum . "=" . $value)->getField($field);
	}
	/**
	 * 银行卡号 每四位 空格 分开
	 * @date: 2017年3月13日 上午11:05:59
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $card 银行卡号
	 * @return:
	 */
	public static function subBankCard($card){
		$p = preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{4})([\d]{0,})?/',$card,$match);
		if(!$p){
			preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{0,})?/',$card,$match);
		}
		unset($match[0]);
		return implode(" ",$match);
	}
	/**
	 * 函数用途描述
	 * @date: 2017年6月8日 下午4:16:35
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public static function replCard($card){
		return "**** **** **** " . array_pop(explode(" ",self::subBankCard($card)));
	}
	/**
	 * 检测元素是否正确
	 * 只能检测一维数组
	 * @param $arr 被检测的数组
	 * @param $repar $arr中必要的元素(键名)
	 * @return boolean
	 */
	protected static function checkParams($arr, $repar = array()){
		if(!is_array($arr)){
			return false;
		}
		if(!count($arr)){
			return false;
		}
		// 判断每个元素是否存在
		if(count($repar)){
			foreach($repar as $val){
				if(!in_array($val,array_keys($arr))){
					return false;
					break;
				}
			}
		}
		// 判断每个元素的值是否正确
		foreach($arr as $val){
			if($val == "" || $val == false || $val == null || $val == "undefined" || $val == "null"){
				return false;
				break;
			}
		}
		return true;
	}
	/**
	 * 加密
	 * @param String $string 需要加密的字串
	 * @param String $skey 加密EKY
	 * @return String
	 */
	protected static function encode($string = '', $skey = S_KEY){
		$strArr = str_split(base64_encode($string));
		$strCount = count($strArr);
		foreach(str_split($skey) as $key => $value)
			$key < $strCount && $strArr[$key] .= $value;
		return str_replace(array(
			'=', 
			'+', 
			'/'
		),array(
			'O0O0O', 
			'o000o', 
			'oo00o'
		),join('',$strArr));
	}
	/**
	 * 解密
	 * @param String $string 需要解密的字串
	 * @param String $skey 解密KEY
	 * @return String
	 */
	protected static function decode($string = '', $skey = S_KEY){
		$strArr = str_split(str_replace(array(
			'O0O0O', 
			'o000o', 
			'oo00o'
		),array(
			'=', 
			'+', 
			'/'
		),$string),2);
		$strCount = count($strArr);
		foreach(str_split($skey) as $key => $value)
			$key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
		return base64_decode(join('',$strArr));
	}
	/**
	 * 发送手机验证码
	 * @date: 2017年6月5日 下午3:34:23
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: 
	 * @return: string
	 */
	protected static function phoneVerifyCode($mobile, $verify, $sendType, $pid = 3){
		if(!empty($mobile) && !empty($verify) && !empty($sendType)){
			$object = new \Common\Api\Message();
			$send = $object->SendVerifyCode($mobile,$verify,$sendType,$pid);
			if($send){
				return true;
			}
		}
		return false;
	}
	/**
	 * 根据代码获取银行名称
	 * @param string $type str 字符串 num 数字
	 */
	protected static function getBankName($code, $type = "str"){
		if($type == "str"){
			$bankNames = json_decode('{"CDB":"国家开发银行","ICBC":"中国工商银行","ABC":"中国农业银行","BOC":"中国银行","CCB":"中国建设银行","PSBC":"中国邮政储蓄银行","COMM":"交通银行","CMB":"招商银行","SPDB":"上海浦东发展银行","CIB":"兴业银行","HXBANK":"华夏银行","GDB":"广东发展银行","CMBC":"中国民生银行","CITIC":"中信银行","CEB":"中国光大银行","EGBANK":"恒丰银行","CZBANK":"浙商银行","BOHAIB":"渤海银行","SPABANK":"平安银行","HKBEA":"东亚银行","HANABANK":"韩亚银行","SHRCB":"上海农村商业银行","YXCCB":"玉溪市商业银行","YDRCB":"尧都农商行","BJBANK":"北京银行","SHBANK":"上海银行","JSBANK":"江苏银行","HZCB":"杭州银行","NJCB":"南京银行","NBBANK":"宁波银行","HSBANK":"徽商银行","CSCB":"长沙银行","CDCB":"成都银行","CQBANK":"重庆银行","DLB":"大连银行","NCB":"南昌银行","FJHXBC":"福建海峡银行","HKB":"汉口银行","WZCB":"温州银行","QDCCB":"青岛银行","TZCB":"台州银行","JXBANK":"嘉兴银行","CSRCB":"常熟农村商业银行","NHB":"南海农村信用联社","CZRCB":"常州农村信用联社","H3CB":"内蒙古银行","SXCB":"绍兴银行","SDEB":"顺德农商银行","WJRCB":"吴江农商银行","ZBCB":"齐商银行","GYCB":"贵阳市商业银行","ZYCBANK":"遵义市商业银行","HZCCB":"湖州市商业银行","DAQINGB":"龙江银行","JINCHB":"晋城银行JCBANK","ZJTLCB":"浙江泰隆商业银行","GDRCC":"广东省农村信用社联合社","DRCBCL":"东莞农村商业银行","MTBANK":"浙江民泰商业银行","GCB":"广州银行","LYCB":"辽阳市商业银行","JSRCU":"江苏省农村信用联合社","LANGFB":"廊坊银行","CZCB":"浙江稠州商业银行","DYCB":"德阳商业银行","JZBANK":"晋中市商业银行","BOSZ":"苏州银行","GLBANK":"桂林银行","URMQCCB":"乌鲁木齐市商业银行","CDRCB":"成都农商银行","ZRCBANK":"张家港农村商业银行","BOD":"东莞银行","LSBANK":"莱商银行","BJRCB":"北京农村商业银行","TRCB":"天津农商银行","SRBANK":"上饶银行","FDB":"富滇银行","CRCBANK":"重庆农村商业银行","ASCB":"鞍山银行","NXBANK":"宁夏银行","BHB":"河北银行","HRXJB":"华融湘江银行","ZGCCB":"自贡市商业银行","YNRCC":"云南省农村信用社","JLBANK":"吉林银行","DYCCB":"东营市商业银行","KLB":"昆仑银行","ORBANK":"鄂尔多斯银行","XTB":"邢台银行","JSB":"晋商银行","TCCB":"天津银行","BOYK":"营口银行","JLRCU":"吉林农信","SDRCU":"山东农信","XABANK":"西安银行","HBRCU":"河北省农村信用社","NXRCU":"宁夏黄河农村商业银行","GZRCU":"贵州省农村信用社","FXCB":"阜新银行","HBHSBANK":"湖北银行黄石分行","ZJNX":"浙江省农村信用社联合社","XXBANK":"新乡银行","HBYCBANK":"湖北银行宜昌分行","LSCCB":"乐山市商业银行","TCRCB":"江苏太仓农村商业银行","BZMD":"驻马店银行","GZB":"赣州银行","WRCB":"无锡农村商业银行","BGB":"广西北部湾银行","GRCB":"广州农商银行","JRCB":"江苏江阴农村商业银行","BOP":"平顶山银行","TACCB":"泰安市商业银行","CGNB":"南充市商业银行","CCQTGB":"重庆三峡银行","XLBANK":"中山小榄村镇银行","HDBANK":"邯郸银行","KORLABANK":"库尔勒市商业银行","BOJZ":"锦州银行","QLBANK":"齐鲁银行","BOQH":"青海银行","YQCCB":"阳泉银行","SJBANK":"盛京银行","FSCB":"抚顺银行","ZZBANK":"郑州银行","SRCB":"深圳农村商业银行","BANKWF":"潍坊银行","JJBANK":"九江银行","JXRCU":"江西省农村信用","HNRCU":"河南省农村信用","GSRCU":"甘肃省农村信用","SCRCU":"四川省农村信用","GXRCU":"广西省农村信用","SXRCCU":"陕西信合","WHRCB":"武汉农村商业银行","YBCCB":"宜宾市商业银行","KSRB":"昆山农村商业银行","SZSBK":"石嘴山银行","HSBK":"衡水银行","XYBANK":"信阳银行","NBYZ":"鄞州银行","ZJKCCB":"张家口市商业银行","XCYH":"许昌银行","JNBANK":"济宁银行","CBKF":"开封市商业银行","WHCCB":"威海市商业银行","HBC":"湖北银行","BOCD":"承德银行","BODD":"丹东银行","JHBANK":"金华银行","BOCY":"朝阳银行","LSBC":"临商银行","BSB":"包商银行","LZYH":"兰州银行","BOZK":"周口银行","DZBANK":"德州银行","SCCB":"三门峡银行","AYCB":"安阳银行","ARCU":"安徽省农村信用社","HURCB":"湖北省农村信用社","HNRCC":"湖南省农村信用社","NYNB":"广东南粤银行","LYBANK":"洛阳银行","NHQS":"农信银清算中心","CBBQS":"城市商业银行资金清算中心"}',true);
			$bankName = $bankNames[$code];
			if($bankName){
				return $bankName;
			}
			return "未知";
		}else{
			$json = substr(file_get_contents("./Public/js/bankData.js"),12);
			$result = json_decode($json,true);
			//dump($result);
			for($i = 10;$i >= 2;$i--){
				$code = substr($code,0,$i);
				foreach($result as $key => $val){
					if($val['bin'] == $code){
						$bankName = $val['bankName'];
						break;
					}
				}
			}
			if($bankName){
				return $bankName;
			}
			return false;
		}
	}
	/**
	 * 获取银行卡类型
	 * @date: 2017年6月8日 下午5:36:10
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $card 银行卡号
	 * @return:
	 */
	public static function checkCard($card, $type = 'cardType'){
		$url = "https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=" . $card . "&cardBinCheck=true";
		$response = json_decode(curlRequest($url),true);
		if($response){
			if($type == 'cardType'){
				return self::getCardType($response['cardType']);
			}elseif($type == 'bank'){
				return self::getBankName($response['bank']);
			}else{
				return false;
			}
		}
		return null;
	}
	/**
	 * 根据代码获取卡类型
	 */
	protected static function getCardType($type){
		switch($type){
			case "DC":
				return "储蓄卡";
				break;
			case "CC":
				return "信用卡";
				break;
			default:
				return "未知";
				break;
		}
	}
}