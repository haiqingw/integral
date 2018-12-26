<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('category');?>" onsubmit="return navTabSearch(this);">
    <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo ($limit); ?>" />
</form>
<script>
    //关闭弹出框
    if ("<?php echo ($state); ?>" == 'success') {
        //$.pdialog.close('addviewProv');
    }
</script>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="add" href="<?php echo U('addCategory');?>" target="navTab" rel="addCategory" width="320" height='180'><span>添加</span></a></li>
            <li><a class="edit" href="<?php echo U('addCategory');?>?cid={cid}" target="navTab" rel="addCategory" width="320"
                    height='180'><span>修改</span></a></li>
            <li><a class="delete" href="<?php echo U('delCategoryProcess');?>?cid={cid}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
        </ul>
    </div>
    <table class="table" width="100%" layoutH="75">
        <thead>
            <tr>
                <th width="10%" align="center">序号</th>
                <th width="20%" align="center">品牌名称</th>
                <th width="20%" align="center">产品名称</th>
                <th width="15%" align="center">规则模板</th>
                <th width="15%" align="center">添加时间</th>
                <th width="10%" align="center">状态</th>
                <th width="10%" align="center">操作</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="cid" rel="<?php echo ($v["id"]); ?>">
                        <td align="center"><?php echo ($i); ?></td>
                        <td align="center"><?php echo ($v["brandName"]); ?></td>
                        <td align="center"><?php echo ($v["productName"]); ?></td>
                        <td align="center"><?php echo ($v["ruleList"]); ?></td>
                        <td align="center"><?php echo (dateformat($v["addtime"])); ?></td>
                        <td align="center">
                            <?php if($v["status"] == 1): ?><font color=#0099FF>正常</font><?php endif; ?>
                            <?php if($v["status"] == 2): ?><font color=#FF0000>停用中</font><?php endif; ?>
                        </td>
                        <td align="center">
                            <?php if($v["status"] == 1): ?><a href="<?php echo U('stopCategory');?>?cid=<?php echo ($v["id"]); ?>" target="ajaxTodo"
                                    title="停用后公司无法添加模板！确定要停用？">
                                    <font color=#FF0000>停用</font>
                                </a><?php endif; ?>
                            <?php if($v["status"] == 2): ?><a href="<?php echo U('openCategory');?>?cid=<?php echo ($v["id"]); ?>" target="ajaxTodo"
                                    title="是否开启该分类？">
                                    <font color=#0099FF>开启</font>
                                </a><?php endif; ?>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
            <?php else: ?>
            <tbody>
                <tr>
                    <td align="center" colspan="2" style="color:red;">抱歉， 没有找到符合的记录！</td>
                </tr>
            </tbody><?php endif; ?>
    </table>
    <div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value})">
                <option value="20" <?php if($limit == 20): ?>selected<?php endif; ?>>20</option>
                <option value="50" <?php if($limit == 50): ?>selected<?php endif; ?>>50</option>
                <option value="100" <?php if($limit == 100): ?>selected<?php endif; ?>>100</option>
                <option value="200" <?php if($limit == 200): ?>selected<?php endif; ?>>200</option>
            </select>
            <span>条，共<?php echo ($totalCount); ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($limit); ?>" pageNumShown="10"
            currentPage="<?php echo ($page); ?>"></div>
    </div>
</div>