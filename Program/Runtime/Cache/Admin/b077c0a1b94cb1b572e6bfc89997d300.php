<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('HomeModule/index');?>">
	<input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
</form>
<div class="pageContent" id="mainContent">
	<form id="SlideForms" method="post" action="<?php echo U('HomeModule/index');?>" onsubmit="return navTabSearch(this);"></form>
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="<?php echo U('HomeModule/add');?>" target="dialog" mask ="true" rel=""><span>添加</span></a></li>
			<li class="line"></li>
			<li><a class="edit" href="<?php echo U('HomeModule/dismodule');?>?mid={mdid}" target="dialog" mask ="true" height="500" width="700" rel=""><span>模块分配</span></a></li>
			<li class="line"></li>
			<li><a class="edit" href="<?php echo U('HomeModule/typemodify');?>?mid={mdid}" target="dialog" mask ="true"  rel=""><span>修改</span></a></li>
			<li class="line"></li>
			<li><a class="delete" href="<?php echo U('HomeModule/tDelete');?>?tid={mdid}" target="ajaxTodo" title="确定删除么"  rel=""><span>删除</span></a></li>
			<li class="line"></li>

		</ul>
	</div>
	<table width="50%" class="table" layoutH="76">
		<thead>
			<tr>
				<th align="center" width="10%">序号</th>
				<th align="center" width="10%">名称</th>
				<th align="center" width="30%">模块名称</th>

			</tr>
		</thead>
		<tbody>

		<?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="mdid" rel="<?php echo ($vo["ModuleTypeID"]); ?>">
				<td><?php echo ($vo["ModuleTypeID"]); ?></td>
				<td><?php echo ($vo["ModuleTypeName"]); ?></td>
				<td><?php echo ($vo["HomeModuleID"]); ?></td>


			</tr><?php endforeach; endif; else: echo "" ;endif; ?>

		</tbody>
	</table>

	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
				<option value="40" <?php if($numPerPage == 40): ?>selected="selected"<?php endif; ?>>40</option>
				<option value="80" <?php if($numPerPage == 80): ?>selected="selected"<?php endif; ?>>80</option>
				<option value="120" <?php if($numPerPage == 120): ?>selected="selected"<?php endif; ?>>120</option>
				<option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200</option>
			</select>
			<span>条，共<?php echo ($totalCount); ?>条</span>
		</div>

		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>
<script type="text/javascript">
        function homeChangeOrd(obj, id) {
            var val = obj.text();
            var c = obj.parent("div");
            obj.parent("div").html("<input type='text' style='text-align:center;width:50px;' onFocus=this.select()  onblur=homeChangeOrdConfirm($(this)," + id + ") value='" + val + "' />");

            c.children("input").focus();
        }
        function homeChangeOrdConfirm(obj, id) {
            var ord = obj.val();
            $.ajax({
                url: "<?php echo U('HomeModule/Sort');?>",
                type: "POST",
                async: false,
                data: {hid: id, sortNum: ord},
                success: function (data) {
                    $('#SlideForms').submit();
                }
            });
        }
</script>