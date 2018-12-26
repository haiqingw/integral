<?php if (!defined('THINK_PATH')) exit();?>
<div class="pageContent">
	
	<form method="post" action="<?php echo U('addAttrRun');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
		<div class="pageFormContent" layoutH="58">
			<input type="hidden" name="id" value="<?php echo ($id); ?>" />
			<div class="unit">
				<label>属性名称：</label>
				<input type="text" name="value" size="30" class="required" />
			</div>
			<div class="unit">
				<label>属性字段：</label>
				<input type="text" name="key" size="30" class="required"/>
			</div>
			<div class="unit">
				<label>属性类型：</label>
				<select name="type" class="combox required">
					<option value="text">text</option>
					<option value="date">date</option>
					<option value="dateCST">dateCST</option>
					<option value="money">money</option>
					<option value="hidePhone">hidePhone</option>
					<option value="number">number</option>
				</select>
			</div>
			
		</div>
		<div class="formBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">提交</button></div></div></li>
				<li><div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div></li>
			</ul>
		</div>
	</form>
	
</div>