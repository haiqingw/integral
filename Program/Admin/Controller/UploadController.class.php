<?php

namespace Admin\Controller;

class UploadController extends CommonController {


    function uploadimg() { // 上传图片
        $upload = new \Think\Upload();
        $upload->rootPath = '.'.C("DURL"); // 设置附件上传根目录
        $upload->savePath = "ueditor/image/";
        $info = $upload->upload();
        if ($info) {
            $ajax = array(
                "state" => "SUCCESS",
                "url" => C("DURL") . $info['upfile']['savepath'] . $info['upfile']['savename'],
                "title" => $info['upfile']['savename'],
                "original" => $info['upfile']['name'],
                "type" => $info['upfile']['type'],
                "size" => $info['upfile']['size'],
            );
            $this->tempfile(__ROOT__ . $info['upfile']['savepath'] . $info['upfile']['savename']);
        } else {
            $ajax = array(
				"path" => $upload->rootPath,
                "state" => "",
				"message" => $upload->getError(),
            );
        }
        $this->ajaxReturn($ajax);
    }

    public function uploadfile() { // 上次文件
        $upload = new \Think\Upload();
        $upload->rootPath = "./Public/";
        $upload->savePath = "/LrUploads/files/";
        $info = $upload->upload();
        if ($info) {
            $ajax = array(
                "original" => $info['upfile']['name'],
                "size" => $info['upfile']['size'],
                "state" => "SUCCESS",
                "title" => $info['upfile']['savename'],
                "type" => $info['upfile']['type'],
                "url" => __ROOT__ . '/Public' . $info['upfile']['savepath'] . $info['upfile']['savename'],
            );
            $this->tempfile(__ROOT__ . '/Public' . $info['upfile']['savepath'] . $info['upfile']['savename']);
        } else {
            $ajax = array();
        }
        $this->ajaxReturn($ajax);
    }

    public function uploadvideo() { // 上传视频
        $upload = new \Think\Upload();
        $upload->rootPath = "./Public/";
        $upload->savePath = "/LrUploads/videos/";
        $info = $upload->upload();
        if ($info) {
            $ajax = array(
                "original" => $info['upfile']['name'],
                "size" => $info['upfile']['size'],
                "state" => "SUCCESS",
                "title" => $info['upfile']['savename'],
                "type" => $info['upfile']['type'],
                "url" => __ROOT__ . '/Public' . $info['upfile']['savepath'] . $info['upfile']['savename'],
            );
        }
        $this->tempfile(__ROOT__ . '/Public' . $info['upfile']['savepath'] . $info['upfile']['savename']);
        $this->ajaxReturn($ajax);
    }

    public function uptofile() { // 保存涂鸦图片
        $imgbase64 = $_POST["upfile"];

        $img = base64_decode(str_replace($result[1], '', $imgbase64));

        $fileSize = strlen($img);
        $uppath = "Public/LrUploads/image/" . date("Y-m-d") . "/";
        if (is_dir("./" . $uppath)) {
            $filename = md5(session("uid") . time() . rand(6)) . ".png";
            if (file_put_contents($uppath . $filename, $img)) {
                $ajax = array(
                    "original" => __ROOT__ . "/" . $uppath . $filename,
                    "size" => $fileSize,
                    "state" => "SUCCESS",
                    "title" => $filename,
                    "type" => "png",
                    "url" => __ROOT__ . "/" . $uppath . $filename,
                );
                $this->tempfile(__ROOT__ . "/" . $uppath . $filename);
            }
        } else {
            if (mkdir("./" . $uppath, 0777, true)) {
                $filename = md5(session("uid") . time() . rand(6)) . ".png";
                if (file_put_contents($uppath . $filename, $img)) {
                    $ajax = array(
                        "original" => __ROOT__ . "/" . $uppath . $filename,
                        "size" => $fileSize,
                        "state" => "SUCCESS",
                        "title" => $filename,
                        "type" => "png",
                        "url" => $filename,
                    );
                    $this->tempfile(__ROOT__ . "/" . $uppath . $filename);
                }
            } else {
                $ajax = array(
                    "state" => "文件上传出错",
                );
            }
        }
        $this->ajaxReturn($ajax);
    }

    // 保存文件到临时文件表中
    private function tempfile($filename = "") {

        if (empty($filename))
            return;
        $Tempfiles = D("tempfiles");
        $uid = session("uid");
        $state = $Tempfiles->add(array(
            'tempfilefilename' => $filename,
            'tempfileuid' => $uid,
        ));
        //dump($state);exit;
    }

}
