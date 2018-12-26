<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2018年3月12日 下午3:54:06
# Filename: ImageManage.class.php
# Description: 图片管理类
#================================================
namespace Common\Api;
define("TIM", "images");
class ImageManage{
	/**
	 * 错误消息存放处
	 * @var string
	 */
	private $errMsg = "";
	/**
	 * 上传文件的键名
	 * @var string
	 */
	private $upKey = "file";
	/**
	 * 最大支持的文件大小:MB
	 * 3 * 1048576
	 * @var integer
	 */
	private $maxFileSize = 3;
	/**
	 * 允许的文件扩展名
	 * @var array
	 */
	private $allowedExtensions = array("jpg","png","gif");
	private $allowedExtensionsTypes = array('image/pjpeg' => "jpg",'image/jpeg' => "jpg",'image/jpg' => "jpg",'image/png' => "png",'image/x-png' => "png",'image/gif' => "gif");
	/**
	 * 上传错误原因
	 * @var array
	 */
	private $uploadErrorReason = array("0" => "upload success","1" => "上传的文件超过了upload_max_filesize限制的值","2" => "上传的文件超过了HTML表单中MAX_FILE_SIZE指定的值","3" => "文件只有部分被上传","4" => "没有文件被上传","6" => "找不到临时文件夹","7" => "文件写入失败");
	/**
	 * 检查上传文件是否存在
	 * @param array $file
	 * @return boolean
	 */
	private function checkUploadFileExists($file){
		if(empty($file)){
			$this->errMsg = "请选择一个文件上传";
			return false;
		}
		if($file[$this->upKey]['error'] != 0){
			$this->errMsg = $this->uploadErrorReason[$file[$this->upKey]['error']];
			return false;
		}
		return true;
	}
	/**
	 * 检查上传文件扩展支持
	 * @param array $file
	 * @return boolean
	 */
	private function checkUploadFileExtensionSupport($file){
		$ext = strtolower(ltrim(strrchr($file[$this->upKey]['name'], "."), "."));
		if(!in_array($ext, $this->allowedExtensions) || $this->allowedExtensionsTypes[$file[$this->upKey]['type']] != $ext){
			$this->errMsg = "只允许上传：" . implode("|", $this->allowedExtensions) . "格式的文件";
			return false;
		}
		return true;
	}
	/**
	 * 检查上传文件大小
	 * @param array $file
	 * @return boolean
	 */
	private function checkUploadFileSize($file){
		if($file[$this->upKey]['size'] > ($this->maxFileSize * 1048576)){
			$this->errMsg = "只允许上传 " . $this->max_file . "MB之内的文件";
			return false;
		}
		return true;
	}
	/**
	 * 文件上传
	 * @param string $temp
	 * @param string $save
	 * @return boolean
	 */
	private function moveUploadedFile($temp, $save){
		if(!move_uploaded_file($temp, $save)){
			$this->errMsg = "文件上传失败";
			return false;
		}
		return true;
	}
	/**
	 * 图片上传方法
	 */
	public function uploadImageToServer($file){
		$status = 0;
		$retData = array();
		if($this->checkUploadFileExists($file)){
			if($this->checkUploadFileExtensionSupport($file)){
				if($this->checkUploadFileSize($file)){
					$uploadPath = self::defaultImageStoragePath();
					$newFileName = $uploadPath . date("YmdHis") . mt_rand(10, 99) . strrchr($file[$this->upKey]['name'], ".");
					if($this->moveUploadedFile($file[$this->upKey]['tmp_name'], $newFileName)){
						//存储数据库 拿到ID
						$data = array("img_path" => $newFileName);
						if(cRec(TIM, $data)){
							$id = gFec(TIM, $data, "img_id");
						}else{
							$id = aRec(TIM, $data);
						}
						$status = 1;
						$retData['id'] = $id;
						$retData['url'] = $newFileName;
					}
				}
			}
		}
		return array("status" => $status,"data" => $retData,"msg" => $this->errMsg);
	}
	/**
	 * 图片删除方法
	 */
	public function delImageRecordAndFileThroughImgID($imgID){
		if($imgPath = $this->getImageStoragePathThroughAttribute($imgID)){
			dRec(TIM, "img_id=" . $imgID);
			if(is_file($imgPath)){
				unlink($imgPath);
			}
		}
		return false;
	}
	/**
	 * 获取多个图片路径(前台)
	 */
	public function getImagePathArray($imgID){
		if(empty($imgID)){
			return false;
		}else{
			$array = array();
			$ex = explode(",", $imgID);
			foreach ($ex as $val){
				$response = $this->getImageStoragePathThroughAttribute($val);
				if($response){
					$array[count($array)] = BASEURL . ltrim($response, ".");
				}
			}
			return $array;
		}
	}
	/**
	 * 图片获取方法
	 */
	public function getImageStoragePathThroughAttribute($imgID){
		if(empty($imgID)){
			return false;
		}else{
			$imgPath = gFec(TIM, "img_id=" . $imgID, "img_path");
			return $imgPath;
		}
	}
	/**
	 * 图片清理方法
	 */
	public function clearUselessImages(){
		$uselessList = sRec(TIM, array("_string" => "plat is null"), "", "", "");
		if($uselessList){
			for($i = 0; $i < count($uselessList); $i++){
				$this->delImageRecordAndFileThroughImgID($uselessList[$i]['img_id']);
			}
		}
	}
	/**
	 * 修改表属性
	 */
	public function modifyTIMAttribute($imgID, $table_name, $table_id, $plat){
		if(empty($imgID) || empty($table_name) || empty($table_id) || empty($plat)){
			return false;
		}else{
			$data = array("table_name" => $table_name,"table_id" => $table_id,"plat" => $plat);
			uRec(TIM, $data, "img_id=" . $imgID);
		}
	}
	/**
	 * 获取图片存储路径
	 * @return string
	 */
	private static function defaultImageStoragePath(){
		$defaultFilePath = array("./Uploads/","ImageStorage/",date('Y-m-d') . "/");
		for($i = 0; $i < count($defaultFilePath); $i++){
			$filePath .= $defaultFilePath[$i];
			if(!is_dir($defaultFilePath[$i]))
				mkdir($filePath);
		}
		return $filePath;
	}
}