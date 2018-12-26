<?php
mb_internal_encoding('utf-8');
define("SYS","system");
define("PUB_TTF","./Public/ttf/class/");
/**
 * 隐藏手机号中间四位
 * @param unknown $phone
 * @return mixed
 */
function hidePhone($phone){
	if(!substr_count($phone,"****")){
		$phone = preg_replace('/(\d{3})(\d{4})(\d{4})/i','$1****$3',$phone);
	}
	return $phone;
}
/**
 * 逗号分隔的字符串按照升序排序
 */
function strSort($str){
	if(gettype($str) == "string"){
		$ex = @explode(",",$str);
	}elseif(gettype($str) == "array"){
		$tempArr = array();
		$str = array_values($str);
		for($i = 0;$i < count($str);$i++){
			$tempArr[$i] = $str[$i]['key'];
		}
		$ex = $tempArr;
	}
	asort($ex);
	return @implode(",",$ex);
}
function importTTF($className){
	return require_cache(PUB_TTF . $className);
}
function getPlatID($bid){
	return M('business')->where("id=" . $bid)->getField("platform_id");
}
/**
 * 极光推送
 * @param $title 标题
 * @param $id 接收者ID 传all为所有人
 * @param $type 类型,自定义
 * @param $json 自定义json字符串数据
 */
function JpushSend($title, $id, $type, $json, $platid = 3){
	$row = fRec("usertable","usertable_ID=" . $platid,"jpush_appkey,jpush_secret");
	if($row && !empty($row['jpush_appkey']) && !empty($row['jpush_secret'])){
		importTTF('Jpush.class.php');
		\Jpush::setParams("app_key",$row['jpush_appkey']);
		\Jpush::setParams("master_secret",$row['jpush_secret']);
		// \Jpush::send_pub("all","你好");
		// 推送标题
		// $title = "您有一笔新的交易";
		// app端接收的数据
		// $json = '{"income":"'.$order['m1'].'","rebate":"'.$order['m2'].'","msg":"支付成功","so":"'.$order['o1'].'","to":"'.$order['o2'].'","pt":"'.dateFormat($order['pt'],4).'"}';
		// 推送消息发送
		// \Jpush::send_pub(array('alias'=>array($bid)),$title,"payment",$json);
		$rec = $id == "all" ? $id : array(
			'alias' => array($id));
		\Jpush::send_pub($rec,$title,$type,$json);
	}
}
/**
 * 对变量进行 JSON 编码
 * @param mixed value 待编码的 value ，除了resource 类型之外，可以为任何数据类型，该函数只能接受 UTF-8 编码的数据
 * @return string 返回 value 值的 JSON 形式
 */
function json_encode_ex($value){
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
 * 公钥加密
 * @param unknown $sourcestr
 * @param unknown $fileName
 * @return string
 */
function publickey_encodeing($sourcestr, $fileName){
	$key_content = file_get_contents($fileName);
	$pubkeyid = openssl_get_publickey($key_content);
	if(openssl_public_encrypt($sourcestr,$crypttext,$pubkeyid)){
		return base64_encode("" . $crypttext);
	}
}
/**
 * 私钥解密
 * @param unknown $crypttext
 * @param unknown $fileName
 * @param string $fromjs
 */
function privatekey_decodeing($crypttext, $fileName, $fromjs = FALSE){
	$key_content = file_get_contents($fileName);
	$prikeyid = openssl_get_privatekey($key_content);
	$crypttext = base64_decode($crypttext);
	$padding = $fromjs ? OPENSSL_NO_PADDING : OPENSSL_PKCS1_PADDING;
	if(openssl_private_decrypt($crypttext,$sourcestr,$prikeyid,$padding)){
		return $fromjs ? rtrim(strrev($sourcestr),"\0") : "" . $sourcestr;
	}
	return;
}
/**
 * RSA加密解密
 * @param string $string
 * @param string $type EN 默认php加密 DE php解密 JS js解密
 * @return string|boolean
 */
function RSAcode($string, $type = "EN"){
	if(!empty($string)){
		$publicKey = PUB_TTF . 'keys/rsa_pub.pem';
		$privateKey = PUB_TTF . 'keys/rsa_pri.pem';
		if($type == "EN"){
			return publickey_encodeing($string,$publicKey);
		}elseif($type == "DE"){
			return privatekey_decodeing($string,$privateKey);
		}elseif($type == "JS"){
			$receiveKey = base64_encode(pack("H*",$string));
			return privatekey_decodeing($receiveKey,$privateKey,TRUE);
		}
	}
	return false;
}
/**
 * 获取val
 * @param string $key
 */
function getVal($key = ""){
	$row = fRec(SYS,SYS . '_key=' . $key,SYS . '_val val');
	if($row){
		return $row['val'];
	}
	return "";
}
/**
 * 检测元素是否正确
 * 只能检测一维数组
 * @param $arr 被检测的数组
 * @param $repar $arr中必要的元素(键名)
 * @return boolean
 */
function checkParams($arr, $repar = array()){
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
 * 检测元素全部是否正确
 * 只能检测一维数组
 * @param $arr 被检测的数组
 * @param $repar $arr中必要的元素(键名)
 * @return boolean
 */
function check_params($arr, $repar = array()){
	if(!is_array($arr)){
		return false;
	}
	if(!count($arr)){
		return false;
	}
	// 判断每个元素是否存在
	$repar = array_keys($repar);
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
 * 检测元素是否正确(充值使用)
 * 只能检测一维数组
 * @param $arr 被检测的数组
 * @param $repar $arr中必要的元素(键名)
 * @return boolean
 */
function check_recharge_params($arr, $repar = array()){
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
function Area($lrIP){
	$Ip = new \Org\Net\IpLocation('UTFWry.dat'); // 实例化类 参数表示IP地址库文件
	$area = $Ip->getlocation($lrIP); // 获取某个IP地址所在的位置
	return $area["country"] . '-' . $area["area"];
}
//验证码方法
function check_verify($code, $id = ''){
	$verify = new \Think\Verify();
	return $verify->check($code,$id);
}
//二维数组动态添加元素
function addkey(&$val, $key, $param){
	$val[$param['key']] = $param['val'];
}
/**
 * 检查权限
 * @param name string|array  需要验证的规则列表,支持逗号分隔的权限规则或索引数组
 * @param uid  int           认证用户的id
 * @param string mode        执行check的模式
 * @param relation string    如果为 'or' 表示满足任一条规则即通过验证;如果为 'and'则表示需满足所有规则才能通过验证
 * @return boolean           通过验证返回true;失败返回false
 */
function authcheck($name, $uid, $type = 1, $mode = 'url', $relation = 'or'){
	if(!in_array($uid,C('ADMINISTRATOR'))){
		$auth = new \Think\Auth();
		return $auth->check($name,$uid,$type,$mode,$relation) ? true : false;
	}else{
		return true;
	}
}
function display($name){
	//$name='Home/'.$name;
	$uid = session('uid');
	if(!in_array($uid,C('ADMINISTRATOR'))){
		if(!authcheck($name,$uid,$type = 0,$mode = 'url',$relation = 'or')){
			return "style='display:none'";
		}
	}
}
function orgcateTree($pid = 0, $level = 0, $type = 0){
	$cate = M('auth_group');
	$array = array();
	$tmp = $cate->where(array('pid' => $pid, 
		'type' => $type))->order("level asc,sort asc")->select();
	//echo $cate->getLastSql();
	if(is_array($tmp)){
		foreach($tmp as $v){
			$v['level'] = $level;
			//$v['pid']>0;
			$array[count($array)] = $v;
			$sub = orgcateTree($v['id'],$level + 1,$type);
			if(is_array($sub))
				$array = array_merge($array,$sub);
		}
	}
	return $array;
}
function cateTree($pid = 0, $object, $level = 0, $db = 0){
	$cate = M('' . $db . '');
	$array = array();
	$tmp = $cate->where(array('pid' => $pid, 
		'object' => $object))->order("sortno")->select();
	if(is_array($tmp)){
		foreach($tmp as $v){
			$v['level'] = $level;
			//$v['pid']>0;
			$array[count($array)] = $v;
			$sub = cateTree($v['id'],$object,$level + 1,$db);
			if(is_array($sub))
				$array = array_merge($array,$sub);
		}
	}
	return $array;
}
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start = 0, $length){
	$charset = "utf-8";
	$suffix = true;
	if(function_exists("mb_substr"))
		$slice = mb_substr($str,$start,$length,$charset);
	elseif(function_exists('iconv_substr')){
		$slice = iconv_substr($str,$start,$length,$charset);
		if(false === $slice){
			$slice = '';
		}
	}else{
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset],$str,$match);
		$slice = join("",array_slice($match[0],$start,$length));
	}
	return $suffix ? $slice . '' : $slice;
}
/*  视图操作
 *  条件：处理复杂类查询
 *  检测视图是否存在？创建视图：调用视图；
 *  返回值：二维数组
 * */
/*  调用存储过程
 * */
//获取当前时间
function getNow(){
	return date("Y-m-d H:i:s");
}
//添加日志
function adLog($uid, $content, $status){
	$logPath = '[' . MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME . "] ";
	$state = $status ? 1 : 0;
	$data = array("uid" => $uid, 
		"content" => $logPath . $content, 
		"addtime" => getNow(), 
		"ip" => get_client_ip(), 
		"status" => $state);
	aRec('log',$data);
}
/*
 * curl get post
 */
function curlRequest($url, $data = null, $second = 30){
	$curl = curl_init();
	curl_setopt($curl,CURLOPT_TIMEOUT,$second);
	curl_setopt($curl,CURLOPT_URL,$url);
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE);
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE);
	if(!empty($data)){
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
	}
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
	$output = curl_exec($curl);
	curl_close($curl);
	return $output;
}
/*  全局日志存储
 *  $str   传入的字符串
 *  $type  默认成功状态 false失败状态 
 * */
