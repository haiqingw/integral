<?php
header("Content-Type: text/html; charset=UTF-8");
define("SUBMIT_URL", "http://web.cr6868.com/asmx/smsservice.aspx"); //提交地址
define("USER_ACCOUNT", "18748106798"); //用户账号
define("INTERFACE_PASSWORD", "41F6253C5F416D2D35406DD778AA"); //接口密码
define("DB_TTP_DB_NAME", "ttsplus");
define("SMST_T", "smstemplates"); //短信模板管理表
define("SMSR_T", "smsrecord"); //短信记录表
define("DB_NAME_TTP", "ttsplus");
define("PREFIX", C('DB_PREFIX'));
define("T_BUS", "user");
define("T_BNK", "bankcard_list");
define("T_BIMP", "important");
define("T_CAPC", "changes_funds");
define("T_SYS", "system");
define("T_CASHB", "cash_back_log");
define("T_HCEN", "helpcenter");
define("T_RTPRE", "real_time_payment_records");
define("T_DCWWW", "drawcash");
define("T_CHANAL", "channel_limit");
define("T_COMM_CATE", "commodity_category");
define("T_COMMODITY", "commodity");
define("T_IMG", "images");
define("T_HMO", "homemodule");
define("T_TM", "terminal_manage");
define("T_EXL_MOMU", "exclu_module"); //专享模块管理
define("T_EXDISPM", "exclv_display_perm_manange"); //专享模块管理
define("T_ICOMEDPM", "income_display_perm_manange");//商户我的收益显示权限管理表
define("T_UHC", "userhelpclass"); //用户帮助分类管理
define("T_UHM", "helpmanage"); //用户帮助管理表
define("T_LA", "larize"); //广码生成背景图上传管理表
define("T_TIPS", "tips"); //资讯管理表
define("T_CLA_TIPS", "tips_class"); //资讯分类表
define("T_ACTI", "activity"); //活动管理表
define("T_ACTI_CLA", "activity_class"); //活动管理表
define("T_ADVER", "advertis"); //广告位图片信息
define("T_ADVER_CLA", "ad_class"); //广告分类管理表
define("T_IMPOR", "important"); //商户账户余额管理表
define("T_COMMENT_BUS", "comment"); //商户评论管理表
define("T_SERA", "service_application"); //服务商申请表
define("T_SPP_ADDRE", "shipping_address"); //收货地址管理表
define("T_ORDER_TAB", "order"); //订单表
define("T_CERT", "verified"); //实名证件审核认证管理表
define("T_BAMA", "cer_audit"); //商户审核管理表
define("T_DRAW_SET", "draw_set"); //提现设置
define("T_WDM", "wd_manage"); //提现管理表
define("T_BSIGN", "bus_sign"); //签到分享表
define("T_CABL", "cash_back_log"); //
define("T_BCMCLASS", "bus_cashback_manage_class");//商户返现类型管理表
define("T_BLMCS", "bus_level_manage"); //商户等级分类管理表
define("T_UPGI", "upgrade_introduce");
/*-----------------------*/
define("DEFAULT_ORDER_MONEY", "0.00"); //默认订单金额
define("DEFAULT_DEPOSIT_MONEY", "0.00"); //默认押金金额
define("DEFAULT_RECOMMENDED", "1");
define("IS_STATUS_ONE", 1);
define("IS_STATUS_TWO", 2);
/***************商户收益提现参数默认值******************/
define("DEFAULT_MAX_MONEY", "10000"); //最大提现金额
define("DEFAULT_DRAW_START_TIME", "09:00:00"); //最在提现时间
define("DEFAULT_DRAW_END_TIME", "18:00:00"); //最晚提现时间
define("DEFAULT_MIN_MONEY", "10"); //提现最小金额
define("DEFAULT_DRAW_NUM", "5"); //当日提现次数限制
define("DEFAULT_DRWA_POU", "1.5"); //默认手续费
define("DEFAULT_DRWA_TAX", "0"); //默认提现税点
define("DEFAULT_SETMETHOD", "1");
define("DEFAULT_TRIAL_TIME", "7");
/**********************************************/
// 第三方类库默认路径
define("PUB_TTF", "./Public/ttf/class/");
function importTTFC($className)
{
	return require_cache(PUB_TTF . $className);
}