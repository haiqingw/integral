<?php
return array(


    'URL_MODEL'   =>  1,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：
	
    'URL_CASE_INSENSITIVE' =>false, //表示URL访问不区分大小写
    
    //'DEFAULT_MODULE'=>'Admin',
    'DEFAULT_MODULE'=>'',
    'DURL'=>'/Uploads/',
	'TMPL_EXCEPTION_FILE' => '/404.html',
	'ERROR_PAGE'=>'/404.html',
    'DEFAULT_TIMEZONE'=>'PRC',
	//'TMPL_FILE_DEPR'=>'_',
   
	'SESSION_OPTIONS' =>  array('expire'=>36000),
	'SESSION_PREFIX'        =>  'mc', 
	
	 // 加载扩展配置文件 多个用,隔开
	'LOAD_EXT_CONFIG' => 'db', 
	
	//上传设置
	'UPLOAD_MAXSIZE'=>31457280,
	'UPLOAD_EXTS'=>array('jpg','gif','png','jpeg','txt','doc','docx','xls','xlsx','ppt','pptx','pdf','rar','zip','wps','wpt','dot','rtf','dps','dpt','pot','pps','et','ett','xlt'),// 设置附件上传类型 
	'UPLOAD_SAVEPATH'=>'./Public/',

	"webTitle" => "在线POS交易分销系统",
	"webLinkTel" => "0471-3484526",
	"webBeian" => "蒙ICP备15001441号-3",
	"webPublic" => "/Public/sft/",
	//商户等级 编号
	"buslevel" => array(
		"1" => "普通用户",
		"2" => "服务商",
		"3" => "代理商",
		"4" => "用户商",
		"5" => "业务员"
	),
	"buscash" => array(
		"1" => "交易分润",
		"2" => "招商补贴",
		"3" => "激活返现",
		"4" => "交易分红",
		"5" => "股份分红"
	),
	"buscard" => array(
		"1" => "借记卡",
		"2" => "贷记卡",
		"3" => "准贷记卡",
	),
);