function aLog($str, $status = true){
	$logPath = '[' . MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME . "]\n";
	$filePath = array("./Uploads/", "log/", 
		date('Y') . "/", date('m') . "/");
	for($i = 0;$i < count($filePath);$i++){
		$newFilePath .= $filePath[$i];
		if(!is_dir($filePath[$i]))
			mkdir($newFilePath);
	}
	$fileName = date('d') . ".txt";
	if(!$handle = fopen($newFilePath . $fileName,'a')){
		return "can't open to file:" . $fileName;
		exit();
	}
	$type = $status ? "Success" : "Error";
	if(fwrite($handle,"[$type][" . date('Y-m-d H:i:s') . "]" . $str . $logPath) === FALSE){
		return "can't write to file:" . $fileName;
		exit();
	}
	fclose($handle);
	//if(is_file($newUpName)) unlink($newUpName);
}
/*  敏感字符串过滤
 *  $str 传入的字符串
 *  返回值：字符串
 * */
function filter($str){
	$keywords = file_get_contents("./Public/dirty");
	$keywords = explode("\n",$keywords);
	foreach($keywords as $val){
		$str = str_ireplace($val,'***',$str);
	}
	return $str;
}
/*  过滤html，js，css代码
 *  $str string
 *  return string
 * */
function fHtml($str){
	$str = htmlspecialchars_decode($str);
	$str = preg_replace("/<(.*?)>/","",$str);
	return $str;
}
/*  查询总数
 *  $table ：表名 string
 *  $where : 条件 string   多条件分割符 ||
 *  $refield : 要返回的字段 string 不填写则全部返回
 *  返回值 ：int 或 false
 *  例 ： cRec('t_me_account','f_m_AccountID=1'); 
 *   */
function cRec($table, $where = ""){
	if(empty($table)){
		return false;
	}else{
		if(empty($where)){
			$where = array();
		}
		if(!is_array($where)){
			$where = strToArray($where);
		}
		return M($table)->where($where)->count();
	}
}
/*  查询单条记录
 *  $table ：表名 string
 *  $where : 条件 string   多条件分割符 ||
 *  $refield : 要返回的字段 string 不填写则全部返回
 *  返回值 ：一维数组 或 false
 *  例1 ： fRec('t_me_account','f_m_AccountID=1||f_m_AccountName=张三','f_m_AccountPhone,f_m_AccountMail'); 
 *  例2 ： fRec('t_me_account','f_m_AccountID=1'); 
 *   */
function fRec($table, $where, $refield = "", $order = ""){
	if(empty($table) || empty($where)){
		return false;
	}else{
		if(!is_array($where)){
			$where = strToArray($where);
		}
		return M($table)->field($refield)->where($where)->order($order)->find();
	}
}
/*  查询多条记录
 *  $table ：表名 string
 *  $where : 条件 string   多条件分割符 ||
 *  $order ：排序字段 string 例："f_m_AccountID desc,f_m_AccountName asc"
 *  $page  ：页数 默认第一页 int
 *  $limit ：多少条记录 默认20条 int 
 *  $refield : 要返回的字段 string 不填写则全部返回
 *  返回值 ：二维数组 或 false
 *  */
