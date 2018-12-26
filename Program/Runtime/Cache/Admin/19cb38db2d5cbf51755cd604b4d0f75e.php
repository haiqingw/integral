<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('HomeModule/index');?>">
	<input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
	<input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
</form>
<div class="pageContent" id="mainContent">
	<form id="SlideForms" method="post" action="<?php echo U('HomeModule/index');?>" onsubmit="return navTabSearch(this);"></form>
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="<?php echo U('HomeModule/upload');?>" target="navTab" rel=""><span>添加</span></a></li>
			<li class="line"></li>
			<li><a class="edit" href="<?php echo U('HomeModule/modify');?>?hid={SID}" target="navTab"  rel=""><span>修改</span></a></li>
			<li class="line"></li>
			<li><a class="delete" href="<?php echo U('HomeModule/Delete');?>?hid={SID}" target="ajaxTodo" title="确定删除么"  rel=""><span>删除</span></a></li>
			<li class="line"></li>

		</ul>
	</div>
	<table width="100%" class="table" layoutH="76">
		<thead>
			<tr>
				<th align="center" width="3%">序号</th>
				<th align="center" width="11%">排序</th>
				<th align="center" width="11%">模块名称</th>
				<th align="center" width="8%">图标</th>
				<th align="center" width="8%">页面地址</th>
				<th align="center" width="8%">控制器</th>
				<th align="center" width="10%">创建时间</th>
				<!--<th align="center" width="10%">使用状态</th>-->
				<th align="center" width="8%">操作</th>
			</tr>
		</thead>
		<tbody>

		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="SID" rel="<?php echo ($vo["hid"]); ?>">
				<td><?php echo ($i + $offset); ?></td>
				<td  style="position:relative;"><a style="display:block;background-color:#dec8c8;width:50%;height:25px;line-height:25px; margin:0 auto;" onclick="homeChangeOrd($(this), <?php echo ($vo['hid']); ?>)" target=""><?php echo ($vo["hsort"]); ?></a></td>
				<td><?php echo ($vo["hname"]); ?></td>
				<td>
					<a style="line-height:25px;" href="<?php echo U('HomeModule/preview');?>?hid=<?php echo ($vo["hid"]); ?>" target="dialog" mask="true" maxable="false" resizable="false" height="611" width="345" title="<?php echo ($vo["hname"]); ?>"><img src="<?php echo ($vo["hpicUrl"]); ?>" width="20" height="20" ></a>	
				</td>
				<td><?php echo ($vo["hHtmlUrl"]); ?></td>
				<td><?php echo ($vo["hControllerUrl"]); ?></td>
				<td><?php echo ($vo["hcreateTime"]); ?></td>
				<td>
			<?php if($vo['hstatus'] == 1): ?><a href="<?php echo U('HomeModule/UseStatus');?>?hid=<?php echo ($vo["hid"]); ?>&uStatus=<?php echo ($vo["hstatus"]); ?>" target="ajaxTodo" title='点击停用'><span style="color:#f73c25;line-height:22px; font-weight:bold;">停用</span></a>
				<?php else: ?>
				<a href="<?php echo U('HomeModule/UseStatus');?>?hid=<?php echo ($vo["hid"]); ?>&uStatus=<?php echo ($vo["hstatus"]); ?>" target="ajaxTodo" title='点击启用'><span style="color:#7e8025;line-height:22px; font-weight:bold;">启用</span></a><?php endif; ?>
			</td>
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