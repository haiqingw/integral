<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
	<form method="post" action="<?php echo U('distributionfunction');?>" class="pageForm required-validate"
		  onsubmit="return validateCallback(this, navTabAjaxDone);">
		<input type="hidden" name="role_id" value="<?php echo ($info["role_id"]); ?>">

		<div class="pageFormContent" layoutH="100">
			<p style="clear: both;">
				<label>设置权限角色名称：</label>
				<input name="roleName" type="text" size="30" value="<?php echo ($info["role_name"]); ?>"/>
			</p>
			<p>
				<label><input onclick="changeAllCheckBox(this)" type="checkbox" value="1"/> 全选/全不选</label>
			</p>
            <style type="text/css">
                li{list-style: none;}
            </style>
            <script type="text/javascript">
            	function changeAllCheckBox(obj){
            		var $ck = ($(obj).attr('checked'));
            		if($ck == "checked"){
            			$("input[type=checkbox]").attr("checked","checked");
            		}else{
            			$("input[type=checkbox]").removeAttr("checked");
            		}
            	}
            	$(function(){
					$("div[qx-list] ul").each( function(index, Element){
						$(this).find("li:first ul:first li:first li input").click(function(){
							var $this=$(this);
							if($this.is(":checked")){
								if($this.closest("ul").find("li input:checked").length > 0){
									$this.closest("ul").parent("li").find("input:first").attr("checked","checked");
									$this.closest("ul").parent("li").closest("ul").parent("li").find("input:first").attr("checked","checked");
									}
								}else{
									if($this.closest("ul").find("li input:checked").length <= 0){
										$this.closest("ul").parent("li").find("input:first").removeAttr("checked");
										$this.closest("ul").parent("li").closest("ul").parent("li").find("input:first").removeAttr("checked");
										}
								}
							});
						$(this).find("li:first ul:first li:first input:first").click(function(){
							if($(this).is(":checked")){
								$(this).closest("ul").parent("li").find("input:first").attr("checked","checked");
								}else{
									$(this).closest("ul").parent("li").find("input:first").removeAttr("checked","checked");
									}
							});	
						});
					});
            </script>
            <div style="clear: both; margin-left: 130px; margin-top: 50px; margin-bottom: 20px;" qx-list>
                <?php if(is_array($pauthinfo)): foreach($pauthinfo as $key=>$v): ?><ul>
                        <li>
                            <?php echo ($v["auth_name"]); ?>
                            <input type="checkbox" value="<?php echo ($v["auth_id"]); ?>" name="lr_authName[]" <?php if(in_array($v['auth_id'],$auth_ids_arr)): ?>checked="checked"<?php endif; ?>>
                            <ul style="margin-left: 70px;">
                                <?php if(is_array($sauthinfo)): foreach($sauthinfo as $key=>$vv): if($vv['auth_pid'] == $v['auth_id']): ?><li>
                                            <?php echo ($vv["auth_name"]); ?>
                                            <input type="checkbox" value="<?php echo ($vv["auth_id"]); ?>" name="lr_authName[]" <?php if(in_array($vv['auth_id'],$auth_ids_arr)): ?>checked="checked"<?php endif; ?>>
                                            <ul style="margin-left: 70px">
                                                <?php if(is_array($tauth_info)): $i = 0; $__LIST__ = $tauth_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vvv): $mod = ($i % 2 );++$i; if($vvv['auth_pid'] == $vv['auth_id']): ?><li>
                                                            <?php echo ($vvv["auth_name"]); ?>
                                                            <input type="checkbox" value="<?php echo ($vvv["auth_id"]); ?>" name="lr_authName[]" <?php if(in_array($vvv['auth_id'],$auth_ids_arr)): ?>checked="checked"<?php endif; ?>>
                                                        </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                            </ul>
                                        </li><?php endif; endforeach; endif; ?>
                            </ul>
                        </li>
                    </ul><?php endforeach; endif; ?>
            </div>

		 </div>
		<div class="formBar">
			<ul style="float: left">
				<li style="margin-left: 100px;">
					<div class="buttonActive">
						<div class="buttonContent">
							<button type="submit">保存</button>
						</div>
					</div>
				</li>
				<li style="margin-left: 20px;">
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