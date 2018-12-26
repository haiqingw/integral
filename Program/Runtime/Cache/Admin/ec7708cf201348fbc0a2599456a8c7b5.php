<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('ChangeBalance/index');?>">
	<input type="hidden" name="status" value="${param.status}"> <input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" />
	<input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" /> <input type="hidden" name="tranType" value="<?php echo ($params["tranType"]); ?>" />
	<input type="hidden" name="ornum" value="<?php echo ($params["ornum"]); ?>" /><input type="hidden" name="payType" value="<?php echo ($params["payType"]); ?>" />
	<input type="hidden" name="keyword" value="<?php echo ($params["keyword"]); ?>" /><input type="hidden" name="status" value="<?php echo ($params["status"]); ?>" />
	<input type="hidden" name="auditStatus" value="<?php echo ($params["auditStatus"]); ?>" /><input type="hidden" name="pageNum" value="<?php echo ($page); ?>" /><input
	 type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
	 <input type="hidden" name="changepwd" value="<?php echo ($changepwd); ?>" />
</form>
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
<script>
	var $chaFlag = false;
	var $chaCheck = false;
	//表单提交
	$(function () {
		$('#changepwdsubmits').click(function () {
			var $pass = $('#changepwd').val();
			if ($pass == "" || Number($pass) == 0) {
				alertMsg.error("请输入密码");
			} else {
				checkSecuritychangepwd();
				if($chaFlag){
					checkchangepwd($pass);
					if(!$chaCheck){
						$('input[name="changepwd"]').val("no");  
					}
				}else{
					$('input[name="changepwd"]').val("");  
				}
				// alert($flag);
				$('#changepwdforms').click();
			}
		});
	});
	//密码是否正确
	function  checkchangepwd(password){
		$.ajax({
            url: '<?php echo U("Buspublicmanage/checkPassword");?>',
            type: "POST",
            dataType: 'Json',
			async: false,
            data: {
				types:'buttonBack',passwd:password
			},
            success: function (ret) {
				if(ret.status){
					 $chaCheck = true;
				}else{
					alertMsg.error("查看密码错误");	
					return false;
				}
            }
        });
	}
	//查看密码是否已设置
	function  checkSecuritychangepwd(){
		$.ajax({
            url: '<?php echo U("Buspublicmanage/checkSecurityPwd");?>',
            type: "POST",
            dataType: 'Json',
            data: {types:'buttonBack'},
			async: false,
            success: function (ret) {
				if(ret.status){
					$chaFlag = true;
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
<?php if($passtipca != 'yes'): ?><form action="<?php echo U('ChangeBalance/index');?>" id="pagerForm" onsubmit="return navTabSearch(this);" method="post">
		<div class="input_code_mian">
			<h3>退扣款查看</h3>
			<div>
				<input type="password" value="<?php echo ($changepwd); ?>" id="changepwd" name="changepwd" autocomplete="off" placeholder="输入正确的密码才可以查询" />
			</div>
			<button id="changepwdsubmits" type="button">验证</button>
			<input id="changepwdforms" style="display: none" type="submit" />
		</div>

	</form><?php endif; ?>
<?php if($passtipca == 'yes'): ?><div class="pageHeader">
		<form id="form" action="<?php echo U('ChangeBalance/index');?>" onsubmit="return navTabSearch(this);" method="post">
			<div class="searchBar" style="margin-left: 2%">
				<input type="hidden" name="changepwd" value="<?php echo ($changepwd); ?>" />
				<table class="searchContent" style="margin-top: 8px;">
					<tr>
						<td>支付时间：<input type="text" name="startDate" class="date" size="15" readonly="true" datefmt="yyyy-MM-dd" value="<?php echo ($startDate); ?>"
							 placeholder="开始时间" style="text-align: center;" /> ~ <input type="text" name="endDate" class="date" readonly="true"
							 size="15" datefmt="yyyy-MM-dd" value="<?php echo ($endDate); ?>" placeholder="结束时间" style="text-align: center;" />
						</td>

						<td>关键字：<input type="text" name="keyword" style="width: 160px; text-align: center;" value="<?php echo ($params["keyword"]); ?>"
							 placeholder="用户姓名,联系电话" />
						</td>
						<td>订单号：<input type="text" name="ornum" size="20" style="text-align: center;" value="<?php echo ($params["ornum"]); ?>" placeholder="输入订单号" />
						</td>
						<td><label>审核状态：</label> <select name="auditStatus" class="combox">
								<option value="">请选择</option>
								<option value="Y" <?php if($params['auditStatus'] == Y): ?>selected="selected"<?php endif; ?>>已审核

								</option>
								<option value="N" <?php if($params['auditStatus'] == N): ?>selected="selected"<?php endif; ?>>未审核
								</option>

							</select></td>
						<td>
							<div class="buttonActive">
								<div class="buttonContent">
									<button type="submit">检索</button>
								</div>
							</div>
						</td>

					</tr>
				</table>
			</div>
		</form>
	</div>
	<div class="pageContent">
		<div class="panelBar">
			<ul class="toolBar">
				<!-- <li><a class="icon" target="_blank"
				onclick="exportChangeBalance(this)"><span>导出EXCEL</span></a></li>
			<li class="line"></li> -->
				<li><a class="edit" href="<?php echo U('ChangeBalance/ModifyStatus');?>?id={sid}" target="ajaxTodo" title="确认修改"><span>作废&nbsp;|&nbsp;恢复</span></a></li>
				<li class="line"></li>
				<!--<li><a class="edit" href="<?php echo U('ChangeBalance/Refund');?>?id={sid}"
				target="dialog" mask="true" width="400" height="450"><span>退回</span></a></li>
			<li class="line"></li>-->
				<li><a class="edit" href="<?php echo U('BaManage/Refund');?>?id={sid}" target="dialog" mask="true" width="550" height="520"><span>审核余额修改</span></a></li>
				<li class="line"></li>
				<!-- <li><a class="icon" href="<?php echo U('ChangeBalance/amount');?>"
				id="zdyexport"><span>统计</span></a></li>
			<li class="line"></li> -->
			</ul>
		</div>
		<table width="100%" class="table" layoutH="150">
			<thead>
				<tr>
					<th width="4%" align="center">序号</th>
					<th width="15%" align="center">订单号</th>
					<th width="8%" align="center">商户名称</th>
					<th width="6%" align="center">商户电话</th>
					<th width="7%" align="center">原金额(&yen;)</th>
					<th width="7%" align="center">变动金额(&yen;)</th>
					<th width="5%" align="center">提现卡号</th>
					<th width="5%" align="center">手续费(&yen;)</th>
					<th width="6%" align="center">变动类型</th>
					<th width="5%" align="center">交易状态</th>
					<th width="5%" align="center">退款状态</th>
					<th width="6%" align="center">退款审核</th>
					<th width="10%" align="center">提交时间</th>
				</tr>
			</thead>
			<?php if($totalCount > 0): ?><tbody id="checkview">
					<?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="sid" rel="<?php echo ($vo["id"]); ?>" data-desknum="">
							<td><?php echo ($i); ?></td>
							<td><?php echo ($vo['ordernum']); ?></td>
							<td><?php echo ($vo['bname']); ?></td>
							<td><?php echo ($vo['e_tel']); ?></td>
							<td><span style="color: #666666; font-weight: bold;"><?php echo ($vo['orgMoney']); ?></span></td>
							<td>
								<?php if(($vo['tranType'] == T) OR ($vo['tranType'] == X) ): ?><span style="color: #CC0000; font-weight: bold;">&minus;
										<?php echo ($vo['payMoney']); ?></span><?php endif; ?>
								<?php if($vo['tranType'] == Z): ?><span style="color: #336633; font-weight: bold;">
										<?php echo ($vo['payMoney']); ?></span><?php endif; ?>
							</td>

							<td>
								<?php if($vo['bank_id'] == 0): ?><span style="color: #993333; font-weight: bold;">无记录</span>
									<?php else: ?> <a href="#" onclick="bankDetail(this, '<?php echo ($vo['bank_id']); ?>')"> <span style="color: #336666; font-weight: bold;">查看</span>
									</a><?php endif; ?>
							</td>
							<td><?php echo ($vo['poundage']); ?></td>
							<td>
								<?php if($vo['payType'] == 1): ?><span style="color: #006633; font-weight: bold;">商户收益</span><?php endif; ?>
							</td>
							<td>
								<?php if($vo['status'] == 1): ?><span style="color: #006633; font-weight: bold;">正常</span><?php endif; ?>
								<?php if($vo['status'] == 2): ?><span style="color: #660033; font-weight: bold;">作废</span><?php endif; ?>
							</td>
							<td>
								<?php if($vo['RefundStatus'] == ''): ?><span style="color: #666666; font-weight: bold;">正常交易</span><?php endif; ?>
								<?php if($vo['RefundStatus'] == 'Z'): ?><span style="color: #333300; font-weight: bold;">退款</span><?php endif; ?>
								<?php if($vo['RefundStatus'] == 'T'): ?><span style="color: #660033; font-weight: bold;">扣款</span><?php endif; ?>
							</td>
							<td>
								<?php if($vo['RefundStatus'] == ''): ?><span style="color: #666666; font-weight: bold;">免审</span>
									<?php else: ?>
									<?php if($vo['auditStatus'] == 'N'): ?><span style="color: #660033; font-weight: bold;">未审核</span><?php endif; ?>
									<?php if($vo['auditStatus'] == 'Y'): ?><span style="color: #006633; font-weight: bold;">已审核</span><?php endif; endif; ?>
							</td>
							<td><?php echo ($vo['createTime']); ?></td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
				<?php else: ?>
				<tbody>
					<tr>
						<td align="center" colspan="8" style="color: red;">抱歉，没有找到符合的记录！</td>
					</tr>
				</tbody><?php endif; ?>
		</table>

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
					</select> <span>条，共<?php echo ($totalCount); ?>条
				</span>
			</div>
			<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
					currentPage="<?php echo ($page); ?>"></div>
		</div>
	</div><?php endif; ?>