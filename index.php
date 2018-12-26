<?php
// +----------------------------------------------------------------------
//kkkk
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
//iii
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);
//// 绑定访问Home模块
//define('BIND_MODULE','Home');
// 绑定访问Login控制器
//define('BIND_CONTROLLER','Login');
// 绑定访问index操作
//define('BIND_ACTION','index');
// 定义应用目录
define('APP_PATH', './Program/');
define("WEB_URL", getcwd());
define('BASEURL', 'http://192.168.31.243:8086');
// define('BASEURL','http://ttsplus.xylrcs.cn');
define('TESTURL', 'http://srgc.xylrcs.cn');
//管理员后台将以下代码打开 商家后台注释
//define('BIND_MODULE','Admin'); 
//define('BIND_MODULE','GoldGEE'); 
//define('BIND_MODULE','Cooper'); 

// 引入ThinkPHP入口文件
require '../ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单
