<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('Role/rolelist');?>">
    <input type="hidden" name="status" value="${param.status}">
    <input type="hidden" name="keywords" value="${param.keywords}" />
    <input type="hidden" name="pageNum" value="1" />
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
    <input type="hidden" name="orderField" value="${param.orderField}" />
</form>

<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="add" href="<?php echo U('roleadd');?>" target="navTab"><span>添加</span></a></li>
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('rolemodify');?>?roleid={sid_user}" target="navTab"><span>修改</span></a></li>
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('distribution');?>?roleid={sid_user}" target="navTab"><span>分配权限</span></a></li>
            <li class="line">line</li>
            <!-- <li><a class="delete" href="<?php echo U('roledel');?>?roleid={sid_user}" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li> -->
        </ul>
    </div>
    <table class="table" width="70%" layoutH="76">
        <thead>
            <tr>
                <th width="30" align="center">角色ID</th>
                <th width="50" align="center">角色名称</th>
                <th width="50" align="center">角色类型</th>
                <th width="50" align="center">代理类型</th>
                <th width="50" align="center">创建时间</th>
                <th width="50" align="center">创建IP</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
        <?php if(is_array($info)): foreach($info as $key=>$v): ?><tr target="sid_user" rel="<?php echo ($v["role_id"]); ?>">
                <td align="center"><?php echo ($v["role_id"]); ?></td>
                <td align="center"><?php echo ($v["role_name"]); ?></td>
                <td align="center">
                    <?php switch($v["role_type"]): case "1": ?>管理<?php break;?>
                        <?php case "2": ?>代理<?php break;?>
                        <?php default: endswitch;?>
                </td>
                <td align="center">
                    <?php switch($v["role_agtp"]): case "1": ?>单级<?php break;?>
                        <?php case "2": ?>多级<?php break;?>
                        <?php default: ?>无<?php endswitch;?>
                </td>
                <td align="center"><?php echo ($v["role_createtime"]); ?></td>
                <td align="center"><?php echo ($v["role_createip"]); ?></td>
            </tr><?php endforeach; endif; ?>
        </tbody>
        <?php else: ?>
            <tbody>
                <tr>
                    <td align="center" colspan="4" style="color:red;">抱歉， 没有找到符合的记录！</td>
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