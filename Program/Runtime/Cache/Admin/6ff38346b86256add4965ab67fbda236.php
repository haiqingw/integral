<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('authlist');?>" onsubmit="return navTabSearch(this);">
    <input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" />
    <input type="hidden" name="model" value="<?php echo ($model); ?>" />
    <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
</form>
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php echo U('authlist');?>" method="post" id="BlacklistIndex">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td align="left">
                    </td>
                    <td align="right">
                        关键词：<input type="text" value="<?php echo ($keywords); ?>" id="keywords" name="keywords" autocomplete="off"
                            placeholder="权限名称,控制器名称,方法名称" style="width: 160px;" />
                    </td>
                    <td>
                        <select name="model" class="combox">
                            <option value="0" <?php if($model == 0): ?>selected<?php endif; ?>>全部区域</option>
                            <?php if(is_array($info)): foreach($info as $key=>$v): ?><option value="<?php echo ($v["model_ID"]); ?>" <?php if($model == $v['model_ID']): ?>selected<?php endif; ?>><?php echo ($v["model_Name"]); ?></option><?php endforeach; endif; ?>
                        </select>
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
            <li><a class="add" href="<?php echo U('authadd');?>" target="navTab"><span>添加</span></a></li>
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('authmodify');?>?authid={sid_user}" target="navTab"><span>修改</span></a></li>
            <li class="line">line</li>
            <li><a title="确定要删除这些记录吗?" target="selectedTodo" rel="decldel" postType="string" href="<?php echo U('authdel');?>"
                    class="delete"><span>删除</span></a></li>
        </ul>
    </div>
    <table class="table" width="80%" layoutH="160">
        <thead>
            <tr>
                <th width="2%"><input type="checkbox" group="decldel" class="checkboxCtrl"></th>
                <th width="30" align="center">权限ID</th>
                <th width="50" align="left">权限名称</th>
                <th width="50" align="center">权限控制器</th>
                <th width="50" align="center">权限操作方法</th>
                <th width="50" align="center">全路径</th>
                <th width="50" align="center">创建时间</th>
                <th width="50" align="center">创建IP</th>
                <th width="100" align="center">排序</th>
                <th width="100" align="center">显示区域</th>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($pAuthinfo)): foreach($pAuthinfo as $key=>$v): ?><tr target="sid_user" rel="<?php echo ($v["auth_id"]); ?>">
                    <td><input name="decldel" value="<?php echo ($v["auth_id"]); ?>" type="checkbox"></td>
                    <td align="center"><?php echo ($v["auth_id"]); ?></td>
                    <td width="100">
                        <?php switch($v["auth_level"]): case "1": ?>　　┗&nbsp;<?php break;?>
                            <?php case "2": ?>　　　　┗&nbsp;<?php break;?>
                            <?php default: endswitch;?>
                        <?php echo ($v["auth_name"]); ?>
                    </td>
                    <td align="center"><?php echo ($v["auth_c"]); ?></td>
                    <td align="center"><?php echo ($v["auth_a"]); ?></td>
                    <td align="center"><?php echo ($v["auth_path"]); ?></td>
                    <td align="center"><?php echo ($v["auth_createtime"]); ?></td>
                    <td align="center"><?php echo ($v["auth_createip"]); ?></td>
                    <td><input type="hidden" name="cid" value="<?php echo ($v["auth_id"]); ?>" /> <input type="text" name="sortno" value="<?php echo ($v["auth_sortno"]); ?>"
                            checkval="<?php echo ($v["auth_sortno"]); ?>" onkeyup="check_mod(event,this)" maxlength="5" style="border: none; width: 80px;text-align: center;" />　<a
                            style=" cursor: pointer; color:#247CE8; display:none; text-decoration: none;" onclick="saves()"
                            sav>保存</a></td>
                    <td align="center"><?php echo ($v["ShowAreaName"]); ?></td>
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
<script type="text/javascript">
    //检查
    function check_mod(e, o) {
        var $this = $(o);
        var v = o.value | 0;
        if (v <= 0) {
            o.value = o.value.replace(/\D/g, '');
            o.focus();
        }
        if ($this.val() == $this.attr('checkval')) {
            $this.next('a').hide();
        } else {
            $this.next('a').show();
        };
    }

    //保存
    function saves() {
        var $this = $('[sav]:visible');
        var cidarr = new Array(); //定义字符串数组
        var valarr = new Array(); //定义字符串数组
        $.each($this, function (index, val) {
            cidarr.push($(this).parents('td').find('input[name="cid"]').val()); //向数组最后插入记录
            valarr.push($(this).parents('td').find('input[name="sortno"]').val()); //向数组最后插入记录
        });

        //异步保存
        $.ajax({
            url: '<?php echo U("sortno");?>',
            type: 'POST',
            dataType: 'json',
            data: {
                cidarr: cidarr,
                valarr: valarr
            },
            success: function (data) {
                if (data.statusCode == 200) {
                    navTab.reload("", {
                        navTabId: ""
                    });
                    alertMsg.correct(data.message);
                } else {
                    alertMsg.error(data.message);
                };
            }
        });
    }
</script>