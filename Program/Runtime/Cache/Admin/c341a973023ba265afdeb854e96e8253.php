<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
	<form method="post" action="<?php echo U('authmodifyfunction');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
		<input type="hidden" name="authID" value="<?php echo ($authInfo["auth_id"]); ?>">
		<div class="pageFormContent" layoutH="500">
			<p style="clear: both;" areaShowID>
				<label>显示区域：</label>
				<select name="areaShowID" class="required combox">
					<option value="" selected="selected">请选择显示区域</option>
					<?php if(is_array($info)): foreach($info as $key=>$v): ?><option value="<?php echo ($v["model_ID"]); ?>" <?php if($v['model_ID'] == $authInfo['auth_area_id']): ?>selected="selected"<?php endif; ?>><?php echo ($v["model_Name"]); ?>&nbsp;|&nbsp;(<?php echo ($v["SystemModuleName"]); ?>)</option><?php endforeach; endif; ?>
				</select>
			</p>
			<p style="clear: both; width: 100%">
				<font style="margin-left: 130px; color: #FF0000;">提示:要显示的位置[例如系统管理]</font>
			</p>
			<p style="clear: both;">
				<label>权限名称：</label>
				<input name="auth_Name" class="required" type="text" size="30" value="<?php echo ($authInfo["auth_name"]); ?>" alt="请输入输入权限名称"/>
			</p>
			<p style="clear: both;" auth_pid >
				<label>所属父级：</label>
				<select name="auth_pid" class="combox">
					<option value="" selected="selected">请选择所属父级</option>
					<option value="0" <?php if($pv['auth_level'] == 0): ?>selected="selected"<?php endif; ?>>顶级</option>
					<?php if(is_array($pauthinfo)): foreach($pauthinfo as $key=>$pv): ?><option value="<?php echo ($pv["auth_id"]); ?>" <?php if($pv['auth_id'] == $authInfo['auth_pid']): ?>selected="selected"<?php endif; ?>>
                            <?php switch($pv["auth_level"]): case "1": ?>　　┗&nbsp;<?php break;?>
                                <?php case "2": ?>　　　　┗&nbsp;<?php break;?>
                                <?php default: endswitch;?>
                            <?php echo ($pv["auth_name"]); ?>
                        </option><?php endforeach; endif; ?>
				</select>
			</p>
			<p style="clear: both;">
				<label>控制器名称：</label>
				<input name="auth_controllerName" type="text" size="30" value="<?php echo ($authInfo["auth_c"]); ?>" alt="请输入输入控制器名称"/>
			</p>
			<p style="clear: both;">
				<label>方法名称：</label>
				<input name="auth_functionName" type="text" size="30" value="<?php echo ($authInfo["auth_a"]); ?>" alt="请输入输入方法名称"/>
			</p>
			<p style="clear: both; width: 100%">
				<font style="margin-left: 130px; color: #FF0000;">提示:栏目顶级可不填添加控制器和方法名称</font>
			</p>
		</div>
		<div class="formBar">
			<ul style="float:left">
				<li style="margin-left: 125px; margin-right: 35px">
					<div class="buttonActive">
						<div class="buttonContent">
							<button type="submit">保存</button>
						</div>
					</div>
				</li>
				<li>
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