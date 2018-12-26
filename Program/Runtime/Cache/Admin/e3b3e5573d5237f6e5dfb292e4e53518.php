<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo C('webTitle');?> - 管理平台</title>

	<link href="/Public/dwzUI/themes/default/style.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="/Public/dwzUI/themes/css/core.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="/Public/dwzUI/themes/css/print.css" rel="stylesheet" type="text/css" media="print" />
	<!-- <link href="/Public/uploadify/uploadify.css" rel="stylesheet" type="text/css" media="screen" /> -->
	<!--[if IE]>
	<link href="/Public/dwzUI/themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
	<![endif]-->

	<!--[if lte IE 9]>
	<script src="/Public/dwzUI/js/speedup.js" type="text/javascript"></script>
	<![endif]-->
	<script src="/Public/dwzUI/js/jquery-1.8.3.min.js" type="text/javascript"></script>
	<!--ueditor编辑器-->
	<script type="text/javascript" charset="utf-8" src="/Public/ueditor/ueditor.config.js"></script>
	<script type="text/javascript" charset="utf-8" src="/Public/ueditor/ueditor.all.min.js">

	</script>
	<script type="text/javascript" charset="utf-8" src="/Public/ueditor/lang/zh-cn/zh-cn.js"></script>
	<!--ueditor编辑器-->
	<script src="/Public/dwzUI/js/ajaxupload.js" type="text/javascript"></script>
	<!--<script src="/Public/dwzUI/js/jquery-1.7.2.js" type="text/javascript"></script>-->
	<script src="/Public/dwzUI/js/jquery.cookie.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/jquery.validate.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/jquery.bgiframe.js" type="text/javascript"></script>
	<!-- <script src="/Public/uploadify/jquery.uploadify.js" type="text/javascript"></script> -->
	<script src="/Public/js/jquery.qrcode.min.js" type="text/javascript"></script>
	<!-- svg图表  supports Firefox 3.0+, Safari 3.0+, Chrome 5.0+, Opera 9.5+ and Internet Explorer 6.0+ -->
	<script type="text/javascript" src="/Public/dwzUI/chart/echarts.min.js"></script>

	<script src="/Public/dwzUI/js/dwz.core.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.util.date.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.validate.method.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.barDrag.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.drag.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.tree.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.accordion.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.ui.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.theme.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.switchEnv.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.alertMsg.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.contextmenu.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.navTab.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.tab.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.resize.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.dialog.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.dialogDrag.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.sortDrag.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.cssTable.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.stable.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.taskBar.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.ajax.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.pagination.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.database.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.datepicker.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.effects.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.panel.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.checkbox.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.history.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.combox.js" type="text/javascript"></script>
	<script src="/Public/dwzUI/js/dwz.print.js" type="text/javascript"></script>
	<script src="/Public/layer/layer.js" type="text/javascript"></script>
	<script src="/Public/ADcheck.js" type="text/javascript"></script>
	<!-- <script src="/Public/search/j.dimensions.js" type="text/javascript"></script> -->
	<!-- <script src="/Public/search/j.suggest.js" type="text/javascript"></script> -->
	<!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=Gavp1fvlrGMnV0KVu1rHkKMS"></script> -->
	<!-- <script type="text/javascript" src="/Public/admin/js/bdMap.js"></script> -->


	<!--script src="/Public/js/modules/highcharts-more.js"></script-->


	<!-- 可以用dwz.min.js替换前面全部dwz.*.js (注意：替换是下面dwz.regional.zh.js还需要引入)
	<script src="bin/dwz.min.js" type="text/javascript"></script>
	-->
	<script src="/Public/dwzUI/js/dwz.regional.zh.js" type="text/javascript"></script>
	<script src="/Public/js/dynSelect.js" type="text/javascript"></script>
	<style type="text/css">
		.dynSelect {
			position: absolute;
			border: 1px solid gray;
			background: white;
			text-align: left;
			overflow: auto;
			max-height: 300px
		}

		.dynSelect p {
			cursor: pointer;
			line-height: 20px;
			padding-left: 5px;
			border-bottom: 1px solid #D1CECE
		}

		.dynSelect p:hover {
			background: gray;
			color: white
		}

		.RLogoBox {
			overflow: hidden;
			padding: 5px 0;
		}

		.RLogoBox:hover {
			text-decoration: none;
		}

		.RLogoBox img {
			width: 45px;
			height: 45px;
			float: left;
			margin-right: 10px;
			margin-top: 5px;
			margin-left: 10px;
		}

		.RLogoBox span {
			display: block;
			height: 55px;
			line-height: 55px;
			font-size: 24px;
			font-weight: bold;
			color: rgb(1, 112, 218);
			text-shadow: 1px 1px 1px #F6D6FF, -1px -1px 1px #fff;
		}
	</style>
	<script type="text/javascript">
		$(function () {
			DWZ.init("/Public/dwzUI/dwz.frag.xml", {
				loginUrl: "/Public/dwzUI/login_dialog.html",
				loginTitle: "登录", // 弹出登录对话框
				//		loginUrl:"login.html",	// 跳到登录页面
				statusCode: {
					ok: 200,
					error: 300,
					timeout: 301
				}, //【可选】
				pageInfo: {
					pageNum: "pageNum",
					numPerPage: "numPerPage",
					orderField: "orderField",
					orderDirection: "orderDirection"
				}, //【可选】
				keys: {
					statusCode: "statusCode",
					message: "message"
				}, //【可选】
				ui: {
					hideMode: 'offsets'
				}, //【可选】hideMode:navTab组件切换的隐藏方式，支持的值有’display’，’offsets’负数偏移位置的值，默认值为’display’
				debug: false, // 调试模式 【true|false】
				callback: function () {
					initEnv();
					$("#themeList").theme({
						themeBase: "/Public/dwzUI/themes"
					}); // themeBase 相对于index页面的主题base路径
					$.cookie("dwz_theme", "azure")
				}
			});
		});
	</script>
