<?php
return array(
	"requireParams" => "requestType,requestKeywords",
	"checkParams" => array(
		"Earnings" => array(
			"savemoney" => "userID,platformID,userPhone",
			"tradestat" => "userID,platformID,userPhone,proid",
			"partner" => "userID,platformID,userPhone",
			"personal" => "userID,platformID,userPhone",
			"championship" => "userID,platformID,userPhone,types,level",
			"ranking" => "userID,platformID,userPhone,level,cashType",
			"myincome" => "userID,platformID,userPhone",
			"resultsperson" => "userID,platformID,userPhone,types,level,checkType",
			"actlist" => "userID,platformID,userPhone,page"
		),
		//在线订单
		"Onlineorder" => array(
			"ordernumlimit" => "platformID,proid",
			"detail" => "id",  //详情
			"order" => "userID,platformID,userPhone,orderinfo,productinfo,money,sid",
			"olist" => "userID,platformID,userPhone,page,isReceipt",
		),
		"Funds" => array(
			"merchandise" => "userID,platformID,userPhone,types"
		),
		"Wx" => array(
			"getsessionkey" => "code",
			"getsessionkeysp" => "code",
			"salesmanrecharge" => "userID,platformID,userPhone,openid,money",
			"salesmanrechargesp" => "userID,platformID,userPhone,openid,money"
		), 
		//解冻
		"Thaw" => array(
			"thawmoney" => "userID,platformID,userPhone",
			"thawlist" => "userID,platformID,userPhone"
		), 
		//资讯管理
		"Weboutputmg" => array(
			"lists" => "userID,platformID,userPhone"
		),
		//服务费
		"Servicefee" => array(
			"complatetip" => "userID,platformID,userPhone,ordernum",
			"checkplatfee" => "platformID",
			"reclist" => "userID,platformID,userPhone",
			"recharge" => "userID,platformID,userPhone,cycle,money",
			"templatelist" => "platformID",
			"rechargenotic" => "userID,platformID,userPhone",
			"checkrertype" => "platformID",
			"prompt" => "userID,platformID,userPhone",
		),
		//服务商
		"Agent" => array(
			"checkterminal" => "userID,platformID,userPhone,keywords",
			"backyards" => "userID,platformID,userPhone,machineID,childID,terminal",
			"macrecords" => "userID,platformID,userPhone",
			"getbus" => "userID,platformID,userPhone,keywords",
			"getbuslist" => "userID,platformID,userPhone,keywords",
			"oneclick" => "userID,platformID,userPhone,machineID,childID,terminal",
			"dialcode" => "userID,platformID,userPhone,childID,machineID",  //拨码
			"childlist" => "userID,platformID,userPhone",  //服务商列表
			"batchnolist" => "userID,platformID,userPhone",  //批次列表
			"terminalsmalllist" => "userID,platformID,userPhone,productID",  //发货使用终端列表
			"delivery" => "userID,platformID,userPhone,orderID,courierName,waybillNumber,terminalNo",  //发货
			"order" => "userID,platformID,userPhone,isReceipt",  //订单列表
			"product" => "platformID",  //产品列表
			"terminal" => "userID,platformID,userPhone",  //终端列表
			"summary" => "userID,platformID,userPhone",  //收益总汇
			"earnings" => "userID,platformID,userPhone",  //收益明细
			"organization" => "userID,platformID,userPhone",  //组织架构
			"getnum" => "userID,platformID,userPhone"
		), 
		//订单
		"Order" => array(
			"detail" => "id",  //详情
			"confirmstatus" => "id", //收货确认状态
			"cancelorder" => "id", //取消订单
			"deleteorder" => "id", //删除订单
			"waybill" => "id",  //物流跟踪
			"getnum" => "userID,platformID,userPhone",  //统计数
			"comment" => "userID,platformID,userPhone,score,content,orderid",  //评论
			"orderquery" => "id,ordernum",  //支付状态查询 
			"olist" => "userID,platformID,userPhone,page,isReceipt",  //列表
			"order" => "userID,platformID,userPhone,sid,proid,openid"
		),  //下订单
		"Assets" => array(
			"busBalance" => "platformID,userID,userPhone"
		),
		"Product" => array("lists" => "plat"),
		"Personal" => array(
			"balancelist" => "userID,userPhone,platformID",
			"balance" => "userID,userPhone,platformID",
			"jiangfeilv" => "userID,userPhone,platformID",
			"busincome" => "userID,userPhone,platformID",
			"wxregister" => "openid,busname,code,regType,ImagePath",
			"mixlogin" => "account,loginType", //混合登录
			"wechatauth" => "userID,userPhone,platformID,openid,ImagePath",
			"feedback" => "userID,userPhone,platformID,content",
			"whetherapply" => "userID,userPhone,platformID",
			"serviceprovider" => "userID,userPhone,platformID",
			"register" => "phone,name,code,password",
			"getdrawpou" => "userID,userPhone,platformID",
			"spendcounted" => "userID,userPhone,platformID",
			"getcerti" => "userID,userPhone,platformID",
			"checkcer" => "userID,userPhone,platformID",
			"getshipping" => "userID,userPhone,platformID",
			"login" => "account,password",
			"getbusinfo" => "userID,userPhone,platformID",
			"getassets" => "userID,userPhone,platformID",
			"getincome" => "userID,userPhone",
			"getbankcard" => "platformID,userID,userPhone",
			"mypos" => "platformID,userID,userPhone",
			"getbname" => "cardNum",
			"getqrcode" => "userID,userPhone",
			"bindwechat" => "ImagePath,openid,platformID,userID,userPhone",
			"unbindwechat" => "platformID,userID,userPhone",
		),
		"System" => array(
			"getsystem" => "type",
			"appswitch" => "platformID"
		),
		"List" => array(
			"upgradeintro" => "platformID",
			"drawlist" => "platformID,userID,userPhone,page",
			"tradeclass" => "platformID,userID,userPhone,level",
			"exclvlist" => "platformID,userID,userPhone",
			"actranking" => "platformID",
			"totalsum" => "platformID,userID,userPhone",
			"montranking" => "platformID,userID,userPhone",
			"teamdetail" => "platformID,userID,userPhone,page,id",
			"shippingaddress" => "userID,userPhone,platformID",
			"probuscomment" => "id,page",
			"inforclass" => "platformID",
			"information" => "platformID",
			"informa" => "platformID",
			"advertis" => "types",
			"module" => "types",
			"productlists" => "platformID,page",  //商品列表
			"sysmsg" => "platformID,page",
			"apphelp" => "platformID",
			"helpcenter" => "platformID,page",
			"tips" => "page", "banner" => "page",
			"lowerdata" => "platformID,userID,userPhone",
			"parentinfo" => "platformID,userID,userPhone",
			"team" => "platformID,userID,userPhone,page",
			"tradelist" => "userID,userPhone,page",
			"incomelist" => "platformID,userID,userPhone,page,types",
			"ranking" => "userID,userPhone,page"
		),
		"Listdetail" => array(
			"sysmsg" => "id",
			"helpcenter" => "id", "tips" => "id",
			"infordetail" => "id",
			"productdetail" => "id",
			"helpDetail" => "id"
		),
		"Operating" => array(
			"checkbankcard" => "platformID,userID,userPhone",
			"checkcards" => "cardNum",
			"addbankcard" => "platformID,userID,userPhone,cardNum,phone,bankName",
			"delshipping" => "id",
			"realnameauth" => "platformID,userID,userPhone,name,idcard",
			"editshippingaddress" => "id,name,consigeephone,address,province,city,area",
			"shippingaddr" => "userID,userPhone,platformID,name,consigeephone,address,defaultState,province,city,area",
			"addrdefultstatus" => "userID,userPhone,platformID,id",
			"forget" => "phone,newpass",
			"repass" => "userID,userPhone,oldpass,newpass",
			"drawcash" => "platformID,userID,userPhone,money,cashType",
			"cardadd" => "userID,userPhone,phone,cardNum,bankName,verify,idCard",
			"cardedit" => "userID,userPhone,phone,cardNum,bankName,verify,idCard",
			"feedback" => "userID,userPhone,content",
			"sendmsg" => "phone,type",
			"addviewnum" => "id,type"
		)
	)
);