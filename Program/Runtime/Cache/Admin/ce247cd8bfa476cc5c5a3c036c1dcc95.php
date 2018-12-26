<?php if (!defined('THINK_PATH')) exit();?><style>
.amend_icon {
	background: rgba(0, 0, 0, 0)
		url("/Public/partner/images/amend_icon.png") no-repeat 82px center/20px
		auto;
	text-indent: 25px;
}
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
</style>
<div class="pageContent">
	<form method="post" submit action="<?php echo U('createAccount');?>"
		class="pageForm required-validate"
		onsubmit="return validateCallback(this, dialogAjaxDone);"
		enctype="multipart/form-data">
		<div class="pageFormContent" layoutH="56">
			<div class="unit">
				<fieldset>
					<legend>登陆账号</legend>
					<dl class="nowrap">
						<dt>手机号码</dt>
						<dd class="canClick amend_icon" data-key="phone"><?php echo ($info["phone"]); ?></dd>
					</dl>
					<dl class="nowrap">
						<dt>商户名称</dt>
						<dd class="canClick amend_icon" data-key="busname"><?php echo ($info["busname"]); ?></dd>
					</dl>
					<dl class="nowrap">
						<dt>重置密码</dt>
						<dd class="amend_icon"><a id="detailOrder" class="detailOrder" href="<?php echo U('Business/resetPassword');?>?id=<?php echo ($info["id"]); ?>" target="ajaxTodo" title="重置密码">重置密码</a></dd>
					</dl>
				</fieldset>
			</div>
		</div>
		<div class="formBar">
			<ul style="float: left">
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
<!-- input表单 修改 -->
<script type="text/javascript">
var thisInput;
$(function() {
	$('.canClick').click(function() {
		thisInput = this;
		var $id = "<?php echo ($info["id"]); ?>";
		var $html = $(this).html();
		if ($(this).find('input').length == 0) {
			$(this).html("");
			$(this).append("<input type='text' style='text-align:center;width:50%;' value='"+ $html + "'/>");
			$(this).find('input').focus().select();
			$(this).find('input').blur(function() {
				$.ajax({
					url : "<?php echo U('Business/doModify');?>",
					type : "post",
					data : {
						id : $id,
						key : $(this).parent().attr('data-key'),
						val : $(this).val()
					},
					dataType : "json",
					success : function(ret) {
						if (ret.status == 1) {
							$("td[data-id='"+$(thisInput).attr('data-key')+$id+"']").html(ret.data);
							/*$.pdialog.closeCurrent(); //这行js就是执行完以上的js之后关闭该dialog弹出框
							navTab.reload(); //刷新当前页  */
							alertMsg.correct(ret.msg);
							$(thisInput).html(ret.data);
							/* setTimeout(window.location.reload(), 4000); */ //定时刷新当前页面	
						} else {
							alertMsg.correct(ret.msg);
							$(thisInput).html($html);
						}

					}
				});	 
		 	});
		}
	});	
})

</script>