</head>

<body scroll="no">
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<a class="RLogoBox" href="javascript:;"><img src="/Public/images/integral_logo.png" /><span>积分商城</span></a>
				<ul class="nav" style="padding-right: 40px;">
					<li style="">当前用户： <span style="font-weight:bold;color:#0000CD;"><?php echo ($infoAcc["companyName"]); ?></span><a href="javascript:;">（<?php echo ($infoAcc["usertable_Name"]); ?>）</a></li>
					<li style=""></li>
					<li style="background-image: none;"><a href="<?php echo U('Index/uppassword');?>" rel="resetpwd" target="dialog" width="400"
						 height='270'>密码更改</a></li>
					<li style=""></li>
					<li style="background-image: none;font-weight: bold;"><a href="<?php echo U('Index/preview');?>" rel="" target="dialog" width="300"
						 height='300' mask="true" style="color: #228b22;">小程序</a></li>
					<li style=""></li>
					<li><a href="javascript:;" logout>退出</a></li>
				</ul>
				<ul class="themeList" id="themeList" style="padding-right: 40px;">
					<li theme="default">
						<div class="selected">蓝色</div>
					</li>
					<li theme="green">
						<div>绿色</div>
					</li>
					<!--<li theme="red"><div>红色</div></li>-->
					<li theme="purple">
						<div>紫色</div>
					</li>
					<li theme="silver">
						<div>银色</div>
					</li>
					<li theme="azure">
						<div>天蓝</div>
					</li>
				</ul>
			</div>

			<!-- navMenu -->

		</div>

		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse">
						<div></div>
					</div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse">
					<h2>主菜单</h2>
					<div>收缩</div>
				</div>
				<div class="accordion" fillSpace="sidebar">
					<?php if(is_array($tlist)): foreach($tlist as $key=>$a): ?><div class="accordionHeader">
							<h2>
								<span>Folder</span><?php echo ($a["modelinfo"]["model_Name"]); ?>
							</h2>
							<?php $ks=$a['modelarray']; ?>
						</div>
						<div class="accordionContent">
							<ul class="tree treeFolder">
								<?php if(is_array($ks)): foreach($ks as $key=>$bbb): ?><li><a><?php echo ($bbb["auth_name"]); ?></a>
										<?php $level=$bbb['leveltr']; ?>
										<ul>
											<?php if(is_array($level)): $i = 0; $__LIST__ = $level;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; $hrefurl = $v['auth_c'].'/'.$v['auth_a'] ?>
												<li><a href="<?php echo U($hrefurl);?>" class="caClick" data-c="<?php echo ($v["auth_c"]); ?>" data-a="<?php echo ($v["auth_a"]); ?>" target="navTab" rel="<?php echo ($hrefurl); ?>"><?php echo ($v['auth_name']); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
										</ul>
									</li><?php endforeach; endif; ?>
							</ul>
						</div><?php endforeach; endif; ?>
				</div>
			</div>
		</div>
		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent">
						<!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="" class="main"><a> <span> <span class="home_icon">我的主页</span>
									</span>
								</a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div>
					<!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight">right</div>
					<!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a>我的主页</a></li>
				</ul>
				<div class="navTab-panel tabsPageContent layoutBox">
					<div class="page unitBox">
						<div style="position: relative; padding-left: 20px;" class="accountInfo">
							<h1 style="color: #029ae5; font-size: 12px; line-height: 60px;">欢迎使用天天刷 - 管理平台</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="footer">&copy; <?php echo ($realyear); ?> <?php echo C('webTitle');?> <?php echo C('webBeian');?></div>

	<script type=”text/javascript”> window.UEDITOR_HOME_URL="/Public/" ; </script> </body> </html> <script>
		/*
 * @description  dwz - 扩展  批量操作 打开对话框（或标签页） 
 * @html  a->target: navTabBat|dialogBat  其他与‘selectTodo’一样
 */
		(function ($) {

			$(document).on('click', 'a[target]', function () {
				var target = $(this).attr("target");
				if (target == "navTabBat" || target == "dialogBat") {
					var inputName = $(this).attr('rel');
					var $input = $("input[name='" + inputName + "']:checked");
					var valueArr = [];
					$input.each(function (index, element) {
						valueArr[index] = $(element).val();
					});
					if (valueArr.length <= 0) {
						alertMsg.error('请选择信息!');
					} else {
						var title = $(this).attr('title') ? $(this).attr('title') : $(this).text();
						var url = $(this).attr('href') + '?' + inputName + '=' + valueArr;
						if (target == "navTabBat") {
							navTab.openTab(inputName, url, { title: title, fresh: false });
						} else {
							var options = {
								fresh: false,
								width: $(this).attr('width'),
								height: $(this).attr('height'),
								mask: $(this).attr('mask'),
								max: $(this).attr('max'),
								minable: $(this).attr('minable') !== 'false',
								maxable: $(this).attr('maxable') !== 'false',
								resizable: $(this).attr('resizable') !== 'false',
								drawable: $(this).attr('drawable') !== 'false',
							};
							$.pdialog.open(url, inputName, title, options);
						}
					}
					return false;
				}
			});

		})(jQuery);
	</script>
	<script type="text/javascript">
		setTimeout(function () {
			$.get('<?php echo U("Main/index");?>', function (data) {
				$('.unitBox').html(data);
			});
		}, 10);
	</script>
	<script type="text/javascript">
		$(function () {
			$("a[logout]").click(function () {
				if (confirm("您确定退出系统?")) {
					$.post("<?php echo U('Login/logout');?>", '', function (data) {
						window.location = "<?php echo U('Login/indexes');?>";
					});
				} else {
					return false;
				}
			});
			/* $('.caClick').click(function(){
				var $c = $(this).attr('data-c');
				var $a = $(this).attr('data-a');
				$.ajax({
					url:"<?php echo U('behaviorAdmin');?>",
					type:"post",
					timeout:1,
					data:{controller:$c,action:$a},
					success:function(data){
						alert(data);
					}
				});
			}); */
		});
	</script>