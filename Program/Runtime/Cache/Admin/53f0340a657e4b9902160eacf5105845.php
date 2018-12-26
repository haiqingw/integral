<?php if (!defined('THINK_PATH')) exit();?>
<div class="pageHeader" style="border:1px #B8D0D6 solid">
        <form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBox');" action="<?php echo U('Users/lists');?>" method="post">
            <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
            <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
            <input type="hidden" name="level" value="<?php echo ($level); ?>" />
            <input type="hidden" name="plat" value="<?php echo ($plat); ?>" />
            <input type="hidden" name="keyword" value="<?php echo ($keyword); ?>" />
            <div class="searchBar">
                <table class="searchContent">
                    <tr>
                        <td>
                            用户名称：
                            <input type="text" name="keyword" value="<?php echo ($keyword); ?>"/>
                        </td>
                        <td>
                            <div class="buttonActive">
                                <div class="buttonContent">
                                    <button type="submit">检索</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>
    
    <div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid">
        <div class="panelBar">
            <ul class="toolBar">
            </ul>
        </div>
        <table class="table" width="99%" layoutH="260" rel="jbsxBox">
            <thead>
                    <tr>
                            <th width="2%"><input type="checkbox" group="decldel" class="checkboxCtrl"></th>
                            <th width="50" align="center">ID</th>
                            <th width="150" align="left">头像</th>
                            <th width="80" align="left">商户名称</th>
                            <th width="80" align="center">商户级别</th>
                            <th width="80" align="center">联系电话</th>
                            <th width="150" align="center">注册时间</th>
                            <th width="80" align="center">商户状态</th>
                            <th width="150" align="center">上级</th>
                            <th width="150" align="center">邀请码</th>
                            <th width="150" align="center">操作</th>
                        </tr>
            </thead>
            <tbody>
                    <?php if(is_array($data)): foreach($data as $key=>$v): ?><tr target="sid_user" rel="<?php echo ($v["id"]); ?>">
                                <td><input name="decldel" value="<?php echo ($v["id"]); ?>" type="checkbox"></td>
                                <td align="center"><?php echo ($v["id"]); ?></td>
                                <td align="center"><img src="<?php echo ($v['ImagePath']); ?>"/></td>
                                <td align="center"><?php echo ($v['busname']); ?></td>
                                <td align="center"><?php echo ($v["level"]); ?></td>
                                <td align="center"><?php echo ($v["phone"]); ?></td>
                                <td align="center"><?php echo (date("Y-m-d H:i",$v["regisTime"])); ?></td>
                                <td align="center"><?php switch($v["status"]): case "1": ?>正常<?php break; case "2": ?>冻结<?php break; case "3": ?>删除<?php break; endswitch;?></td>
                                <td align="center"><?php echo ($parents[$key]); ?></td>
                                <td align="center"><?php echo ($v["code"]); ?></td>
                                <td align="center"></td>
            
                            </tr><?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="panelBar">
                <div class="pages">
                    <span>显示</span>
                    <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage:this.value}, 'jbsxBox')">
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
                <div class="pagination" rel="jbsxBox" totalCount="<?php echo ($totalCount); ?>" numPerPage="<?php echo ($numPerPage); ?>" pageNumShown="10"
                    currentPage="<?php echo ($page); ?>"></div>
            </div>
    </div>