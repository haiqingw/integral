<?php
/**
 * Created by PhpStorm.
 * User: Ning.an
 * Date: 2016/6/23
 * Time: 9:55
 * Email: ai_yuem@aliyun.com
 * Address:anchina.net
 * 设备信息Model
 */
namespace Admin\Model;

use Think\Model\ViewModel;

class DeviceinfoViewModel extends ViewModel
{
    public $viewFields = array(
            "Deviceinfo" => array(
                "DeviceInfo_ID",
                "DeviceInfo_VersionNumber",
                "DeviceInfo_UniqueIdentification",
                "DeviceInfo_AppVersionNumber",
                "DeviceInfo_OperatorsName",
                "DeviceInfo_NetworkType",
                "DeviceInfo_DeviceName",
                "DeviceInfo_DeviceType",
                "DeviceInfo_CreateTime"
        )
    );
}
