<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('Model/arealist');?>">
    <input type="hidden" name="status" value="${param.status}">
    <input type="hidden" name="keywords" value="${param.keywords}" />
    <input type="hidden" name="pageNum" value="1" />
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
    <input type="hidden" name="orderField" value="${param.orderField}" />
</form>

<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="add" href="<?php echo U('areaadd');?>" target="navTab"><span>添加</span></a></li>
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('areamodify');?>?areaid={sid_user}" target="navTab"><span>修改</span></a></li>
            <li class="line">line</li>
            <li><a class="delete" href="<?php echo U('areadel');?>?areaid={sid_user}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
        </ul>
    </div>
    <table class="table" width="55%" layoutH="76">
        <thead>
        <tr>
            <th width="30" align="center">模块ID</th>
            <th width="50" align="center">模块名称</th>
            <th width="80" align="center">备注</th>
            <th width="50" align="center">创建时间</th>
            <th width="50" align="center">创建IP</th>
            <th width="80" align="center">排序</th>
			<th width="80" align="center">系统名称</th>
        </tr>
    </thead>
        <?php if($totalCount > 0): ?><tbody>
        <?php if(is_array($info)): foreach($info as $key=>$v): ?><tr target="sid_user" rel="<?php echo ($v["model_ID"]); ?>">
                <td align="center"><?php echo ($v["model_ID"]); ?></td>
                <td align="center"><?php echo ($v["model_Name"]); ?></td>
                <td align="center"><?php echo ($v["model_Remark"]); ?></td>
                <td align="center"><?php echo ($v["model_Createtime"]); ?></td>
                <td align="center"><?php echo ($v["model_Createip"]); ?></td>
                <td><input type="hidden" name="cid" value="<?php echo ($v["model_ID"]); ?>" /> <input type="text" name="sortno" value="<?php echo ($v["model_Sortno"]); ?>" checkval="<?php echo ($v["model_Sortno"]); ?>" onkeyup="check_mod(event,this)" maxlength="5" style="border: none; width: 80px;text-align: center;" />　<a style=" cursor: pointer; color:#247CE8; display:none; text-decoration: none;" onclick="saves()" sav>保存</a></td>
				<td align="center"><?php echo ($v["model_SystemName"]); ?></td>
            </tr><?php endforeach; endif; ?>
        </tbody>
        <?php else: ?>
            <tbody>
                <tr>
                    <td align="center" colspan="7" style="color:red;">抱歉， 没有找到符合的记录！</td>
                </tr>
            </tbody><?php endif; ?>
    </table>
    <div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
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
<script type="text/javascript">
    //检查
    function check_mod(e,o){
        var $this = $(o);
        var v=o.value|0;
            if(v<=0){
                o.value=o.value.replace(/\D/g,'');
                o.focus();
            }
        if ($this.val()==$this.attr('checkval')) {
            $this.next('a').hide();
        } else{
            $this.next('a').show();
        };
    }

    //保存
    function saves() {
        var $this = $('[sav]:visible');
        var cidarr = new Array(); //定义字符串数组
        var valarr = new Array(); //定义字符串数组
        $.each($this, function(index, val) {
             cidarr.push($(this).parents('td').find('input[name="cid"]').val()); //向数组最后插入记录
             valarr.push($(this).parents('td').find('input[name="sortno"]').val()); //向数组最后插入记录
        });

        //异步保存
        $.ajax({
            url: '<?php echo U("sortno");?>',
            type: 'POST',
            dataType: 'json',
            data: {cidarr:cidarr,valarr:valarr},
            success:function (data) {
                if (data.statusCode==200) {
                    navTab.reload("", {navTabId: ""});
                    alertMsg.correct(data.message);
                } else{
                    alertMsg.error(data.message);
                };
            }
        });
    }
</script>