function sRec($table, $where = "", $order = "", $page = 1, $limit = 20, $refield = ""){
	if(empty($table)){
		return false;
	}else{
		if(empty($where)){
			$where = array();
		}
		$offset = ($page - 1) * $limit;
		if(!is_array($where)){
			$where = strToArray($where);
		}
		return M($table)->field($refield)->where($where)->order($order)->limit("$offset,$limit")->select();
	}
}
/*  添加一条记录
 *  $table : 表名  string
 *  $data  : 数据  string   多条件分割符 ||
 *  返回值 ：自动生成的ID 或 false
 *  例 : aRec('t_me_account','f_m_AccountName=张三||f_m_AccountMail=1@qq.com');
 *  做验证配合 fRec()
 * */
function aRec($table, $data){
	if(empty($table) || empty($data)){
		return false;
	}else{
		if(!is_array($data)){
			$data = strToArray($data);
		}
		$id = M($table)->add($data);
		if($id){
			return $id;
		}else{
			return false;
		}
	}
}
/*  删除某一条记录
 *  $table : 表名  string
 *  $where : 条件  string   多条件分割符 ||
 *  返回值 ：true 或 false
 *  例 ： dRec('t_me_account','f_m_AccountID=1');
 *  做验证配合 fRec()
 *  */
function dRec($table, $where){
	if(empty($table) || empty($where)){
		return false;
	}else{
		if(!is_array($where)){
			$where = strToArray($where);
		}
		if(M($table)->where($where)->delete()){
			return true;
		}else{
			return false;
		}
	}
}
/*  修改某一条记录 
 *  $table : 表名  string
 *  $data  : 要修改的字段  string
 *  $where : 条件  string
 *  返回值 ：true 或 false
 *  例 ： uRec('t_me_account','f_m_AccountName=张三||f_m_AccountMail=1@qq.com','f_m_AccountID=1');
 *  做验证配合 fRec()
 * */
function uRec($table, $data, $where){
	if(empty($table) || empty($data) || empty($where)){
		return false;
	}else{
		if(!is_array($data)){
			$data = strToArray($data);
		}
		if(!is_array($where)){
			$where = strToArray($where);
		}
		if(M($table)->where($where)->save($data)){
			return true;
		}else{
			return false;
		}
	}
}
/* *
 * 获取某一字段值 
 *  $table : 表名  string
 *  $where : 条件  string
 *  $refield 字段  strings
 */
function gFec($table, $where, $refield){
	if(empty($table) || empty($where) || empty($refield)){
		return false;
	}else{
		if(!is_array($where)){
			$where = strToArray($where);
		}
		return M($table)->where($where)->getField($refield);
	}
}
/*  字符串分隔符自动转换数组
 *  $str   : 要分割的字符串 string
 *  $begin : 开始分隔符 string
 *  $end   : 结束分隔符 string
 *  返回值 ：数组 array
 * */
function strToArray($str, $begin = "||", $end = "="){
	$exdata = @explode($begin,$str);
	for($i = 0;$i < count($exdata);$i++){
		$ex = @explode($end,$exdata[$i]);
		$newData[$ex[0]] = $ex[1];
	}
	return $newData;
}
/* 加密解密
 * $string 字符串
 * $operation 类型 E加密 D解密
 * $key 钥匙 自定义
 * $sp 总控钥匙 admin
 * 返回值 ： string  错误返回 空字符串
 * 例：encrypt('你好','E','123','admin');
 * */
