<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('BaManage/index');?>">
	<input type="hidden" name="keyword" value="<?php echo ($params["keyword"]); ?>" />
	<input type="hidden" name="checkPwd" value="<?php echo ($checkPwd); ?>" />
	<input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
	<input type="hidden" name="cashType" value="<?php echo ($cashType); ?>" />

</form>
<script>
	var $flags= false;
	var $checks = false;
	//表单提交
	
	$(function () {
		// $(document).keydown(function(event){
			
  		// 	if(event.keyCode ==13){
    	// 		submitFrom();
		// 		if($flags){
		// 			 $flags = false;
		// 			 $checks = false;
		// 		}
  		// 	}
		// });
		$('#balancesubmits').click(function () {
			submitFrom();
		});
	});
	function  submitFrom(){
		var $pass = $('#password').val();
			//  alert($pass);
			//  return false;
			if ($pass == "" || Number($pass) == 0 || $pass == 'no') {
				alertMsg.error("请输入密码");
			} else {
				checkBalancePwd();
				if($flags){
					checkBalance($pass);
					if(!$checks){
						$('input[name="checkPwd"]').val("no");  
					}
				}else{
					$('input[name="checkPwd"]').val("");  
				}
				$('#basubmits').click(); 
			}
	}
	//密码是否正确
	function  checkBalance(password){
		$.ajax({
            url: '<?php echo U("Buspublicmanage/checkPassword");?>',
            type: "POST",
            dataType: 'Json',
            data: {
				types:'viewBalance',
				passwd:password
			},
			async: false,
            success: function (ret) {
				if(ret.status){
					 $checks = true;
				}else{
					alertMsg.error("查看密码错误");	
					return false;
				}
            }
        });
	}
	//查看密码是否已设置
	function  checkBalancePwd(){
		$.ajax({
            url: '<?php echo U("Buspublicmanage/checkSecurityPwd");?>',
            type: "POST",
            dataType: 'Json',
            data: {types:'viewBalance'},
			async: false,
            success: function (ret) {
				if(ret.status){
					$flags = true;
				}else{
					alertMsg.warn("安全密码未设置",{
						okCall: function(){
							navTab.openTab("Password/passwords", "<?php echo U('Password/passwords');?>", {title: "安全密码", fresh: false})
						}
					});
					return false;
				}
            }
        });
	}
</script>

<?php if($passtip != 'yes'): ?><!--<div class="pageHeader">-->
	<!--<div class="searchBar">-->
	<form action="<?php echo U('BaManage/index');?>" id="pagerForm" onsubmit="return navTabSearch(this);" method="post">
		<div class="input_code_mian">
			<h3>商户余额查看</h3>
			<div>
				<input type="password" value="<?php echo ($checkPwd); ?>" id="password" name="checkPwd" autocomplete="off" placeholder="输入正确的密码才可以查询" />
			</div>
			<button id="balancesubmits" type="button">验证</button>
			<input id="basubmits" style="display: none" type="submit" />
		</div>

	</form><?php endif; ?>
<style type="text/css">
	.input_code_mian {
		width: 20%;
		position: absolute;
		left: 50%;
		margin-left: -10%;
		top: 40%;
		margin-top: -80px;
		background: #f1f1f1;
		border-radius: 10px;
		border: 1px solid #ccc;
		box-sizing: border-box;
	}

	.input_code_mian>h3 {
		height: 60px;
		line-height: 60px;
		font-size: 30px;
		text-align: center;
	}

	.input_code_mian>div {
		height: 40px;
		padding: 0 10px;
		box-sizing: border-box;
		border: 1px solid #ccc;
		width: 90%;
		margin: 0 auto;
		background: #fff;
	}

	.input_code_mian>div input {
		width: 100%;
		height: 40px;
		line-height: 40px;
		font-size: 16px;
		border: none;
		background: none;
		padding: 0;
	}

	.input_code_mian>button {
		display: block;
		cursor: pointer;
		width: 40%;
		height: 40px;
		line-height: 40px;
		text-align: center;
		font-family: "黑体";
		font-size: 16px;
		border-radius: 5px;
		margin: 15px auto;
		background: #029ae5;
		color: #fff;
		border: none;
	}

	.input_code_mian>button:hover {
		text-decoration: none;
		background: #ee3435;
	}
