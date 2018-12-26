<?php
/**
 * Created by PhpStorm.
 * User: 宁
 * Date: 2015/9/2
 * Time: 11:54
 * 权限Model
 */
namespace Admin\Model;

use Think\Model;

class FuncModel extends Model
{
    /**
     * 删除文件
     *  */
    function unlink($path)
    {
        @unlink('.'.$path);
    }
    /**
     * Undocumented function
     * 上传文件
     * @param [type] $path
     * @param [type] $fileUrl
     * @return void
     */
    public function DoUpload($path,$fileUrl)
    {
        if (!is_dir($path)) {
            @mkdir('.' . $path, 0777, true);
        }
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728; // 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
        $upload->rootPath = "." . $path; // 设置附件上传根目
        $upload->savePath = ''; // 设置附件上传（子）目录
        // 上传文件
        return $upload->uploadOne($fileUrl);
    }

    public function DoUploads($path,$fileurls)
    {
        if (!is_dir($path)) {
            @mkdir('.' . $path, 0777, true);
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =      '.'.$path; // 设置附件上传根目录
        $upload->savePath  =      ''; // 设置附件上传（子）目录
        // 上传文件 
        return $upload->upload($fileurls);
    }
    /**
     * 添加日志
     * @param [type] $event
     * @param [type] $type=true/false
     * @return void
     */
    public function addLog($event,$type)
    {
        aLog("用户名为：" . session("UserName") . "于 " . date("Y-m-d H:i:s") . " IP为： " . get_client_ip() . "添加项目名称为 " . $event . "保存成功", $type);
    }
    
}