function encrypt($string, $operation, $key = '', $sp = ''){
	if($sp == 'admin'){
		$key = md5($key);
		$key_length = strlen($key);
		$string = $operation == 'D' ? base64_decode($string) : substr(md5($string . $key),0,8) . $string;
		$string_length = strlen($string);
		$rndkey = $box = array();
		$result = '';
		for($i = 0;$i <= 255;$i++){
			$rndkey[$i] = ord($key[$i % $key_length]);
			$box[$i] = $i;
		}
		for($j = $i = 0;$i < 256;$i++){
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for($a = $j = $i = 0;$i < $string_length;$i++){
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}
		if($operation == 'D'){
			if(substr($result,0,8) == substr(md5(substr($result,8) . $key),0,8)){
				return substr($result,8);
			}else{
				return '';
			}
		}else{
			return str_replace('=','',base64_encode($result));
		}
	}else{
		return '';
	}
}
/**
 * 文件名生成函数
 * 生成结果 201510221445482516277
 */
function crFileName(){
	$a = date('Y');
	$a .= date('m');
	$a .= date('d');
	$b = uniqid();
	$c = rand(1,800);
	return $a . $b . $c;
}
/* 树型分类
 * $Table 表名称
 * $field 所需字段
 $field=array(
 'id'    => '',
 'fid'   => '',
 'level' => '',
 'sort'  => '',
 );
 * $pid 起始父ID
 * $level 起始级别
 * 返回值 ： array 
 * 例：baseTrees('Good',$field,0,0);
 * */
function baseTrees($Table = "", $field = array(), $pid = 0, $level = 0){
	if(empty($Table) && empty($field)){
		return false;
	}
	$cate = M($Table);
	$array = array();
	$tmp = $cate->where(array(
		$field['fid'] => $pid))->order($field['sort'])->select();
	if(!empty($tmp)){
		foreach($tmp as $v){
			$v[$field['level']] = $level;
			$array[count($array)] = $v;
			$sub = baseTrees($Table,$field,$v[$field['id']],$level + 1);
			if(!empty($sub)){
				$array = array_merge($array,$sub);
			}
			;
		}
	}
	return $array;
}
/*  删除数组中的某个键值
 *  $arr  数组
 *  $str  要删除的键值
 *  return array
 * */
function dArr($arr, $str){
	if(!is_array($arr) || empty($str)){
		return false;
	}else{
		foreach($arr as $key => $value){
			if($value === $str)
				array_splice($arr,$key,1);
		}
		return $arr;
	}
}
//生成随机密码
function getRandPass($length = 6){
	$password = '';
	//将你想要的字符添加到下面字符串中，默认是数字1-9和26个英文字母
	$chars = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
	$char_len = strlen($chars);
	for($i = 0;$i < $length;$i++){
		$loop = mt_rand(0,($char_len - 1));
		//将这个字符串当作一个数组，随机取出一个字符，并循环拼接成你需要的位数
		$password .= $chars[$loop];
	}
	return $password;
}
/**
 * 发送邮件函数
 */
function sendMail1($to, $title, $content){
	Vendor('PHPMailer.PHPMailerAutoload');
	$mail = new PHPMailer(); //实例化
	$mail->IsSMTP(); // 启用SMTP
	$mail->Host = C('MAIL_HOST'); //smtp服务器的名称（这里以163邮箱为例）
	$mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
	$mail->Username = C('MAIL_USERNAME'); //你的邮箱名
	$mail->Password = C('MAIL_PASSWORD'); //邮箱密码
	$mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
	$mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
	$mail->AddAddress($to,"尊敬的客户");
	$mail->WordWrap = 50; //设置每行字符长度
	$mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
	$mail->CharSet = C('MAIL_CHARSET'); //设置邮件编码
	$mail->Subject = $title; //主题
	$mail->Body = $content; //内容
	$mail->AltBody = "内蒙古懒人网络科技有限公司"; //正文不支持HTML的备用显示
	if($mail->Send()){
		return true;
	}else{
		// return($mail->ErrorInfo);
		return false;
	}
}
//获取ip地址的地理位置
function GetIpLookup($ip = ''){
	if(empty($ip)){
		$ip = get_client_ip();
	}
	$res = @file_get_contents('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip=' . $ip);
	if(empty($res)){
		return false;
	}
	$jsonMatches = array();
	preg_match('#\{.+?\}#',$res,$jsonMatches);
	if(!isset($jsonMatches[0])){
		return false;
	}
	$json = json_decode($jsonMatches[0],true);
	if(isset($json['ret']) && $json['ret'] == 1){
		$json['ip'] = $ip;
		unset($json['ret']);
	}else{
		return false;
	}
	return $json;
}
//替换过滤中文逗号全角逗号
function strreplace($Astr){
	$Astr = str_replace("，",",",$Astr);
	$Astr = str_replace("　，",",",$Astr);
	return trim($Astr);
}
//去掉空数组，重复数组键值，重建数组索引
function FilterUniqueValues($Astr){
	$Astr = array_filter($Astr); //去掉空数组
	$Astr = array_unique($Astr); //去掉数组中重复的元素
	$Astr = array_values($Astr); //重建数组索引
	return $Astr;
}
//数组过滤
function delEempty($v){
	if($v === "" || is_int($v)){ //当数组中存在空值和php值时，换回false，也就是去掉该数组中的空值和php值
		return false;
	}
	return true;
}
//上传图片裁图
function CutPicture($imgstr, $size){
	$imgrow = explode(',',$imgstr);
	$artwork = './Uploads/artwork';
	$big = './Uploads/big';
	$small = './Uploads/small';
	$upload_tmp = './Uploads/upload_tmp/';
	$datedir = date('Y-m-d');
	//解析裁图尺寸
	$a = explode(',',$size);
	foreach($a as $value){
		$sizerow[] = explode(':',$value);
	}
	//解析裁图尺寸
	//创建文件夹
	// Create target dir
	if(!is_dir($artwork . '/' . $datedir)){
		@mkdir($artwork . '/' . $datedir,0777,true);
	}
	// Create target dir
	if(!is_dir($big . '/' . $datedir)){
		@mkdir($big . '/' . $datedir,0777,true);
	}
	// Create target dir
	if(!is_dir($small . '/' . $datedir)){
		@mkdir($small . '/' . $datedir,0777,true);
	}
	//创建文件夹
	//循环裁剪操作
	foreach($imgrow as $value){
		$fileName = substr($value,11);
		$image = new \Think\Image();
		$imghref = $upload_tmp . $value;
		//循环裁剪
		if($size != ''){
			for($i = 0;$i < count($sizerow);$i++){
				$image->open($imghref);
				$imagesize = $image->size(); // 返回图片的尺寸数组 0 图片宽度 1 图片高度
				//宽小于高
				if($imagesize[0] < $imagesize[1]){
					$pro = $imagesize[0] / $sizerow[$i][0];
					$w = $sizerow[$i][0];
					$h = $imagesize[1] / $pro;
				}
				//宽大于高
				if($imagesize[0] > $imagesize[1]){
					$pro = $imagesize[1] / $sizerow[$i][1];
					$w = $imagesize[0] / $pro;
					$h = $sizerow[$i][0];
				}
				//相等
				if($imagesize[0] == $imagesize[1]){
					$pro = $imagesize[0] / $sizerow[$i][0];
					$w = $sizerow[$i][0];
					$h = $imagesize[1] / $pro;
				}
				//首先缩略图片
				$image->thumb($w,$h,1);
				$imagesize = $image->size(); // 返回图片的尺寸数组 0 图片宽度 1 图片高度
				//宽大于高
				if($sizerow[$i][0] < $sizerow[$i][1]){
					$stw = ($imagesize[0] - $sizerow[$i][0]) / 2;
					$sth = 0;
				}
				//宽小于高
				if($sizerow[$i][0] > $sizerow[$i][1]){
					$stw = 0;
					$sth = ($imagesize[1] - $sizerow[$i][1]) / 2;
				}
				//相等
				if($sizerow[$i][0] == $sizerow[$i][1]){
					$stw = 0;
					$sth = 0;
				}
				//裁图并保存
				if($i == 0){
					$bigname = $big . '/' . $datedir . '/' . $fileName;
					$image->crop($sizerow[$i][0],$sizerow[$i][1],$stw,$sth)->save($bigname);
				}else{
					$smallname = $small . '/' . $datedir . '/' . $fileName;
					$image->crop($sizerow[$i][0],$sizerow[$i][1],$stw,$sth)->save($smallname);
				}
			}
		}
		//移动保存原图
		rename($upload_tmp . $value,$artwork . '/' . $datedir . '/' . $fileName);
		//移动保存原图
		//循环裁剪
	}
}
//清空上传图片缓存表
function DelTempImg($ids){
	$wheres['userids'] = $ids;
	$temps = D('imagetemp'); //实例化图片缓存表
	$rows = $temps->field('id,imgsrc')->where($wheres)->select();
	for($i = 0;$i < count($rows);$i++){
		$statuses = false;
		$artworks = 'Uploads/upload_tmp/' . $rows[$i]['imgsrc']; //图片路径
		if(file_exists($artworks)){
			@unlink($artworks); //删除图片
			$statuses = true;
		}
		if($statuses){
			$temps->delete($rows[$i]['id']);
		}
	}
}
//删除图片
function DelbsImg($imgpath){
	$rows = explode(',',$imgpath);
	for($i = 0;$i < count($rows);$i++){
		$statuses = false;
		$artworks = 'Uploads/artwork/' . $rows[$i]; //图片路径
		$bigs = 'Uploads/big/' . $rows[$i]; //图片路径
		$smalls = 'Uploads/small/' . $rows[$i]; //图片路径
		if(file_exists($artworks)){
			@unlink($artworks); //删除图片
			$statuses = true;
		}
		if(file_exists($bigs)){
			@unlink($bigs); //删除图片
			$statuses = true;
		}
		if(file_exists($smalls)){
			@unlink($smalls); //删除图片
			$statuses = true;
		}
	}
}
/*  将时间戳转换成时间
 *  $timestamp  时间戳
 *  $type  类型
 *  返回值：格式化的时间
 *  */
function dateFormat($timestamp, $type = 2){
	switch($type){
		case 1:
			$format = "Y-m-d";
			break;
		case 2:
			$format = "Y-m-d H:i:s";
			break;
		case 3:
			$format = "Y年m月d日";
			break;
		case 4:
			$format = "Y年m月d日 H时i分s秒";
			break;
	}
	return date($format,$timestamp);
}
/**
 * 保存文件
 * 
 * @param string $fileName 文件名（含相对路径）
 * @param string $text 文件内容
 * @return boolean 
 */
function saveFile($fileName, $text){
	if(!$fileName || !$text)
		return false;
	if(makeDir(dirname($fileName))){
		if($fp = fopen($fileName,"w")){
			if(@fwrite($fp,$text)){
				fclose($fp);
				return true;
			}else{
				fclose($fp);
				return false;
			}
		}
	}
	return false;
}
/**
 * 连续创建目录
 *
 * @param string $dir 目录字符串
 * @param int $mode 权限数字
 * @return boolean
 */
function makeDir($dir, $mode = "0777"){
	if(!dir)
		return false;
	if(!file_exists($dir)){
		return mkdir($dir,$mode,true);
	}else{
		return true;
	}
}
//根据菜品ID查询菜品分类名称
function CaiPing_Name($D_C_ID){
	$T_goods_sort = D("T_goods_sort"); //实例化菜品分类表
	$where = array(
		"f_g_GoodsSortID" => intval($D_C_ID), 
		"f_b_AccountID" => session("BusinID"));
	$info = $T_goods_sort->field("f_g_GoodsSortName")->where($where)->find();
	return $info["f_g_GoodsSortName"];
}
//根据菜品ID查询菜品名称
function CaiPingName($cpid){
	$T_dishes = D("T_dishes"); //实例化菜品表
	$where = array("Dishes_ID" => $cpid, 
		"Dishes_AccountID" => session("BusinID"));
	$info = $T_dishes->field("Dishes_Name")->where($where)->find();
	return $info["Dishes_Name"];
}
//获取用户信息
function getUserName($userid){
	$userMod = D("usertable");
	$where = array(
		"usertable_ID =" . intval($userid));
	$getUserName = $userMod->field("usertable_Name")->where($where)->find();
	return $getUserName["usertable_Name"];
}
//获取角色信息
function getUserRoleName($roleid){
	$userMod = D("role");
	$where = array("role_id =" . intval($roleid));
	$getUserName = $userMod->field("role_name")->where($where)->find();
	return $getUserName["role_name"];
}
//获取菜品类型信息
function GoodArea($ids){
	$str = '';
	$Goodarr = D("t_goods_sort");
	if($ids){
		$idsrow = explode(',',$ids);
		foreach($idsrow as $value){
			$where['f_g_GoodsSortID'] = intval($value);
			$GoodName = $Goodarr->field("f_g_GoodsSortName")->where($where)->find();
			$str .= "　" . $GoodName["f_g_GoodsSortName"];
		}
	}else{
		$str = '无';
	}
	return $str;
}
//打印方法
function liansuo_post($url, $data){ // 模拟提交数据函数
	$curl = curl_init(); // 启动一个CURL会话
	curl_setopt($curl,CURLOPT_URL,$url); // 要访问的地址
	curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0); // 对认证证书来源的检测
	curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,1); // 从证书中检查SSL加密算法是否存在
	curl_setopt($curl,CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览
	curl_setopt($curl,CURLOPT_HTTPHEADER,array(
		'Expect:')); //解决数据包大不能提交
	curl_setopt($curl,CURLOPT_FOLLOWLOCATION,1); // 使用自动跳转
	curl_setopt($curl,CURLOPT_AUTOREFERER,1); // 自动设置Referer
	curl_setopt($curl,CURLOPT_POST,1); // 发送一个常规的Post请求
	curl_setopt($curl,CURLOPT_POSTFIELDS,$data); // Post提交的数据包
	curl_setopt($curl,CURLOPT_COOKIEFILE,$GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息
	curl_setopt($curl,CURLOPT_TIMEOUT,30); // 设置超时限制防止死循
	curl_setopt($curl,CURLOPT_HEADER,0); // 显示返回的Header区域内容
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1); // 获取的信息以文件流的形式返回
	$tmpInfo = curl_exec($curl); // 执行操作
	if(curl_errno($curl)){
		echo 'Errno' . curl_error($curl);
	}
	curl_close($curl); // 关键CURL会话
	return $tmpInfo; // 返回数据
}
function generateSign($params, $apiKey, $msign){
	//所有请求参数按照字母先后顺序排
	ksort($params);
	//定义字符串开始所包括的字符串
	$stringToBeSigned = $apiKey;
	//把所有参数名和参数值串在一起
	foreach($params as $k => $v){
		$stringToBeSigned .= urldecode($k . $v);
	}
	unset($k,$v);
	//定义字符串结尾所包括的字符串
	$stringToBeSigned .= $msign;
	//使用MD5进行加密，再转化成大写
	return strtoupper(md5($stringToBeSigned));
}
/* 打印模版 */
//前台 Print Templates
function RecPT($a){
	$modelstr = "
@@2" . str_pad1($a['bname'],35,' ',STR_PAD_BOTH) . "
● 号码：" . $a['desknum'] . "
单号：" . $a['ordernum'] . "
时间：" . $a['addtime'] . "

名称　　　　　　数量　　金额
******************************* 
";
	for($i = 0;$i < count($a['caidan']);$i++){
		$modelstr .= $a['caidan'][$i]['name'] . $a['caidan'][$i]['num'] . "      " . $a['caidan'][$i]['price'] . "\n (" . $a['caidan'][$i]['stand'] . ")";
		if($i != count($a['caidan']) - 1)
			$modelstr .= "\n";
	}
	//*******************************
	$modelstr .= "
******************************* 
　　　　　　合计：　￥" . $a['totalmoney'] . "
　　　　　　应付：　￥" . $a['totalmoney'] . "
　　　　　　优惠：　￥" . $a['youhui'] . "
　　　　　　实付：　￥" . $a['shifu'] . "
        ";
	return $modelstr;
}
//厨房
function KitPT($a){
	$modelstr = "
@@2" . str_pad1($a['bname'],35,' ',STR_PAD_BOTH) . "
● 号码：" . $a['desknum'] . "
单号：" . $a['ordernum'] . "
时间：" . $a['addtime'] . "

名称　　　　　　　　　数量
******************************* 
";
	for($i = 0;$i < count($a['caidan']);$i++){
		$modelstr .= $a['caidan'][$i]['name'] . "       " . $a['caidan'][$i]['num'] . "\n (" . $a['caidan'][$i]['stand'] . ")";
		if($i != count($a['caidan']) - 1)
			$modelstr .= "\n";
	}
	return $modelstr;
}
//YPay打印模板
function printTemplate($a){
	$modelstr = "
@@2" . str_pad1($a['bname'],35,' ',STR_PAD_BOTH) . "
\n*******************************\n
支付金额：￥" . $a['totalmoney'] . "
支付时间：" . $a['paytime'] . "
交易单号：\n" . $a['ordernum'] . "
\n*******************************\n
        ";
	return $modelstr;
}
function printers($smg, $num, $key){
	//$smg = I('smg');
	//$num = I('num');
	//$key = I('key');
	$msg = $smg; //打印内容
	$apiKey = "ef3e946c8dd29974054bcacfa30d93f754ae2bcf"; //apiKey
	$mKey = $key; //秘钥
	$partner = "3009"; //用户id
	$machine_code = $num; //打印机终端号
	$ti = time();
	$params = array('partner' => $partner, 
		'machine_code' => $machine_code, 
		'time' => $ti);
	$sign = generateSign($params,$apiKey,$mKey);
	$params['sign'] = $sign;
	$params['content'] = $msg;
	$url = 'open.10ss.net:8888'; //接口端点
	$p = '';
	foreach($params as $k => $v){
		$p .= $k . '=' . $v . '&';
	}
	$data = rtrim($p,'&');
	$status = liansuo_post($url,$data);
	$statusarr = json_decode($status,true);
	switch($statusarr['state']){
		case 1:
			$res['statusCode'] = 1;
			$res['message'] = '打印成功！';
			break;
		case 2:
			$res['statusCode'] = 2;
			$res['message'] = '提交时间超时！';
			break;
		case 3:
			$res['statusCode'] = 3;
			$res['message'] = '参数有误';
			break;
		case 4:
			$res['statusCode'] = 4;
			$res['message'] = '签名加密验证失败！';
			break;
		default:
			break;
	}
	if($res['statusCode'] == 1){
		return true;
	}else{
		return false;
	}
}
//当天订单统计
function DayCount($type){
	//$type 0 表示 统计订单数量1 表示统计订单的金额
	$T_orders = D("T_orders"); //实例化总订单表
	$dateTime = strtotime(date("Y-m-d"));
	$dateTimeOne = strtotime(date("Y-m-d")) + 24 * 60 * 60;
	$sql = "SELECT COUNT(OrdersOrderNum) AS Count_OrdersOrderNum,SUM(OrdersTotalMoney) AS SumOrdersOrderNum FROM mc_t_orders WHERE OrdersAddtime >= $dateTime AND OrdersAddtime <=$dateTimeOne AND Bid='" . session("BusinID") . "' AND OrdersType='2'";
	$info = $T_orders->query($sql);
	if($type == 0){
		return $info[0]["Count_OrdersOrderNum"];
	}else{
		return empty($info[0]["SumOrdersOrderNum"]) ? 0 : $info[0]["SumOrdersOrderNum"];
	}
}
function substr_cut($user_name){
	$strlen = mb_strlen($user_name, 'utf-8');
	$firstStr = mb_substr($user_name, 0, 1, 'utf-8');
	$lastStr = mb_substr($user_name, -1, 1, 'utf-8');
	return $strlen == 2 ? $firstStr . str_repeat('*', mb_strlen($user_name, 'utf-8') - 1) : $firstStr . str_repeat("*", $strlen - 2) . $lastStr;
}
function str_pad_unicode($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT) {
	$str_len = mb_strlen($str);
	$pad_str_len = mb_strlen($pad_str);
	if (!$str_len && ($dir == STR_PAD_RIGHT || $dir == STR_PAD_LEFT)) {
		$str_len = 1; // @debug
	}
	if (!$pad_len || !$pad_str_len || $pad_len <= $str_len) {
		return $str;
	}
	$result = null;
	$repeat = ceil($str_len - $pad_str_len + $pad_len);
	if ($dir == STR_PAD_RIGHT) {
		$result = $str . str_repeat($pad_str, $repeat);
		$result = mb_substr($result, 0, $pad_len);
	} else if ($dir == STR_PAD_LEFT) {
		$result = str_repeat($pad_str, $repeat) . $str;
		$result = mb_substr($result, -$pad_len);
	} else if ($dir == STR_PAD_BOTH) {
		$length = ($pad_len - $str_len) / 2;
		$repeat = ceil($length / $pad_str_len);
		$result = mb_substr(str_repeat($pad_str, $repeat), 0, floor($length))
		. $str
		. mb_substr(str_repeat($pad_str, $repeat), 0, ceil($length));
	}
	return $result;
}
function str_pad1($input, $pad_length, $pad_string, $pad_type){
	$strlen = (strlen($input) + mb_strlen($input,'UTF8')) / 2;
	if($strlen < $pad_length){
		$difference = $pad_length - $strlen;
		switch($pad_type){
			case STR_PAD_RIGHT:
				return $input . str_repeat($pad_string,$difference);
				break;
			case STR_PAD_LEFT:
				return str_repeat($pad_string,$difference) . $input;
				break;
			default:
				$left = $difference / 2;
				$right = $difference - $left;
				return str_repeat($pad_string,$left) . $input . str_repeat($pad_string,$right);
				break;
		}
	}else{
		return $input;
	}
}
// Filename 文件名称
// Data 保存的数据，二维数组(必须)
// ColName 列名
function writeexcel($Filename = "", $Data = array(), $ColName = array()){
	// 导入写Excel文件类库
	import("Org.Util.PHPExcel");
	import("Org.Util.PHPExcel.Cell");
	// 格式xls
	import("Org.Util.PHPExcel.Writer.Excel2007");
	$Writer = new \PHPExcel_Writer_Excel2007();
	$PHPExcel = new \PHPExcel();
	$ActSheet = $PHPExcel->setActiveSheetIndex(0);
	$ActSheet->setTitle("Sheet1");
	$Letter = ord("A");
	$i = 0;
	// 设置列名
	foreach($ColName as $key => $v){
		$ActSheet->setCellValue(chr($Letter + $i) . "1",$v);
		$i++;
	}
	// 填写数据
	$row = 0;
	foreach($Data as $varr){
		$Letter = ord("A");
		$col = 0;
		foreach($varr as $v){
			$ActSheet->setCellValue(chr($Letter + $col) . ($row + 2),$v);
			$col++;
		}
		$row++;
	}
	$PHPExcel->createSheet();
	$Writer = \PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');
	$userbrowser = $_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/MSIE/i',$userbrowser)){
		$file_name = urlencode($Filename);
	}else{
		$file_name = $Filename;
	}
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
	header("Content-Type:application/force-download");
	header("Content-Type:application/vnd.ms-execl");
	header("Content-Type:application/octet-stream");
	header("Content-Type:application/download");
	header('Content-Disposition:attachment;filename="' . $file_name . '.xlsx"');
	header("Content-Transfer-Encoding:binary");
	ob_clean();
	$Writer->save('php://output');
}
/**
 *  导出
 * @param string $path ：导出文件指定存储路径
 * @param sting  $Filename 文件名
 * @param array $Data  数据
 * @param array $ColName 字段
 */
