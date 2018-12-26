<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
.dialogBackground {
	background-color: #000033;
}
</style>
<div class="pageContent">
	<div class="pageFormContent" layoutH="58">
		<div class="unit">
			<input type="hidden" id="idsm" name="ids" value="<?php echo ($ids); ?>" />
			<fieldset>
				<legend>机具调拨</legend>
				<dl class="nowrap">
					<dt>选择用户：</dt>
					<dd>
						<input type="hidden" id="belongIDDialog" name="orgLookup.id" value="" /> <input type="text" name="orgLookup.name" size="30" style="width: 100px;" value="" suggestFields="name,phone" lookupGroup="orgLookup" readonly="readonly" class="required" /> <a class="btnLook" href="<?php echo U('searchBack');?>" lookupGroup="orgLookup" rel="lookup">查找带回</a>
					</dd>
				</dl>
			</fieldset>
		</div>
		<table class="table" width="100%" layoutH="200">
			<thead>
				<tr>
					<th align="center">序号</th>
					<th align="center">批次</th>
					<th align="center">POS机终端码</th>
					<th align="center">调拨状态</th>
				</tr>
			</thead>
			<tbody>
				<?php if(is_array($arrays)): $i = 0; $__LIST__ = $arrays;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
					<td><?php echo ($i); ?></td>
					<td><?php echo ($v["batchNo"]); ?></td>
					<td><?php echo ($v["terminalNo"]); ?></td>
					<td><?php if($v['allotStatus'] == 1): ?>未调拨<?php else: ?>已调拨<?php endif; ?></td>

				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
		</table>
	</div>
	<div class="formBar">
		<ul style="float: left">
			<li style="margin-left: 20px;">
				<div class="buttonActive">
					<div class="buttonContent">
						<button type="button" onclick="batchDialCode()">调拨</button>
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

</div>
<script>
	function batchDialCode() {
		var belongID = $('#belongIDDialog').val();
		var $ids = $('#idsm').val();
		if (belongID == "") {
			layer.alert("请选择服务商");
			return;
		}
		var $layer = layer.confirm('确认要调拨到该服务商下吗？', {
			btn : [ '确定', '取消' ], //按钮
			icon : 3
		}, function() {
			layer.close($layer);
			$.ajax({
				url : "<?php echo U('MachineManage/checkDialCode');?>",
				data : {
					belongID : belongID,
					ids : $ids
				},
				type : "post",
				dataType : "json",
				success : function(ret) {
					if (ret.status) {
						$.pdialog.closeCurrent(); //这行js就是执行完以上的js之后关闭该dialog弹出框
						navTab.reload(); //刷新当前页 
						alertMsg.correct("调拨成功");
					} else {
						alertMsg.error(ret.msg);
					}
				}
			});
		}, function() {
		});
	}
</script>