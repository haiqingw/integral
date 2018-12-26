<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
	.dialogBackground {
		background-color: #000033;
	}
</style>
<div class="pageContent">
	<form method="post" CheckUs_form action="<?php echo U('Business/modifyStatus');?>" enctype="multipart/form-data" class="pageForm required-validate"
	 onsubmit="return validateCallback(this, dialogAjaxDone);">
		<div class="pageFormContent" layoutH="58" style="overflow: hidden;">
			<input type="hidden" name="id" id="id" value="<?php echo ($info["id"]); ?>" />
			<fieldset>
				<legend>用户操作</legend>
				<dl class="nowrap">
					<dt style="width: 80px;">商户状态：</dt>
					<dd style="padding-left: 30px;">
						<select name="status" id='status' class="combox">
							<option value="">请选择状态</option>
							<option value="1" <?php if($info['status'] == 1): ?>selected="selected"<?php endif; ?>>恢复</option>
							<option value="2" <?php if($info['status'] == 2): ?>selected="selected"<?php endif; ?>>冻结</option>
							<option value="3" <?php if($info['status'] == 3): ?>selected="selected"<?php endif; ?>>删除</option>
						</select>
					</dd>
				</dl>
			</fieldset>
		</div>
		<div class="formBar">
			<ul style="float: right;">
				<li style="margin-right: 20px;">
					<div class="buttonActive">
						<div class="buttonContent">
							<button type="submit" id="CheckUssave">提交</button>
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