function mwriteexcel($path, $Filename = "", $Data = array(), $ColName = array()){
	// 导入写Excel文件类库
	import("Org.Util.PHPExcel");
	import("Org.Util.PHPExcel.Cell");
	// 格式xls
	import("Org.Util.PHPExcel.Writer.Excel2007");
	$Writer = new \PHPExcel_Writer_Excel2007();
	$PHPExcel = new \PHPExcel();
	$ActSheet = $PHPExcel->setActiveSheetIndex(0);
	$ActSheet->setTitle("Sheet1");
	$Letter = ord("A");
	$i = 0;
	// 设置列名
	foreach($ColName as $key => $v){
		$ActSheet->setCellValue(chr($Letter + $i) . "1",$v);
		$i++;
	}
	// 填写数据
	$row = 0;
	foreach($Data as $varr){
		$Letter = ord("A");
		$col = 0;
		foreach($varr as $v){
			$ActSheet->setCellValue(chr($Letter + $col) . ($row + 2),$v);
			$col++;
		}
		$row++;
	}
	$PHPExcel->createSheet();
	$Writer = \PHPExcel_IOFactory::createWriter($PHPExcel,'Excel2007');
	ob_clean();
	$Writer->save($path . $Filename . '.xlsx');
}
/**
 * 获取商户名称
 * @param  busID
 */
