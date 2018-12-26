<?php
define("JD_INTER_URL", "https://aliyun-bankcard4-verify.apistore.cn/bank4");
define("VERIFY_KEY", "bfd415ae9dc444689ed44398f172af25s");
class AliyunBankcard
{
	/**
	 * 设置参数
	 * @param string $key
	 * @param string $val
	 */
	public function setParams($key, $val)
	{
		$this->params[$key] = trim($val);
	}
	/**
	 * URL提交参数格式化
	 * @date: 2018年1月31日 下午5:29:13
	 * @author: HaiQing.Wu <398994668@qq.com>	
	 */
	public function paramsFormat($params)
	{
		$o = "";
		foreach ($params as $k => $v) {
			$o .= "$k=" . urlencode($v) . "&";
		}
		return substr($o, 0, -1);
	}
	/**
	 * get进行url请求
	 * @param string $url
	 * @param array $post_data
	 */
	public function curlRequestce($url, $data = null, $second = 30)
	{
		$headers = array();
		array_push($headers, "Authorization:APPCODE " . VERIFY_KEY);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, true);
		if (1 == strpos("$" . $host, "https://")) {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		}
		return curl_exec($curl);
	}
	/**
	 * 检测元素是否正确
	 * 只能检测一维数组
	 * @param $arr 被检测的数组
	 * @param $repar $arr中必要的元素(键名)
	 * @return boolean
	 */
	protected function checkParams($arr, $repar = array())
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
}
/**
 * 银行卡验证
 * @date: 2018年1月31日 下午4:15:04
 * @author: HaiQing.Wu <398994668@qq.com>
 */
class BankCard extends AliyunBankcard
{
	protected $params = [
		'Mobile' => '',
		'bankcard' => '',
		'cardNo' => '',
		'realName' => '',
	];
	/**
	 * 四要素验证 
	 * @date: 2018年1月31日 下午3:55:10
	 * @author: HaiQing.Wu <398994668@qq.com>
	 * @param:
	 * @return:
	 */
	public function handle()
	{
		//获取用户信息
		$checkParams = $this->checkParams($this->params);
		if (!$checkParams) {
			throw new MyException("缺少信息，补全");
		}
		$url = JD_INTER_URL . "?" . $this->paramsFormat($this->params);
		$jsonData = $this->curlRequestce($url);
		file_put_contents("./bankverify", $jsonData);
		$jsonDatarow = json_decode($jsonData, true);
		if ($jsonDatarow['error_code'] == 0) {
			$status = array(
				"status" => 1,
				"msg" => "认证成功"
			);
		} else {
			$status = array(
				"status" => 2,
				"msg" => $jsonDatarow['reason']
			);
		}
		return $status;
	}
}