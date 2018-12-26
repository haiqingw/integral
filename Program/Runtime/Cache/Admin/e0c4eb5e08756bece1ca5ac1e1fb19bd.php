<?php if (!defined('THINK_PATH')) exit();?>
<div class="pageContent">
	
	<form method="post" action="<?php echo U('editAttrRun');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
		<div class="pageFormContent" layoutH="58">
			<input type="hidden" name="id" value="<?php echo ($id); ?>" />
			<input type="hidden" name="oldKey" value="<?php echo ($key); ?>" />
			<div class="unit">
				<label>属性名称：</label>
				<input type="text" name="value" size="30" value="<?php echo ($attrDetail["value"]); ?>" class="required" />
			</div>
			<div class="unit">
				<label>属性字段：</label>
				<input type="text" name="key" size="30" value="<?php echo ($attrDetail["key"]); ?>" class="required"/>
			</div>
			<div class="unit">
				<label>属性类型：</label>
				<select name="type" class="combox required">
					<option value="text" <?php if($attrDetail['type'] == 'text'): ?>selected<?php endif; ?>>text</option>
					<option value="date" <?php if($attrDetail['type'] == 'date'): ?>selected<?php endif; ?>>date</option>
					<option value="dateCST" <?php if($attrDetail['type'] == 'dateCST'): ?>selected<?php endif; ?>>dateCST</option>
					<option value="money" <?php if($attrDetail['type'] == 'money'): ?>selected<?php endif; ?>>money</option>
					<option value="hidePhone" <?php if($attrDetail['type'] == 'hidePhone'): ?>selected<?php endif; ?>>hidePhone</option>
					<option value="number" <?php if($attrDetail['type'] == 'number'): ?>selected<?php endif; ?>>number</option>
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