function getBusName($busId){
	$mod = D("Businfo");
	$re = $mod->field("bus_Name")->where(array(
		"bus_ID" => $busId))->find();
	return $re['bus_Name'];
}
/**
 * 益支付折扣比例
 */
function getYPayDiscount($yPayID){
	$mod = D("Ypayorders");
	$info = $mod->where(array(
		"YPayID" => $yPayID))->find();
	if($info['YPayIsDiscount'] == 2){
		$data = $info['YPayDiscount'] / 10 . "折";
	}else{
		$data = "不打折";
	}
	return $data;
}
/**
 * 点餐折扣比例
 */
function getSpotYPayDiscount($SoID){
	$mod = D("Scanorders");
	$info = $mod->where(array("SoID" => $SoID))->find();
	if($info['SoIsDazhe'] == 2){
		$data = $info['SoDazheBL'] / 10 . "折";
	}else{
		$data = "不打折";
	}
	return $data;
}
//百度语音接口
function getYy($content){
	header("Content-Type:text/html;charset=utf-8");
	$client_id = "zr0fLOOnd9AqxIrlVvIM6GOV";
	$client_secret = "5a6337a24401496cec0c62165b368169";
	$url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id={$client_id}&client_secret={$client_secret}&";
	$tk = json_decode(file_get_contents($url),"true");
	$params = array('tex' => $content, 
		'tok' => $tk['access_token'], 'ctp' => 1, 
		'cuid' => 2, 'spd' => 6, 'pit' => 5, 
		'vol' => 9, 'per' => 0);
	$url = "http://tsn.baidu.com/text2audio?tex=" . $params['tex'] . "&lan=zh&cuid=" . $params['cuid'] . "&ctp=" . $params['ctp'] . "&tok=" . $params['tok'] . "&spd=" . $params['spd'] . "&pit=" . $params['pit'] . "&vol=" . $params['vol'] . "&per=" . $params['per'];
	//echo "<script>window.open('$url','newwindow','height=1,width=1,top=0,left=0,toolbar=no,menubar=no,scrollbars=no,resizeable=no,location=no,status=no')</script>";
	return $url;
}
//获取商户区域
function AcquisitionArea($busCityArea){
	$cityaID = explode(",",$busCityArea);
	$mod = D('T_city as city');
	$where = array(
		array("city.f_b_CityID" => $cityaID[0]), 
		array("area.f_b_AreaID" => $cityaID['1']));
	$join = "LEFT JOIN mc_t_area as area on city.f_b_CityID = area.f_b_CityID";
	$city = $mod->join($join)->where($where)->find();
	if($city){
		return $city['f_b_CityName'] . "-" . $city['f_b_AreaName'];
	}else{
		return false;
	}
}
//获取经营类别
function busScope($ScopeID){
	$info = D("T_bus_scope")->field("f_b_ScopeName")->where(array(
		"f_b_ScopeID" => $ScopeID))->find();
	return $info['f_b_ScopeName'];
}
//外卖打印模板开始
//前台 Print Templates
function TakeRecPT($a){
	$modelstr = "
@@2" . str_pad1($a['bname'],35,' ',STR_PAD_BOTH) . "
单号：" . $a['ordernum'] . "
时间：" . $a['addtime'] . "
备注：" . $a["Remark"] . "

名称　　　　　　数量　　金额
*******************************
";
	for($i = 0;$i < count($a['caidan']);$i++){
		$modelstr .= $a['caidan'][$i]['name'] . $a['caidan'][$i]['num'] . "      " . $a['caidan'][$i]['price'] . "\n(" . $a['caidan'][$i]['stand'] . ")";
		if($i != count($a['caidan']) - 1)
			$modelstr .= "\n";
	}
	//*******************************
	$modelstr .= "
*******************************
　　　　　　合计：　￥" . $a['totalmoney'] . "
　　　　　　应付：　￥" . $a['totalmoney'] . "

配送信息：" . $a['addressInfo'] . "
        ";
	return $modelstr;
}
//厨房
function TakeKitPT($a){
	$modelstr = "
@@2" . str_pad1($a['bname'],35,' ',STR_PAD_BOTH) . "
单号：" . $a['ordernum'] . "
时间：" . $a['addtime'] . "
备注：" . $a["Remark"] . "

名称　　　　　　　　　数量
*******************************
";
	for($i = 0;$i < count($a['caidan']);$i++){
		$modelstr .= $a['caidan'][$i]['name'] . "       " . $a['caidan'][$i]['num'] . "\n(" . $a['caidan'][$i]['stand'] . ")";
		if($i != count($a['caidan']) - 1)
			$modelstr .= "\n";
	}
	$modelstr .= "
*******************************
配送信息：" . $a["addressInfo"] . "
    ";
	return $modelstr;
}
//外卖打印模板结束
function getDesk($desknum, $BusID){
	$mod = D("T_desknum");
	$where = array('f_b_Aid' => $BusID, 
		'f_d_DeskNum' => $desknum);
	$desk = $mod->field("f_d_DeskID ,f_d_DeskNum,f_d_DeskUp,f_d_DeskDown")->where($where)->find();
	if(preg_match('/\d/',$desknum)){
		$p = "号";
	}
	if(empty($desk['f_d_DeskUp'])){
		$dk = $desknum . $p . $desk['f_d_DeskDown'];
	}else{
		$dk = $desk['f_d_DeskUp'] . preg_replace('/\D/','',$desknum) . $p . $desk['f_d_DeskDown'];
	}
	return $dk;
}
function getPath($uid, $type){
	$filePath = array("./Uploads/", "Business/", 
		$uid . "/", $type . "/");
	for($i = 0;$i < count($filePath);$i++){
		$newFilePath .= $filePath[$i];
		if(!is_dir($filePath[$i]))
			mkdir($newFilePath);
	}
	return $newFilePath;
}
/**
 * 2017-06-19 加入 前后台调用 
 */
