<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
	.dialogBackground {
		background-color: #000033;
	}
</style>
<div class="pageContent">
	<form method="post" submitButton action="<?php echo U('Business/doAdd');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);"
	 enctype="multipart/form-data">
		<div class="pageFormContent" layoutH="56">
			<div class="unit">
				<fieldset>
					<legend>登陆账号</legend>
					<dl class="nowrap">
						<dt>商户名称</dt>
						<dd>
							<input type="text" id="name" name="name" value="" emp="{empty:true}" mess="填写商户名称" />
						</dd>
					</dl>
					<dl class="nowrap">
						<dt>手机号码</dt>
						<dd>
							<input type="text" id="phone" name="phone" value="" emp="{empty:true,tel:true}" mess="填写手机号码" maxlength="11" />
						</dd>
					</dl>
					<dl class="nowrap">
						<dt>商户级别</dt>
						<dd>
							<input type="hidden" id="level" name="level" value="<?php echo ($info["englishname"]); ?>" />
							<input type="text" id="" name="" value="<?php echo ($info["classname"]); ?>" readonly="readonly" />
						</dd>
					</dl>
					<dl class="nowrap">
						<dt>登陆密码</dt>
						<dd>
							<input type="text" id="password" name="password" value="p123456" emp="{empty:true,zcpass:false}" mess="填写登陆密码" />
						</dd>
					</dl>
				</fieldset>
			</div>
		</div>
		<div class="formBar">
			<ul style="float: left">
				<li style="margin-left: 125px; margin-right: 35px">
					<div class="buttonActive">
						<div class="buttonContent">
							<button type="button" id="submitButton">提交</button>
						</div>
					</div>
				</li>
				<li>
					<div class="button">
						<div class="buttonContent">
							<button type="button" class="close">返回列表</button>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</form>
</div>
<script type="text/javascript">
	function isPhoneNo(phone) {
		var pattern = /^1[34578]\d{9}$/;
		return pattern.test(phone);
	}
	//提交表单
	$(function () {
		$('#submitButton').click(function () {
			var $phone = $("#phone").val();
			if (!$phone) {
				alertMsg.warn("手机号不能为空");
				return false;
			} else {
				if (isPhoneNo($phone) == false) {
					alertMsg.warn("手机号格式不正确");
					return false;
				} else {
					var $layer = layer.confirm('确认要添加么？', {
						btn: ['确定', '取消'], //按钮
						icon: 3
					}, function () {
						layer.close($layer);
						$('[submitButton]').submit();
					}, function () {});
				}
			}
		});
	})
</script>