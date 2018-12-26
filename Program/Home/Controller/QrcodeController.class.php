<?php
/*=============================================================================
 #
 # Author: hojk - hojk@foxmail.com
 #
 # Last modified: 2016-03-31 10:01
 #
 # Filename: SalesmanQrcodeController.class.php
 #
 # Description: 业务员二维码
 #
 =============================================================================*/
namespace Home\Controller;
use Think\Controller;
class QrcodeController extends CommonController{
	public function index(){
		$bid = $this->mid;
		$row = fRec("business","id=" . $bid,"name,platform_id");
		$filePath = self::getPath($bid);
		$headimg = "./Public/sft/images/codeIcon.png";
		$qrcode = $filePath . "qrcode.png";
		$bgimg = $filePath . "bgimg.jpg";
		$par = parent::encode("pid:{$row['platform_id']}|tid:{$bid}");
		$text = BASEURL . "/index.php/Home/Index/Register/regParam/" . $par;
		if(!is_file($qrcode)){
			$this->createImg($bid,$headimg,$text);
		}
// 		if(!is_file($bgimg)){
			$text = "我是".$row['name']."，我为瀚银科技代言";
			$this->mergedImages($qrcode,$bgimg,$text);
// 		}
		$imgSrc = BASEURL . ltrim($bgimg,".");
		return $imgSrc;
	}
	protected function saveHeadimg($img, $path){
		if(substr($img,0,4) == 'http'){
			// 设置运行时间为无限制
			set_time_limit(0);
			$url = trim($img);
			$curl = curl_init();
			// 设置你需要抓取的URL
			curl_setopt($curl,CURLOPT_URL,$url);
			// 设置header
			curl_setopt($curl,CURLOPT_HEADER,0);
			// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
			curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
			// 运行cURL，请求网页
			$file = curl_exec($curl);
			// 关闭URL请求
			curl_close($curl);
			// 将文件写入获得的数据
			$filename = $path . "headimg.jpg";
			$write = @fopen($filename,"w");
			if($write == false){
				return false;
			}
			if(fwrite($write,$file) == false){
				return false;
			}
			if(fclose($write) == false){
				return false;
			}
		}else{
			$filename = $path . "headimg.jpg";
			file_put_contents($filename,file_get_contents($img));
		}
	}
	//合并二维码和背景
	protected function mergedImages($qr, $savePath,$text = ""){
		$first = "./Public/sft/images/codeImg.jpg";
		$x = 160;
		$y = 600;
		$qrSize = "430";
		$headSize = "55";
		$firstImg = imagecreatefromstring(file_get_contents($first));
		$qrImg = imagecreatefromstring(file_get_contents($qr));
		$firstAttr = getimagesize($first);
		$firstWidth = $firstAttr[0];
		$firstHeight = $firstAttr[1];
		$qrAttr = getimagesize($qr);
		$qrWidth = $qrAttr[0];
		$qrHeight = $qrAttr[1];
		$qrWidth = $qrAttr[0];
		$qrHeight = $qrAttr[1];
		$QR = imagecreatetruecolor($firstWidth,$firstHeight);
		imagecopyresampled($QR,$firstImg,0,0,0,0,$firstWidth,$firstHeight,$firstWidth,$firstHeight);
		imagecopyresampled($QR,$qrImg,$x,$y,0,0,$qrSize,$qrSize,$qrWidth,$qrHeight);
		//var_dump($firstQRAttr);
		if(!empty($text)){
			$remarkColor = imagecolorallocate($QR,14,122,255);
			$str = $text;
			$strlen = floor(strlen($str)/3);
			//4.35 取字数占满屏幕 每个字占比
			$everyWidth = round($firstWidth / 100 * 4.35,2);
			//计算居中向左偏移量
			$remarkLeft = round(($firstWidth - round($strlen * $everyWidth,2))/2,2);
			imagettftext($QR,25,0,$remarkLeft,555,$remarkColor,"./Public/ttf/msyh1.ttf",$str); 
		}
		//header('Content-type: image/jpg');
		//imagejpeg($QR);
		imagejpeg($QR,$savePath);
	}
	//获取文件路径
	protected static function getPath($openid){
		$filePath = array(
			"./Uploads/", 
			"shareQrcode/", 
			md5($openid) . "/"
		);
		for($i = 0;$i < count($filePath);$i++){
			$newFilePath .= $filePath[$i];
			if(!is_dir($filePath[$i]))
				mkdir($newFilePath);
		}
		@chmod($newFilePath,0777);
		return $newFilePath;
	}
	//生成二维码图片 
	function createImg($bid, $headimg, $qrtext){
		require ('./Public/ttf/class/qrcode.php');
		$value = $qrtext;
		$errorCorrectionLevel = 'M';
		$matrixPointSize = 7;
		$ratio = $matrixPointSize * 10;
		$remarkLeft = $matrixPointSize * 11;
		$remarkTop = $matrixPointSize * 5;
		$cornerSize = 5;
		$filename = $this->getPath($bid) . 'qrcode.png';
		\QRcode::png($value,$filename,$errorCorrectionLevel,$matrixPointSize,1);
		$qrcode = file_get_contents($filename);
		$qrcode = imagecreatefromstring($qrcode);
		$qrcode_width = imagesx($qrcode);
		$qrcode_height = imagesy($qrcode);
		$QR = imagecreatefromstring(file_get_contents($filename));
		$firstQRAttr = getimagesize($filename);
		$firstQRWidth = $firstQRAttr[0];
		$firstQRHeight = $firstQRAttr[1]; //+ $ratio;   
		$qrcode = imagecreatetruecolor($firstQRWidth,$firstQRHeight);
		imagecopyresampled($qrcode,$QR,0,0,0,0,$firstQRWidth,$firstQRHeight,$firstQRWidth,$firstQRHeight);
		//$remarkColor = imagecolorallocate($qrcode,0,0,0);
		//imagettftext($qrcode,$ratio/4,0,$remarkLeft,$firstQRHeight-$remarkTop,$remarkColor,"./Public/ttf/msyh.ttf","微信扫码支付"); 
		$corner = file_get_contents('./Public/ttf/ARzuuy.png');
		$corner = imagecreatefromstring($corner);
		$corner_width = imagesx($corner);
		$corner_height = imagesy($corner);
		$corner_qr_height = $corner_qr_width = $qrcode_width / $cornerSize;
		$from_width = ($qrcode_width - $corner_qr_width) / 2;
		imagecopyresampled($qrcode,$corner,$from_width,$from_width,0,0,$corner_qr_width,$corner_qr_height,$corner_width,$corner_height);
		//logo图片
		// 		if(empty($headimg)){
		// 			$logo = file_get_contents('./Public/images/logo_' . $pid . '.png');
		// 		}else{
		$logo = file_get_contents($headimg);
		// 		}
		$logo = imagecreatefromstring($logo);
		//$logo = imagecreate(200,200);
		//$bgColor= imagecolorallocate($logo,242,242,242);
		//imagecolortransparent($logo,$bgColor); 
		//$fontColor = imagecolorallocate($logo,0,186,255);
		//imagettftext($logo,80,0,$left,145,$fontColor,"./Public/ttf/msyh.ttf",$desknum[$i]); 
		$logo_width = imagesx($logo);
		$logo_height = imagesy($logo);
		$logo_qr_height = $logo_qr_width = $qrcode_width / 5 - 6;
		$from_width = ($qrcode_width - $logo_qr_width) / 2;
		imagecopyresampled($qrcode,$logo,$from_width,$from_width,0,0,$logo_qr_width,$logo_qr_height,$logo_width,$logo_height);
		//header("Content-Type: image/png");
		//imagepng($qrcode);
		imagepng($qrcode,$filename);
		//return $filename;
	}
}
?>
