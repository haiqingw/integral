<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('Class/index');?>" onsubmit="return navTabSearch(this);">
    <input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" />
    <input type="hidden" name="model" value="<?php echo ($model); ?>" />
    <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
</form>
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php echo U('Class/index');?>" method="post" id="ClassIndex">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td align="left">
                    </td>
                    <td align="right">
                        关键词：<input type="text" value="<?php echo ($keywords); ?>" id="keywords" name="keywords" autocomplete="off"
                            placeholder="请输入分类名称" style="width: 160px;" value="<?php echo ($keywords); ?>"/>
                    </td>

                    <td align="left">
                        <div class="buttonActive">
                            <div class="buttonContent"><button type="submit">检索</button></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>
<div class="pageContent">
    <div class="panelBar">
        <ul class="toolBar">
            <li><a class="add" href="<?php echo U('add');?>" target="dialog"><span>添加</span></a></li>
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('update');?>?id={sid_user}" target="dialog"><span>修改</span></a></li>
            <li class="line">line</li>
            <li><a title="确定要删除这些记录吗?" target="selectedTodo" rel="decldel" postType="string" href="<?php echo U('Class/del');?>"
                    class="delete"><span>删除</span></a></li>
        </ul>
    </div>
    <table class="table" width="80%" layoutH="160">
        <thead>
            <tr>
                <th width="2%"><input type="checkbox" group="decldel" class="checkboxCtrl"></th>
                <th width="50" align="center">ID</th>
                <th width="80" align="left">分类名称</th>
                <th width="80" align="left">所属分类</th>
                <th width="80" align="center">分类图片</th>
                <th width="80" align="center">排序</th>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr target="sid_user" rel="<?php echo ($v["id"]); ?>">
                    <td><input name="decldel" value="<?php echo ($v["id"]); ?>" type="checkbox"></td>
                    <td align="center"><?php echo ($v["id"]); ?></td>
                    <td align="center"><?php echo ($v["name"]); ?></td>
                    <td align="center">
                        <?php if($v['pId'] == 0): ?>顶级分类
                            <?php else: echo ($v['parentname']); endif; ?>
                    </td>
                    <td align="center"><img style="height:100%;" src="<?php echo ($v["photo"]); ?>" /></td>
                    <td align="center"><?php echo ($v["sort"]); ?></td>
                </tr><?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
                <option value="20" <?php if($numPerPage == 20): ?>selected="selected"<?php endif; ?>>20
                </option>
                <option value="50" <?php if($numPerPage == 50): ?>selected="selected"<?php endif; ?>>50
                </option>
                <option value="100" <?php if($numPerPage == 100): ?>selected="selected"<?php endif; ?>>100
                </option>
                <option value="200" <?php if($numPerPage == 200): ?>selected="selected"<?php endif; ?>>200
                </option>
            </select>
            <span>条，共<?php echo ($totalCount); ?>条</span>
        </div>
        <div class="pagination" targetType="navTab" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
            currentPage="<?php echo ($page); ?>"></div>
    </div>
</div>
<script>
function searchByClass(){
    $('#ClassIndex').submit();
}
</script>