function getTradeType($key){
	switch($key){
		case "1001":
			return "刷卡支付收款";
			break;
		case "1002":
			return "微信支付收款";
			break;
		case "1010":
			return "支付宝支付收款";
			break;
		case "1013":
			return "云闪付支付收款";
			break;
		case "2001":
			return "T0取现";
			break;
		case "2012":
			return "TS取现";
			break;
	}
}
function getTradeStatus($key){
	switch($key){
		case "I":
			return "初始";
			break;
		case "S":
			return "成功";
			break;
		case "F":
			return "失败";
			break;
		case "U":
			return "未知卡";
			break;
	}
}
function getCardType($key){
	switch($key){
		case "D":
			return "借记卡";
			break;
		case "C":
			return "贷记卡";
			break;
		case "U":
			return "未知卡";
			break;
	}
}
function getLevel($key){
	switch($key){
		case "1":
			return "直属上级";
			break;
		case "2":
			return "间属上级";
			break;
		case "3":
			return "第三级上级";
			break;
	}
}
function getOutputAN($n){
	if(strlen($n) > 11){
		return "POS客户";
	}else{
		return getBusinessName($n,"id");
	}
}
function getReceiveAN($n){
	if(strlen($n) > 11){
		return getBusinessName($n,"agentNo");
	}else{
		return getBusinessName($n,"id");
	}
}
function getBusinessName($id, $field){
	$row = fRec("user",$field . "=" . $id,"busname name,phone");
	if($row){
		return $row['name'] . "-" . $row['phone'];
	}else{
		return "该代理商尚未绑定平台账户";
	}
}
function drawcashRemind($money){
	$phone = "15332711334";
	$content = "有一笔金额为" . $money . "元的提现需要审核";
	$flag = 0;
	$params = '';
	$argv = array('name' => '18748106798',  //必填参数。用户账号
		'pwd' => '41F6253C5F416D2D35406DD778AA',  //必填参数。（web平台：基本资料中的接口密码）
		'content' => '【小掌柜】' . $content,  //必填参数。发送内容（1-500 个汉字）UTF-8编码
		'mobile' => $phone, //必填参数。手机号码。多个以英文逗号隔开
'stime' => '', //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
'sign' => '', //必填参数。用户签名。
'type' => 'pt', //必填参数。固定值 pt
'extno' => '');
	foreach($argv as $key => $value){
		if($flag != 0){
			$params .= "&";
			$flag = 1;
		}
		$params .= $key . "=";
		$params .= urlencode($value); // urlencode($value);
		$flag = 1;
	}
	$url = "http://web.cr6868.com/asmx/smsservice.aspx?" . $params; //提交的url地址
	$con = substr(file_get_contents($url),0,1); //获取信息发送后的状态
}
function getTerminal($bid, $tableName = '', $isActive = ''){
	$where = array('bid'=>$bid);
	if(!empty($isActive)){
		$where['isActive'] = $isActive;
	}
	$terminal = sRec('terminal_manage',$where,'','','');
	if($terminal){
		$newArr = array();
		$newArr1 = array();
		for($i=0;$i<count($terminal);$i++){
			$tName = M('commodity_category')->where('id=(select category_id from p_commodity where id = '.$terminal[$i]['proid'].')')->getField('ruleList');
			$newArr[$i]['tableName'] = str_replace("Cashback/","posdata_",$tName);
			$newArr[$i]['terminalNo'] = $terminal[$i]['terminal'];
			if($tableName == $newArr[$i]['tableName'] || $tableName == str_replace("Cashback/","",$tName)){
				$newArr1 = $newArr[$i];
			}
		}
		if(!empty($tableName)){
			return $newArr1;
		}else{
			return $newArr;
		}
	}
	return false;
}