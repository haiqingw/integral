<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="">
	<input type="hidden" name="status" value="${param.status}">
    <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
    <input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" />
</form>
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="" method="post" id="BlacklistIndex">
        <div class="searchBar">
            <table class="searchContent">
                <tbody><tr>
                    <td align="left">
                    </td>
                    <td align="right">
                        关键词：<input type="text" value="" id="keywords" name="keywords" autocomplete="off" placeholder="返回码，返回码原因" style="width: 160px;" class="textInput">
                    </td>
                <td align="left">
                    <div class="buttonActive"><div class="buttonContent"><button type="submit">检索</button></div></div>
                </td>
                </tr>
            </tbody></table>
        </div>
    </form>
</div>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="<?php echo U('add');?>" width="330" height="330" rel="addRes" target="dialog"><span>添加</span></a></li>
			<li class="line"></li>
			<li><a class="edit" href="<?php echo U('add');?>?id={id}" width="330" height="330" target="dialog" rel="addRes"><span>修改</span></a></li>
			<li class="line"></li>
			<li><a class="delete" href="<?php echo U('delete');?>?id={id}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li class="line">line</li>
			<li><a class="icon" href="<?php echo U('update');?>" target="ajaxTodo"><span>更新Data文件</span></a></li>
			<li class="line">line</li>
			<li style="line-height:25px;"><b style="color:red"><?php echo BASEURL; ?>/Public/js/response.js</b></li>
		</ul>
	</div>
	<table width="50%" class="table" layoutH="115">
		<thead>
		<tr>
			<th align="center">返回码（resCode）</th>
			<th align="center">返回码描述（resMsg）</th>
		</tr>
		</thead>
		<tbody>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr target="id" rel="<?php echo ($vo["id"]); ?>">
				<td><?php echo ($vo["code"]); ?></td>
				<td><?php echo ($vo["msg"]); ?></td>
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>

	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
				<option value="20" <?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20</option>
				<option value="50" <?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50</option>
				<option value="100" <?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100</option>
				<option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200</option>
				
			</select>
			<span>条，共<?php echo ($totalCount); ?>条</span>
		</div>
		
		<div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10" currentPage="<?php echo ($page); ?>"></div>
	</div>
</div>
<script src="/Public/js/response.js"></script>
<script>
//alert(response[1001]);
$(function(){
	if("<?php echo ($state); ?>" == "success"){
		$.pdialog.close("addRes");
	}
});
</script>