</style>
<?php if($passtip == 'yes'): ?><div class="pageHeader">
		<form id="form" action="<?php echo U('BaManage/index');?>" onsubmit="return navTabSearch(this);" method="post">
			<div class="searchBar" style="margin-left: 2%">
				<table class="searchContent" style="margin-top: 8px;">
					<input type="hidden" name="checkPwd" value="<?php echo ($checkPwd); ?>" />
					<tr>
						<td>关键字：
							<input type="text" name="keyword" id="keyword" style="width: 160px; text-align: center;" value="<?php echo ($params["keyword"]); ?>"
							 placeholder="用户姓名,联系电话" />
						</td>
						<td>
							<select class="combox" name="cashType">
								<?php if(is_array($cashlist)): $i = 0; $__LIST__ = $cashlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v["englishname"]); ?>"<?php if($v['englishname'] == $cashType): ?>selected="selected"<?php endif; ?>><?php echo ($v["classname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							</select>
						</td>
						<td>
							<div class="buttonActive">
								<div class="buttonContent">
									<button type="submit">检索</button>
								</div>
							</div>
						</td>

					</tr>
				</table>
				<table class="searchContent" style="margin-top: 8px;">
					<tr></tr>
				</table>
			</div>
		</form>
	</div>
	<div class="pageContent">
		<div class="panelBar">
			<ul class="toolBar">
				<!-- <li><a class="icon" target="_blank" onclick="exportPay(this)"><span>导出EXCEL</span></a></li>
			<li class="line"></li>-->
				<li>
					<a class="edit" href="<?php echo U('BaManage/edit');?>?id={sid}" target="dialog" mask="true" width="600" height="380">
						<span>余额增减</span>
					</a>
				</li>
				<li class="line"></li>
				<li>
					<a class="add" href="<?php echo U('BaManage/add');?>" target="dialog" mask="true" width="400" height="330" rel="add">
						<span>新增余额</span>
					</a>
				</li>
				<li class="line"></li>
				<!--li><a class="edit" href="<?php echo U('BaManage/temporary');?>"
				target="dialog" mask="true" width="1268" height="600" rel="add"
				title="临时存储"><span>更新查看</span></a></li>
			<li class="line"></li-->
			</ul>
		</div>
		<table width="100%" class="table" layoutH="140">
			<thead>
				<tr>
					<th width="5%" align="center">序号</th>
					<th width="8%" align="center">商户名称</th>
					<th width="10%" align="center">商户电话</th>
					<th width="8%" align="center">余额</th>
					<th width="8%" align="center">存储类型</th>
					<th width="10%" align="center">存储时间</th>
					<th width="10%" align="center">最新时间</th>
					<th width="12%" align="center">交易状态</th>
				</tr>
			</thead>

			<?php if($totalCount > 0): ?><tbody id="checkview">
					<?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid" rel="<?php echo ($vo["id"]); ?>" data-desknum="">
							<td><?php echo ($i); ?></td>
							<td><?php echo ($vo['e_name']); ?></td>
							<td><?php echo ($vo['e_tel']); ?></td>
							<td>
								<span style="font-weight: bold;color: #996600;"><?php echo ($vo['total_amount']); ?></span>
							</td>
							<td>

								<span style="color: #006633; font-weight: bold;"><?php echo ($vo["classname"]); ?></span>
							</td>
							<td><?php echo ($vo['update_date']); ?></td>
							<td><?php echo ($vo['modify_time']); ?></td>
							<td>
								<span id="detailOrder" class="detailOrder" onclick="detailOrder('<?php echo ($vo["bus_id"]); ?>','<?php echo ($vo["e_name"]); ?>')">收益明细</span>
								<span id="detailOrder" class="detailOrder" onclick="daysCords('<?php echo ($vo["bus_id"]); ?>','<?php echo ($vo["e_name"]); ?>')">日统计</span>
								<span id="detailOrder" class="detailOrder" onclick="monthCords('<?php echo ($vo["bus_id"]); ?>','<?php echo ($vo["e_name"]); ?>')">月统计</span>
							</td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
				<?php else: ?>
				<tbody>
					<tr>
						<td align="center" colspan="8" style="color: red;">抱歉，没有找到符合的记录！</td>
					</tr>
				</tbody><?php endif; ?>
		</table>
		<div class="statisticsb">
			<ul>
				<li>
					<span>总数：共&nbsp;[&nbsp;
						<em><?php echo ($totalCount); ?></em>&nbsp;]&nbsp;条
					</span>
				</li>
				<li>
					<span id="totalMoney">总余额：
						<b class="viewTotal" onclick="viewTotal()">点击查看</b>
					</span>
				</li>
			</ul>
		</div>
		<script>
			function monthCords(id, n) {
				var url = "<?php echo U('DataAnalysis/monthCords');?>?id=" + id;
				$.pdialog.open(url, "dialog", "月统计-" + n, {
					mask: true,
					width: 1300,
					height: 700,
					drawable: false,
					maxable: false,
					minable: false,
					resizable: false
				});
			}
			/**
			 *
			 */
			function daysCords(id, n) {
				var url = "<?php echo U('DataAnalysis/daysCords');?>?id=" + id;
				$.pdialog.open(url, "dialog", "当月收益-" + n, {
					mask: true,
					width: 1300,
					height: 860,
					drawable: false,
					maxable: false,
					minable: false,
					resizable: false
				});
			}
			/**
			 * 明细列表
			 */
			function detailOrder(id, n) {
				var url = "<?php echo U('Detail/detailOrder');?>?id=" + id;
				$.pdialog.open(url, "dialog", "收益明细-" + n, {
					mask: true,
					width: 1024,
					height: 520,
					drawable: false,
					maxable: false,
					minable: false,
					resizable: false
				});
			}
			/**
			 * 总余额统计
			 */
			function viewTotal() {
				var $keyword = $("#keyword").val();
				$.ajax({
					url: "<?php echo U('BaManage/Total');?>",
					dataType: "JSON",
					type: "POST",
					data: {
						keyword: $keyword
					},
					success: function (ret) {
						var money = 0;
						if (ret.sta == 1) {
							money = ret.money;
						}
						$("#totalMoney").html("总余额：<em>" + money + "</em>");

					}
				});
			}
		</script>
		<style>
			.detailOrder {
				height: 25px;
				position: relative;
				cursor: pointer;
				background: #333366;
				color: #FFFFFF;
				border: 1px solid gray;
				border-radius: 10px;
				box-shadow: 1px 1px 1px 1px rgba(0, 0, 0, 0.5);
				padding: 3px 5px;
				font-weight: bold;
				font-size: 10px;
			}

			.detailOrder:hover {
				left: 1px;
				top: 1px;
			}

			.viewTotal {
				position: relative;
				cursor: pointer;
				background: #333366;
				color: #FFFFFF;
				border: 1px solid gray;
				border-radius: 10px;
				box-shadow: 1px 1px 1px 1px rgba(0, 0, 0, 0.5);
				padding: 3px 5px;
				font-weight: bold;
				font-size: 11px;
			}

			.viewTotal:hover {
				left: 1px;
				top: 1px;
			}

			.statisticsb {
				bottom: 28px;
				height: 40px;
				left: 0;
				position: absolute;
				width: 100%;
			}

			.statisticsb ul {
				overflow: hidden;
				text-align: center;
			}

			.statisticsb ul li {
				display: inline-block;
				height: 36px;
				border: 2px #999999 solid;
				/*text-align:left;*/
				line-height: 36px;
				margin-left: 5px;
				padding: 0 15px 0 0;
				font-weight: bold;
				color: #371414;
			}

			.statisticsb ul li span {
				padding-left: 15px;
				display: block;
				line-height: 36px;
			}

			.statisticsb ul li span em {
				color: #996600;
				font-size: 14px;
			}

			.statisticsb ul li a {
				padding-left: 15px;
				color: #993333;
				display: block;
				line-height: 36px;
			}

			.tongji {
				padding-left: 33%;
			}

			.tongji .tongji_1 {
				padding: 0 0 0 20px;
				text-align: center;
			}

			.tongji .tongji_1 span {
				color: red;
			}
		</style>
		<div class="panelBar">
			<div class="pages">
				<span>显示</span>
				<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
					<option value="20" <?php if($Page['numPerPage'] == 30): ?>selected="selected"<?php endif; ?>>20
</option>
<option value="50" <?php if($Page['numPerPage'] == 50): ?>selected="selected"<?php endif; ?>>50
</option>
<option value="100" <?php if($Page['numPerPage'] == 100): ?>selected="selected"<?php endif; ?>>100
</option>
<option value="200" <?php if($Page['numPerPage'] == 200): ?>selected="selected"<?php endif; ?>>200
</option>

</select>
<span>条，共<?php echo ($totalCount); ?>条</span>
</div>

<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
 currentPage="<?php echo ($page); ?>"></div>
</div>
</div><?php endif; ?>