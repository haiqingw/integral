<?php
#================================================
# Author: hojk - hojk@foxmail.com
# Date: 2016年10月24日 下午3:48:39
# Filename: HPay.config.php
# Description: 华付通配置文件
#================================================
header("Content-Type:text/html;charset=utf-8");
define("PUB_TTF","./Public/ttf/class/");
define("P_URL","http://pay.xylrcs.cn");
define("T_URL","http://paytest.xylrcs.cn");
define("QD_ID","1000000005");
define("BUS_ID","789303288242769920");
define("PAR_ID","0000000001");
define("SER_URL","http://218.17.162.237:8088/connector/gateway/");
define("C_F","/index.php/EasyApp/SecondsTo/");
$date = date('YmdHis');
define("reqTime",$date);
define("LS_NUM","LS" . $date);
?>