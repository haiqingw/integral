<?php
namespace App\Controller;

use Think\Controller;

define("S_KEY", "sjfwporwejclamlmerew");
define("DEFAULT_LIMIT", 10);
define("DEFAULT_PAGE", 1);
define("DEFAULT_MONEY", "0.00");
define("DEFAULT_ACT_COUNT", 0);
define("INFO_HTML_VIEW_FILE_URL", "/Uploads/message/"); //资讯HTML生成文件存储路径
require_cache('./Public/ttf/class/set.php');
class BaseController extends Controller
{
	protected static function getChar($num)  // $num为生成汉字的数量
	{
		$b = '';
		for ($i = 0; $i < $num; $i++) {
            // 使用chr()函数拼接双字节汉字，前一个chr()为高位字节，后一个为低位字节
			$a = chr(mt_rand(0xB0, 0xD0)) . chr(mt_rand(0xA1, 0xF0));
            // 转码
			$b .= iconv('GB2312', 'UTF-8', $a);
		}
		return $b;
	}

	protected static function get_bus_ids($keywords, $plat)
	{
		$str = "";
		if (!empty($keywords)) {
			$where['plat'] = $plat;
			$where['busname|phone'] = array("like", "%" . $keywords . "%");
			$ret = M(T_BUS)->field('id')->where($where)->select();
			if ($ret) {
				$temp = array();
				$i = 0;
				while ($i < count($ret)) {
					$temp[] = $ret[$i]['id'];
					$i++;
				}
				$str = implode(",", $temp);
			}
		}
		return $str;
	}
	/**
	 * 移除 HTML、XML 以及 PHP 的标签,获取纯文本内容
	 * @date: 2017年12月4日 下午5:42:48
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $content 文本内容
	 * @return: string
	 */
	protected static function removeLabel($content)
	{
		if (empty($content)) {
			return false;
		}
		$content_01 = $content; //从数据库获取富文本content
		$content_02 = htmlspecialchars_decode($content_01); //把一些预定义的 HTML 实体转换为字符
		$content_03 = str_replace("&nbsp;", "", $content_02); //将空格替换成空
		$contents = strip_tags($content_03); //函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
		$con = mb_substr($contents, 0, 100, "utf-8"); //返回字符串中的前100字符串长度的字符
		return $con;
	}
	//获取称
	protected static function get_days_name($rerType, $days)
	{
		$name = "";
		if (!empty($rerType) || !empty($days)) {
			$cycle_list = M("cycle_templates")->where(array("status" => 1))->select();
			if ($cycle_list) {
				foreach ($cycle_list as $key => $val) {
					if ($rerType == $val['rerType']) {
						if ($val['days'] == $days) {
							$name = $val['title'];
						}
					}
				}
			}
		}
		return $name;

	}
	/**
	 * 获取文件路径
	 * @param $type 文件夹名称
	 * @return 文件路径
	 */
	protected static function get_public_file_Path($path, $plat, $bid)
	{
		$filePath = array(
			"." . $path . $plat . "/" . $bid . "/"
		);
		for ($i = 0; $i < count($filePath); $i++) {
			$newFilePath .= $filePath[$i];
			if (!is_dir($filePath[$i]))
				mkdir($newFilePath, 0777, true);
		}
		@chmod($newFilePath, 0777);
		return $newFilePath;
	}
	/**
	 * 测试调用
	 * @date: 2018年4月25日 上午9:45:00
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function testParams()
	{
		return array(
			"userID" => md5(203),
			"userPhone" => self::encode('13704751958'),
			"platformID" => 175
		);
	}
	/**
	 * 添加logs
	 * @param array $arr
	 * @param string $type
	 */
	protected static function aLogs($type, $arr, $bid)
	{
		$baseUrl = "./Public/payRecord/" . $bid . "/";
		if (!is_dir($baseUrl)) {
			@mkdir($baseUrl);
		}
		$url = $baseUrl . $type . date("Y-m-d") . ".txt";
		file_put_contents($url, var_export($arr, true) . "\n", FILE_APPEND);
	}
	/**
	 * 格式化字符串 （前后个保留四位中间 星号代替）
	 * @date: 2017年11月14日 下午4:12:14
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $cardNum 银行卡号
	 * @return:
	 */
	protected static function substrCut($params)
	{
		//获取字符串长度
		$strlen = mb_strlen($params, 'utf-8');
		//如果字符创长度小于2，不做任何处理
		if ($strlen < 2) {
			return $params;
		} else {
			//mb_substr — 获取字符串的部分
			$firstStr = mb_substr($params, 0, 4, 'utf-8');
			$lastStr = mb_substr($params, -4, 4, 'utf-8');
			//str_repeat — 重复一个字符串
			return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($params, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
		}
	}
	/**
	 * 姓名验证
	 * @date: 2017年11月13日 下午1:39:46
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function checkName($name)
	{
		//preg_match_all("/^([x81-xfe][x40-xfe])+$/",$name,$match)
		if (mb_strlen($name, "utf-8") >= 2 || mb_strlen($name, "utf-8" > 10)) {
			if (!eregi("[^\x80-\xff]", "$name")) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/*/
	 # 函数功能：身份证号码检查
	 # 函数名称：check_id
	 # 参数表 ：string $idcard 身份证号码
	 # 返回值 ：bool 是否正确
	 /*/
	protected static function check_id($idcard)
	{
		if (strlen($idcard) == 15 || strlen($idcard) == 18) {
			if (strlen($idcard) == 15) {
				$idcard = self::idcard_15to18($idcard);
			}
			if (self::idcard_checksum18($idcard)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	/*/
	 # 函数功能：
	 # 函数名称：idcard_checksum18
	 # 参数表 ：string $idcard 十八位身份证号码
	 # 返回值 ：bool
	 /*/
	protected static function idcard_checksum18($idcard)
	{
		if (strlen($idcard) != 18) {
			return false;
		}
		$idcard_base = substr($idcard, 0, 17);
		if (self::idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
			return false;
		} else {
			return true;
		}
	}
	/*/
	 # 函数功能：将15位身份证升级到18位
	 # 函数名称：idcard_15to18
	 # 参数表 ：string $idcard 十五位身份证号码
	 # 返回值 ：string
	 /*/
	protected static function idcard_15to18($idcard)
	{
		if (strlen($idcard) != 15) {
			return false;
		} else { // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
			if (array_search(substr($idcard, 12, 3), array(
				'996', '997', '998', '999'
			)) !== false) {
				$idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
			} else {
				$idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
			}
		}
		$idcard = $idcard . self::idcard_verify_number($idcard);
		return $idcard;
	}
	/*/
	 # 函数功能：计算身份证号码中的检校码
	 # 函数名称：idcard_verify_number
	 # 参数表 ：string $idcard_base 身份证号码的前十七位
	 # 返回值 ：string 检校码
	 /*/
	protected static function idcard_verify_number($idcard_base)
	{
		if (strlen($idcard_base) != 17) {
			return false;
		}
		$factor = array(
			7, 9, 10, 5, 8, 4, 2, 1,
			6, 3, 7, 9, 10, 5, 8, 4, 2
		); //debug 加权因子
		$verify_number_list = array(
			'1', '0', 'X', '9',
			'8', '7', '6', '5', '4', '3', '2'
		); //debug 校验码对应值
		$checksum = 0;
		for ($i = 0; $i < strlen($idcard_base); $i++) {
			$checksum += substr($idcard_base, $i, 1) * $factor[$i];
		}
		$mod = $checksum % 11;
		$verify_number = $verify_number_list[$mod];
		return $verify_number;
	}
	/**
	 * 获取收货地址
	 * @date: 2018年4月12日 下午3:15:02
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected static function getShipp($id)
	{
		if (!empty($id)) {
			$info = fRec(T_SPP_ADDRE, array(
				"id" => $id, "status" => 1
			), "name,phone,province,city,area,address");
			if ($info) {
				return $info;
			}
		}
		return false;
	}
	/**
	 * 获取 字段值
	 * @date: 2017年12月5日 下午3:01:24
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $table 数据表
	 * @param: $key 条件字段
	 * @param: $value 条件值
	 * @param: $field 获取字段
	 * @return: string
	 */
	protected static function getField($table, $key, $value, $field)
	{
		return M($table)->where(array(
			$key => $value
		))->getField($field);
	}
	/**
	 * 替换fckedit中的图片 添加域名
	 * @param  string $content 要替换的内容
	 * @param  string $strUrl 内容中图片要加的域名
	 * @return string
	 * @eg
	 */
	protected static function replacePicUrl($content = null, $strUrl = null)
	{
		if ($strUrl) {
			//提取图片路径的src的正则表达式 并把结果存入$matches中
			preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]+>/isU", $content, $matches);
			$img = "";
			if (!empty($matches)) {
				//注意，上面的正则表达式说明src的值是放在数组的第三个中
				$img = $matches[2];
			} else {
				$img = "";
			}
			if (!empty($img)) {
				$patterns = array();
				$replacements = array();
				foreach ($img as $imgItem) {
					$final_imgUrl = $strUrl . $imgItem;
					$replacements[] = $final_imgUrl;
					$img_new = "/" . preg_replace("/\//i", "\/", $imgItem) . "/";
					$patterns[] = $img_new;
				}
				//让数组按照key来排序
				ksort($patterns);
				ksort($replacements);
				//替换内容
				$vote_content = preg_replace($patterns, $replacements, $content);
				return $vote_content;
			} else {
				return $content;
			}
		} else {
			return $content;
		}
	}
	/**
	 * 获取文件路径
	 * @param $type 文件夹名称
	 * @return 文件路径
	 */
	protected static function HelpPath($plat)
	{
		$filePath = array(
			"./Uploads/UserHelp/" . $plat . "/"
		);
		for ($i = 0; $i < count($filePath); $i++) {
			$newFilePath .= $filePath[$i];
			if (!is_dir($filePath[$i]))
				mkdir($newFilePath, 0777, true);
		}
		@chmod($newFilePath, 0777);
		return $newFilePath;
	}
	public function _initialize()
	{
		//添加IP白名单
		//exit();
	}
	/**
	 * 截取小数位
	 */
	protected static function subDecimals($decimals, $number = 2)
	{
		$ex = @explode(".", $decimals);
		if (strlen($ex[1]) > $number) {
			$c = substr($ex[1], 0, $number);
			return $ex[0] . "." . $c;
		} else {
			return $decimals;
		}
	}
	/**
	 * 获取商户累计收益
	 * @param unknown $bid
	 * @return string|unknown
	 */
	protected static function getBusinessIncome($bid)
	{
		$money = M('changes_funds')->where(array(
			"bid" => $bid, "status" => "Y",
			"changeType" => "P"
		))->sum("changeAmount");
		return self::subDecimals(empty($money) ? 0 : $money);
	}
	/**
	 * 商户余额
	 * @date: 2017年6月15日 下午3:26:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function getBusinessBalance($bid, $cashType)
	{
		$money = "0.00";
		if (!empty($bid)) {
			$row = fRec(T_IMPOR, array("bus_id" => $bid, "payType" => $cashType), "total_amount");
			$balance = RSAcode($row['total_amount'], "DE") ? RSAcode($row['total_amount'], "DE") : '0.00';
			if ($money) {
				$money = self::subDecimals($balance);
			}
		}
		return $money;
	}
	/**
	 * 获取关闭详情
	 * @param string $type
	 * @param string $v
	 * @return true 关闭 false 不关闭
	 * $this->getCloseInfo('pay','wx|zfb|yl')
	 * $this->getCloseInfo('paid','all')
	 */
	protected static function getCloseInfo($type, $v)
	{
		if (empty($type) || empty($v)) {
			return false;
		}
		$week = getdate();
		$day = $week['wday'];
		$info = unserialize(getVal('week'));
		if ($info) {
			for ($i = 0; $i < count($info); $i++) {
				if ($info[$i]['day'] == $day && !empty($info[$i][$type]) && strstr($info[$i][$type], $v) != false) {
					return true;
				}
			}
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
	protected static function drawCaltStat($key, $pid)
	{
		$val = M("back_level")->where(array(
			"platform_id" => $pid
		))->getField($key);
		if ($val) {
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
	protected static function busID($where)
	{
		if (!empty($where)) {
			$wheres['phone|openid'] = $where['phone'];
			$where['plat'] = $where['plat'];
			$id = M(T_BUS)->where($wheres)->getField("id");
			if ($id) {
				return $id;
			}
		}
		return "";
	}
	/**
	 * 获取商户信息（电话，姓名）
	 * @date: 2018年6月28日 下午3:23:36
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:  $bid 商户ID
	 */
	protected static function get_user_info($bid)
	{
		if (!empty($bid)) {
			$busname = "";
			$phone = "";
			$businfo = M(T_BUS)->field("busname,phone")->where(array(
				"id" => $bid
			))->find();
			if ($businfo) {
				$realName = M(T_CERT)->where(array(
					"bid" => $bid
				))->getField("name");
				if ($realName) {
					$busname = $realName;
				} else {
					$busname = $businfo['busname'];
				}
				$phone = $businfo['phone'];
			}
			return array(
				"busname" => $busname,
				"phone" => $phone
			);
		}
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
	public static function check($phone, $bid, $pid)
	{
		if (!empty($phone) && !empty($bid) && !empty($pid)) {
			$where = array(
				"phone|openid" => $phone,
				"plat" => $pid
			);
			$id = M(T_BUS)->where($where)->getField("id");
			if ($bid == md5($id)) {
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
	public static function getValue($table, $colum, $value, $field)
	{
		return M($table)->where($colum . "=" . $value)->getField($field);
	}
	/**
	 * 银行卡号 每四位 空格 分开
	 * @date: 2017年3月13日 上午11:05:59
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: $card 银行卡号
	 * @return:
	 */
	public static function subBankCard($card)
	{
		$p = preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{4})([\d]{0,})?/', $card, $match);
		if (!$p) {
			preg_match('/([\d]{4})([\d]{4})([\d]{4})([\d]{0,})?/', $card, $match);
		}
		unset($match[0]);
		return implode(" ", $match);
	}
	/**
	 * 星号替换银行卡
	 * @date: 2017年6月8日 下午4:16:35
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	public static function replCard($card)
	{
		return "**** **** **** " . array_pop(explode(" ", self::subBankCard($card)));
	}
	/**
	 * 加密
	 * @param String $string 需要加密的字串
	 * @param String $skey 加密EKY
	 * @return String
	 */
	protected static function encode($string = '', $skey = S_KEY)
	{
		$strArr = str_split(base64_encode($string));
		$strCount = count($strArr);
		foreach (str_split($skey) as $key => $value)
			$key < $strCount && $strArr[$key] .= $value;
		return str_replace(array('=', '+', '/'), array(
			'O0O0O', 'o000o', 'oo00o'
		), join('', $strArr));
	}
	/**
	 * 解密
	 * @param String $string 需要解密的字串
	 * @param String $skey 解密KEY
	 * @return String
	 */
	protected static function decode($string = '', $skey = S_KEY)
	{
		$strArr = str_split(str_replace(array(
			'O0O0O', 'o000o', 'oo00o'
		), array(
			'=',
			'+', '/'
		), $string), 2);
		$strCount = count($strArr);
		foreach (str_split($skey) as $key => $value)
			$key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
		return base64_decode(join('', $strArr));
	}
	/**
	 * 发送手机验证码
	 * @date: 2017年6月5日 下午3:34:23
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: 
	 * @return: string
	 */
	protected static function phoneVerifyCode($mobile, $verify, $sendType, $pid = 3)
	{
		if (!empty($mobile) && !empty($verify) && !empty($sendType)) {
			$object = new \Common\Api\Message();
			$send = $object->SendVerifyCode($mobile, $verify, $sendType, $pid);
			if ($send) {
				return true;
			}
		}
		return false;
	}
	/**
	 * 根据代码获取银行名称
	 * @param string $type str 字符串 num 数字
	 */
	protected static function getBankName($code, $type = "str")
	{
		if ($type == "str") {
			$bankNames = json_decode('{"CDB":"国家开发银行","ICBC":"中国工商银行","ABC":"中国农业银行","BOC":"中国银行","CCB":"中国建设银行","PSBC":"中国邮政储蓄银行","COMM":"交通银行","CMB":"招商银行","SPDB":"上海浦东发展银行","CIB":"兴业银行","HXBANK":"华夏银行","GDB":"广东发展银行","CMBC":"中国民生银行","CITIC":"中信银行","CEB":"中国光大银行","EGBANK":"恒丰银行","CZBANK":"浙商银行","BOHAIB":"渤海银行","SPABANK":"平安银行","HKBEA":"东亚银行","HANABANK":"韩亚银行","SHRCB":"上海农村商业银行","YXCCB":"玉溪市商业银行","YDRCB":"尧都农商行","BJBANK":"北京银行","SHBANK":"上海银行","JSBANK":"江苏银行","HZCB":"杭州银行","NJCB":"南京银行","NBBANK":"宁波银行","HSBANK":"徽商银行","CSCB":"长沙银行","CDCB":"成都银行","CQBANK":"重庆银行","DLB":"大连银行","NCB":"南昌银行","FJHXBC":"福建海峡银行","HKB":"汉口银行","WZCB":"温州银行","QDCCB":"青岛银行","TZCB":"台州银行","JXBANK":"嘉兴银行","CSRCB":"常熟农村商业银行","NHB":"南海农村信用联社","CZRCB":"常州农村信用联社","H3CB":"内蒙古银行","SXCB":"绍兴银行","SDEB":"顺德农商银行","WJRCB":"吴江农商银行","ZBCB":"齐商银行","GYCB":"贵阳市商业银行","ZYCBANK":"遵义市商业银行","HZCCB":"湖州市商业银行","DAQINGB":"龙江银行","JINCHB":"晋城银行JCBANK","ZJTLCB":"浙江泰隆商业银行","GDRCC":"广东省农村信用社联合社","DRCBCL":"东莞农村商业银行","MTBANK":"浙江民泰商业银行","GCB":"广州银行","LYCB":"辽阳市商业银行","JSRCU":"江苏省农村信用联合社","LANGFB":"廊坊银行","CZCB":"浙江稠州商业银行","DYCB":"德阳商业银行","JZBANK":"晋中市商业银行","BOSZ":"苏州银行","GLBANK":"桂林银行","URMQCCB":"乌鲁木齐市商业银行","CDRCB":"成都农商银行","ZRCBANK":"张家港农村商业银行","BOD":"东莞银行","LSBANK":"莱商银行","BJRCB":"北京农村商业银行","TRCB":"天津农商银行","SRBANK":"上饶银行","FDB":"富滇银行","CRCBANK":"重庆农村商业银行","ASCB":"鞍山银行","NXBANK":"宁夏银行","BHB":"河北银行","HRXJB":"华融湘江银行","ZGCCB":"自贡市商业银行","YNRCC":"云南省农村信用社","JLBANK":"吉林银行","DYCCB":"东营市商业银行","KLB":"昆仑银行","ORBANK":"鄂尔多斯银行","XTB":"邢台银行","JSB":"晋商银行","TCCB":"天津银行","BOYK":"营口银行","JLRCU":"吉林农信","SDRCU":"山东农信","XABANK":"西安银行","HBRCU":"河北省农村信用社","NXRCU":"宁夏黄河农村商业银行","GZRCU":"贵州省农村信用社","FXCB":"阜新银行","HBHSBANK":"湖北银行黄石分行","ZJNX":"浙江省农村信用社联合社","XXBANK":"新乡银行","HBYCBANK":"湖北银行宜昌分行","LSCCB":"乐山市商业银行","TCRCB":"江苏太仓农村商业银行","BZMD":"驻马店银行","GZB":"赣州银行","WRCB":"无锡农村商业银行","BGB":"广西北部湾银行","GRCB":"广州农商银行","JRCB":"江苏江阴农村商业银行","BOP":"平顶山银行","TACCB":"泰安市商业银行","CGNB":"南充市商业银行","CCQTGB":"重庆三峡银行","XLBANK":"中山小榄村镇银行","HDBANK":"邯郸银行","KORLABANK":"库尔勒市商业银行","BOJZ":"锦州银行","QLBANK":"齐鲁银行","BOQH":"青海银行","YQCCB":"阳泉银行","SJBANK":"盛京银行","FSCB":"抚顺银行","ZZBANK":"郑州银行","SRCB":"深圳农村商业银行","BANKWF":"潍坊银行","JJBANK":"九江银行","JXRCU":"江西省农村信用","HNRCU":"河南省农村信用","GSRCU":"甘肃省农村信用","SCRCU":"四川省农村信用","GXRCU":"广西省农村信用","SXRCCU":"陕西信合","WHRCB":"武汉农村商业银行","YBCCB":"宜宾市商业银行","KSRB":"昆山农村商业银行","SZSBK":"石嘴山银行","HSBK":"衡水银行","XYBANK":"信阳银行","NBYZ":"鄞州银行","ZJKCCB":"张家口市商业银行","XCYH":"许昌银行","JNBANK":"济宁银行","CBKF":"开封市商业银行","WHCCB":"威海市商业银行","HBC":"湖北银行","BOCD":"承德银行","BODD":"丹东银行","JHBANK":"金华银行","BOCY":"朝阳银行","LSBC":"临商银行","BSB":"包商银行","LZYH":"兰州银行","BOZK":"周口银行","DZBANK":"德州银行","SCCB":"三门峡银行","AYCB":"安阳银行","ARCU":"安徽省农村信用社","HURCB":"湖北省农村信用社","HNRCC":"湖南省农村信用社","NYNB":"广东南粤银行","LYBANK":"洛阳银行","NHQS":"农信银清算中心","CBBQS":"城市商业银行资金清算中心"}', true);
			$bankName = $bankNames[$code];
			if ($bankName) {
				return $bankName;
			}
			return "未知";
		} else {
			$json = substr(file_get_contents("./Public/js/bankData.js"), 12);
			$result = json_decode($json, true);
			//dump($result);
			for ($i = 10; $i >= 2; $i--) {
				$code = substr($code, 0, $i);
				foreach ($result as $key => $val) {
					if ($val['bin'] == $code) {
						$bankName = $val['bankName'];
						break;
					}
				}
			}
			if ($bankName) {
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
	protected static function checkCard($card, $type = 'cardType')
	{
		$url = "https://ccdcapi.alipay.com/validateAndCacheCardInfo.json?_input_charset=utf-8&cardNo=" . $card . "&cardBinCheck=true";
		$response = json_decode(curlRequest($url), true);
		if ($response) {
			if ($type == 'cardType') {
				return self::getCardType($response['cardType']);
			} elseif ($type == 'bank') {
				return self::getBankName($response['bank']);
			} else {
				return false;
			}
		}
		return null;
	}
	/**
	 * 根据代码获取卡类型
	 */
	protected static function getCardType($type)
	{
		switch ($type) {
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
	/**
	 * 获取返现金额
	 * @date: 2018年5月16日 下午5:13:10
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected function rewardsnum($bid)
	{
		$sum = '0.00';
		if ($bid) {
			$sql = "select ifnull(sum(cashMoney),'0.00') sum from p_cash_back_log where outputAN in (select id from " . PREFIX . T_BUS . " where parent = " . $bid . ") and  receiveAN = " . $bid . " and isAddWallet = 1";
			$query = M()->query($sql);
			if ($query) {
				$sum = self::subDecimals($query[0]['sum'], 2);
			}
		}
		return $sum;
	}
	/**
	 * 获取团队总数
	 * @date: 2018年4月20日 下午3:12:54
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected function getteamnum($parentid)
	{
		$num = 0;
		if ($parentid) {
			$re = M(T_BUS)->where(array("parent" => $parentid, "status" => 1))->count();
			if ($re) {
				$num = $re;
			}
		}
		return $num;
	}
	/**
	 * 获取领取数
	 * @date: 2018年4月20日 下午3:18:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected function getnum($parentid)
	{
		$num = 0;
		if ($parentid) {
// 			$sql = "select count(*) con from " . PREFIX . T_ORDER_TAB . " where bid in (select id from " . PREFIX . T_BUS . " where parent = " . $parentid . " and status = 1) and isOrder = 1 and wxIsPay = 2";
			$sql = "select count(*) con from p_terminal_manage where bid in (select id from p_user where parent = {$parentid}) and isActive = 2";
			$query = M()->query($sql);
			if ($query) {
				$num = $query[0]['con'];
			}
		}
		return $num;
	}
	/**
	 * 商户余额
	 * @date: 2017年6月15日 下午3:26:20
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function busBalance($bid)
	{
		$money = 0;
		if (!empty($bid)) {
			$row = fRec("important", "bus_id=" . $bid, "total_amount");
			return RSAcode($row['total_amount'], "DE") ? RSAcode($row['total_amount'], "DE") : 0;
		}
		return false;
	}
	//false 关闭 true 不关闭
	protected static function isClosed()
	{
		if (self::getCloseInfo('paid', 'all')) {
			return false;
		}
		return true;
	}
	/**
	 * 获取账号提现状态
	 * @param number $bid
	 */
	protected static function getDrawcashStatus($bid)
	{
		$row = fRec(T_BUS, "id=" . $bid, "status");
		if ($row['status'] == 1) {
			return true;
		}
		return false;
	}
	/**
	 * 商户提现设置
	 * @param unknown $plat
	 */
	protected static function drawSet($plat, $user_level)
	{
		if (!empty($plat)) {
			$field = array(
				"maxMoney", "minMoney",
				"startTime", "endTime", "num",
				"drawStatus", "paymentStatus",
				"tax", "pou", "userLevel", "setMethod"
			);
			$res = M(T_DRAW_SET)->field($field)->where(array("plat" => $plat, "userLevel" => $user_level))->find();
			if ($res) {
				$res['startTime'] = date("H:i:s", $res['startTime']);
				$res['endTime'] = date("H:i:s", $res['endTime']);
				return $res;
			}
		}
		return false;
	}
	/**
	 * 是否可以提现
	 * @date: 2018年4月18日 下午3:46:05
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected static function drawStatus($plat, $userlevel)
	{
		$status = self::drawSet($plat, $userlevel);
		if ($status) {
			return true;
		}
		return false;
	}
	/**
	 * 函数用途描述
	 * @date: 2018年4月18日 下午4:03:58
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected static function drawCount($bid)
	{
		$num = 0;
		if (!empty($bid)) {
			$date = date("Y-m-d");
			$where = array(
				"bid" => $bid,
				"createTime" => array(
					array("egt", strtotime($date)),
					array(
						"lt",
						strtotime("+1 day", strtotime($date))
					)
				)
			);
			$con = M(T_WDM)->where($where)->count();
			if ($con > 0) {
				$num = $con;
			}
		}
		return $num;
	}
	/**
	 * 生成订单号
	 * @date: 2018年4月12日 下午3:07:06
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected static function generate_order_number()
	{
		return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
	}
	/**
	 * 随机邀请码
	 * @date: 2018年6月28日 上午11:55:29
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected static function invite_code()
	{
		//邀请年月
		$order_date = date('ym');
		//邀请码主体（YYYYMIISSNNNNNNNN）
		$order_id_main = $order_date . rand(10000000, 99999999);
		//邀请码主体长度
		$order_id_len = strlen($order_id_main);
		$order_id_sum = 0;
		for ($i = 0; $i < $order_id_len; $i++) {
			$order_id_sum += (int)(substr($order_id_main, $i, 1));
		}
		//唯一邀请码（YYYYIISSNNNNNNNNCC）
		$order_id = $order_id_main . str_pad((100 - $order_id_sum % 100) % 100, 2, '0', STR_PAD_LEFT);
		return $order_id;
	}
	//获取银行卡信息
	protected static function getBankInfo($bid, $id)
	{
		$info = fRec(T_BNK, 'id=' . $id . '||bid=' . $bid . '||status=1', 'id,card_number,name,bank_name');
		$row = fRec(T_BUS, "id=" . $bid, "idCard,realName");
		if ($info) {
			$bank = @explode("-", $info['bank_name']);
			return [
				'id' => $info['id'],
				'name' => $row['realName'],
				'idCard' => $row['idCard'],
				'bankName' => $bank[0],
				'bankCardNum' => $info['card_number'],
				'wh' => substr($info['card_number'], strlen($info['card_number']) - 3)
			];
		}
		return false;
	}
	/**
	 * 获取提现到账银行卡信息
	 * @date: 2018年4月19日 上午9:52:16
	 * @author: HaiQing.Wu <398994668@qq.com>
	 */
	protected static function account_bank_card($bid)
	{
		if (!empty($bid)) {
			$info = M(T_BNK)->field("id")->where(array(
				"bid" => $bid
			))->find();
			if ($info) {
				return ["id" => $info['id']];
			}
		}
		return false;
	}
	/**
	 * 更新提现管理
	 * @date: 2017年3月9日 上午10:55:55
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param: variable
	 * @return:
	 */
	protected static function updateWithdraw($params)
	{
		if (checkParams($params, array(
			"bid",
			"money", "bankId", "ordernum",
			"remark", "platform_id", "cashType"
		))) {
			$data = array(
				"bid" => $params['bid'],
				"payType" => $params['cashType'],
				"money" => $params['money'],
				"bankId" => $params['bankId'],
				"ordernum" => $params['ordernum'],
				"status" => 1, "reviewStatus" => 2,
				"createTime" => time(),
				"remark" => $params['remark'],
				"platform_id" => $params['platform_id']
			);
			if (!aRec("wd_manage", $data)) {
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}
}