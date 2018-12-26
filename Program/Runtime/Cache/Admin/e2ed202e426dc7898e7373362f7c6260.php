<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('WithdrawalManage/review');?>">
	<input type="hidden" name="status" value="${param.status}"> <input type="hidden" name="keywords" value="${param.keywords}" />
	<input type="hidden" name="pageNum" value="1" />
	<!--每页显示多少条数据开始-->
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
	<input type="hidden" name="checkPwds" value="<?php echo ($checkPwds); ?>" />
	<!--每页显示多少条数据开始-->
	<input type="hidden" name="orderField" value="${param.orderField}" />
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
	var $flag = false;
	var $check = false;
	//表单提交
	$(function () {
		$('#submits').click(function () {
			var $pass = $('#passwords').val();
			if ($pass == "" || Number($pass) == 0) {
				alertMsg.error("请输入密码");
			} else {
				checkSecurityPwd();
				if($flag){
					checkPwd($pass);
					if(!$check){
						$('input[name="checkPwds"]').val("no");  
					}
				}else{
					$('input[name="checkPwds"]').val("");  
				}
				// alert($flag);
				$('#forms').click();
			}
		});
	});
	//密码是否正确
	function  checkPwd(password){
		$.ajax({
            url: '<?php echo U("Buspublicmanage/checkPassword");?>',
            type: "POST",
            dataType: 'Json',
			async: false,
            data: {
				types:'txAudit',passwd:password
			},
            success: function (ret) {
				if(ret.status){
					 $check = true;
				}else{
					alertMsg.error("查看密码错误");	
					return false;
				}
            }
        });
	}
	//查看密码是否已设置
	function  checkSecurityPwd(){
		$.ajax({
            url: '<?php echo U("Buspublicmanage/checkSecurityPwd");?>',
            type: "POST",
            dataType: 'Json',
            data: {types:'txAudit'},
			async: false,
            success: function (ret) {
				if(ret.status){
					$flag = true;
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
<?php if($passtips != 'yes'): ?><form action="<?php echo U('WithdrawalManage/review');?>" id="pagerForm" onsubmit="return navTabSearch(this);" method="post">
		<div class="input_code_mian">
			<h3>用户提现查看</h3>
			<div>
				<input type="password" value="<?php echo ($checkPwds); ?>" id="passwords" name="checkPwds" autocomplete="off" placeholder="输入正确的密码才可以查询" />
			</div>
			<button id="submits" type="button">验证</button>
			<input id="forms" style="display: none" type="submit" />
		</div>

	</form><?php endif; ?>
<?php if($passtips == 'yes'): ?><form id="form" action="<?php echo U('BaManage/index');?>" onsubmit="return navTabSearch(this);" method="post">
		<input type="hidden" name="checkPwds" value="<?php echo ($checkPwds); ?>" />
	</form>

	<div class="pageContent">
		<div class="panelBar">
			<ul class="toolBar">
				<li><a class="edit" id="review" target="ajaxTodo" href="<?php echo U('DoReview');?>?ids={cid}" title="确认审核 "><span>审核</span></a></li>
				<li class="line"></li>
				<!-- <li><a class="add" href="<?php echo U('addPenny');?>?ids={cid}" target="ajaxTodo" title="确定要加一分吗?"><span>加一分</span></a></li> -->
			</ul>
		</div>
		<table class="table" width="100%" layoutH="100">
			<thead>
				<tr>
					<!-- <th width="3%" align="center"><input type="checkbox"
					group="busReview" class="checkboxCtrl"></th> -->
					<th width="5%" align="center">序号</th>
					<th width="10%" align="center">订单号</th>
					<th width="8%" align="center">商户姓名</th>
					<th width="8%" align="center">商户电话</th>
					<th width="8%" align="center">审核金额</th>
					<th width="8%" align="center">审核状态</th>
					<th width="8%" align="center">提现状态</th>
					<th width="10%" align="center">提现时间</th>
					<th width="13%" align="center">备注</th>
				</tr>
			</thead>
			<?php if($totalCount > 0): ?><tbody>
					<?php if(is_array($reArray)): $i = 0; $__LIST__ = $reArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="cid" rel="<?php echo ($v["id"]); ?>,<?php echo ($v["bid"]); ?>">
							<!-- <td><input name="busReview" id="busReview" value="<?php echo ($v["id"]); ?>"
					type="checkbox"></td> -->
							<td><?php echo ($i); ?></td>
							<td><?php echo ($v["ordernum"]); ?></td>
							<td><?php echo ($v["name"]); ?></td>
							<td><?php echo ($v["tel"]); ?></td>
							<td><span style="color: #CC0000; font-weight: bold;">
									<?php echo ($v['money']); ?></span></td>
							<td>
								<?php if($v['reviewStatus'] == 4): ?><span style="color: #FF6600; font-weight: bold;">未通过</span><?php endif; ?>
								<?php if($v['reviewStatus'] == 2): ?><span style="color: #660033; font-weight: bold;">未审核</span><?php endif; ?>
							</td>
							<td>
								<?php if($v['status'] == 1): ?><span style="color: #006633; font-weight: bold;">正常</span><?php endif; ?>
								<?php if($v['status'] == 2): ?><span style="color: #660033; font-weight: bold;">作废</span><?php endif; ?>
							</td>
							<td><?php echo ($v["createTime"]); ?></td>
							<!-- <td><a href="<?php echo U('verify');?>?id=<?php echo ($v["id"]); ?>" target="dialog"
					mask="true" width="450" height="410" title="审核确认"><span
						style="color: #006600; font-weight: bold;">审核</span></a></td> -->
							<td><?php echo ($v["remark"]); ?></td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				</tbody>
				<?php else: ?>
				<tbody>
					<tr>
						<td align="center" colspan="6" style="color: red;">抱歉，
							没有找到符合的记录！</td>
					</tr>
				</tbody><?php endif; ?>
		</table>
		<div class="statistics">
			<ul>
				<li><span>审核总数：共&nbsp;[&nbsp;<em><?php echo ($totalCount); ?></em>&nbsp;]&nbsp;条
					</span></li>
				<li><span>提现总金额：<em>&yen;&nbsp;<?php echo ($totalMoney); ?></em></span></li>
			</ul>
		</div>
		<style>
			.statistics {
				bottom: 28px;
				height: 40px;
				left: 0;
				position: absolute;
				width: 100%;
			}

			.statistics ul {
				overflow: hidden;
				text-align: center;
			}

			.statistics ul li {
				/*width:14%;*/
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

			.statistics ul li span {
				padding-left: 15px;
				display: block;
				line-height: 36px;
			}

			.statistics ul li span em {
				color: #996600;
				font-size: 14px;
			}

			.statistics ul li a {
				padding-left: 15px;
				color: #993333;
				display: block;
				line-height: 36px;
			}
		</style>
		<div class="panelBar">
			<div class="pages">
				<span>显示</span> <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
					<option value="20" <?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20
</option>
<option value="50" <?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50
</option>
<option value="100" <?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100
</option>
<option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200
</option>
</select> <span>条，共<?php echo ($totalCount); ?>条</span>
</div>
<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
 currentPage="<?php echo ($page); ?>"></div>
</div>
</div><?php endif; ?>