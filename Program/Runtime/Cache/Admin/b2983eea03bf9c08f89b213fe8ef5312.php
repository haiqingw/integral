<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
	<form method="post" addRes action="<?php echo U('addRes');?>?id=<?php echo ($id); ?>" enctype="multipart/form-data" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<div class="pageFormContent" layoutH="58" style="overflow:hidden;">
			<p style="width: 295px; margin-top: 20px;">
				<label style="width: 80px;">返回码：</label>
				<input name="code" type="text" size="20" value="<?php echo ($info["code"]); ?>"/>
			</p>
			<p style="width: 295px; margin-top: 20px;">
				<label style="width: 80px;">返回码描述：</label>
				<textarea name="msg" style="height:100px;width:150px;"><?php echo ($info["msg"]); ?></textarea>
			</p>
		</div>
		<div class="formBar">
			<ul style="float: right;">
				<li style="margin-right: 20px;">
					<div class="buttonActive">
						<div class="buttonContent">
							<button type="button" id="save">保存</button>
						</div>
					</div>
				</li>
				<li style="margin-right: 20px;">
					<div class="button">
						<div class="buttonContent">
							<button type="button" class="close">取消</button>
						</div>
					</div>
				</li>
			</ul>
		</div>
	</form>
</div>
<script type="text/javascript">
	//表单提交验证
    $('#save').click(function(){
    	var $code = $('input[name=code]').val();
    	var $msg = $('textarea[name=msg]').val();
    	if($code == ""){
    		alertMsg.warn("请输入返回码");
    		return;
    	}
    	if($msg == ""){
    		alertMsg.warn("请输入返回码原因");
    		return;
    	}
    	$('[addRes]').submit();
	});
</script>