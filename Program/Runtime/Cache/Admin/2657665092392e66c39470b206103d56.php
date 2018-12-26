<?php if (!defined('THINK_PATH')) exit();?><div class="pageHeader" style="border:1px #B8D0D6 solid">
    <form id="pagerForm" levelfrom onsubmit="return divSearch(this, 'jbsxBoxSETBUSLEVELSC');" action="<?php echo U('BusinessPlatform/levellist');?>"
        method="post">
        <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
        <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
        <input type="hidden" name="plat" value="<?php echo ($plat); ?>" />
        <input type="hidden" name="keywords" value="<?php echo ($params['keywords']); ?>" />
        <input type="hidden" name="startDate" value="<?php echo ($startDate); ?>" />
        <input type="hidden" name="endDate" value="<?php echo ($endDate); ?>" />
        <input type="hidden" name="status" value="<?php echo ($params['status']); ?>">
        <input type="hidden" name="level" value="<?php echo ($level); ?>">
        <input type="hidden" name="al" value="<?php echo ($al); ?>">
        <input type="hidden" name="parent" value="<?php echo ($params['parent']); ?>">
        <div class="searchBar">
        </div>
    </form>
</div>
<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid;width:100%;">
    <div class="panelBar">
        <ul class="toolBar">
            <li class="line">line</li>
            <li><a class="add" href="<?php echo U('BusinessPlatform/leveladd');?>?plat=<?php echo ($plat); ?>&addTypes=level" target="dialog"
                    width="600" height="350" mask="true" rel="add" title="添加"><span>添加级别</span></a></li>
            <li class="line">line</li>
            <li>
                <a class="edit" onclick="levelTypeDisplay()" title="修改显示状态"><span>使用状态变更</span></a>
            </li>
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('ModuleManage/exceladd');?>?plat=<?php echo ($plat); ?>&id={id}" target="dialog" width="600"
                    height="500" mask="true" rel="add" title="添加"><span>APP专享模块显示</span></a></li>
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('ModuleManage/addincome');?>?plat=<?php echo ($plat); ?>&id={id}" target="dialog" width="600"
                    height="500" mask="true" rel="edit" title="添加"><span>APP我的收益显示</span></a></li>
            <li class="line">line</li>
        </ul>
    </div>
    <table class="table" width="100%" layoutH="310" rel="jbsxBoxSETBUSLEVELSC">
        <thead>
            <tr>
                <th width="4%" align="center">类型ID</th>
                <th width="5%" align="center">级别</th>
                <th width="5%" align="center">简拼</th>
                <th width="5%" align="center">区分编号</th>
                <th width="5%" align="center">状态</th>
                <th width="8%" align="center">升级介绍</th>
                <th width="8%" align="center">专享模块显示</th>
                <th width="8%" align="center">我的收益显示</th>
            </tr>
        </thead>
        <?php if($totalCount > 0): ?><tbody>
                <?php if(is_array($resArray)): $i = 0; $__LIST__ = $resArray;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr target="id" rel="<?php echo ($v["id"]); ?>" onclick="levelID('<?php echo ($v["id"]); ?>','<?php echo ($v["plat"]); ?>','level')">
                        <td align="center" style="font-weight: bold;color:rgb(7, 6, 6);">
                            <span style="color:rgb(196, 21, 50);"><?php echo ($v["id"]); ?></span>
                        </td>
                        <td align="center"><?php echo ($v["classname"]); ?></td>
                        <td align="center"><?php echo ($v["englishname"]); ?></td>
                        <td align="center" style="font-weight: bold;color:#000000;">编号：<?php echo ($v["nums"]); ?></td>
                        <td align="center" style="font-weight: bold;">
                            <?php if($v['status'] == 1): ?><span style="color:rgb(28, 71, 37);"><?php echo ($v["stat"]); ?></span><?php endif; ?>
                            <?php if($v['status'] == 2): ?><span style="color:rgb(214, 184, 188);"><?php echo ($v["stat"]); ?></span><?php endif; ?>
                        </td>
                        <td align="center">
                            <?php if(empty($v['intro'])): ?><span style="font-weight: bold;color:rgb(196, 21, 50);">未设置</span>
                                <?php else: ?>
                                <a style="line-height: 25px;font-weight: bold;color:rgb(53, 21, 196);" href="<?php echo U('BusinessPlatform/toview');?>?id=<?php echo ($v["id"]); ?>&types=sys"
                                    target="dialog" mask="true" height="611" width="500" title="">详情</a><?php endif; ?>
                        </td>
                        <td align="center" style="font-weight: bold;color:#115841;">
                            <?php if(empty($v['exclv'])): ?><span style="font-weight: bold;color:rgb(196, 21, 50);">不显示</span>
                                <?php else: ?>
                                <span class="">显示</span>
                                <a style="line-height: 25px;font-weight: bold;color:rgb(53, 21, 196);" href="<?php echo U('ModuleManage/toview');?>?id=<?php echo ($v["id"]); ?>"
                                    target="dialog" mask="true" height="300" width="500" title=""> <span style="color: #800000; font-weight: bold; border-left:1px solid #ccc; padding-left:15px; margin-left:10px;">详情</span></a><?php endif; ?>
                        </td>
                        <td align="center" style="font-weight: bold;color:#115841;">
                            <?php if(empty($v['cashID'])): ?><span style="font-weight: bold;color:rgb(196, 21, 50);">不显示</span>
                                <?php else: ?>
                                <span class="">显示</span>
                                <a style="line-height: 25px;font-weight: bold;color:rgb(53, 21, 196);" href="<?php echo U('ModuleManage/toviews');?>?id=<?php echo ($v["id"]); ?>"
                                    target="dialog" mask="true" height="300" width="500" title=""> <span style="color: #800000; font-weight: bold; border-left:1px solid #ccc; padding-left:15px; margin-left:10px;">详情</span></a><?php endif; ?>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
            <?php else: ?>
            <tbody>
                <tr>
                    <td align="center" colspan="13" style="color: red;">抱歉，没有找到符合的记录！</td>
                </tr>
            </tbody><?php endif; ?>
    </table>
</div>
<script type="text/javascript">
    var obj = {};
    var msg;
    //修改显示状态
    function levelTypeDisplay() {
        // alert(JSON.stringify(obj));
        // return false;
        if (obj == '') {
            alertMsg.error("请选中修改的信息");
            return false;
        } else {
            getStatus();
            var $layer = layer.confirm('当前：（' + msg + '）状态,确认要修改么？', {
                btn: ['确定', '取消'], //按钮
                icon: 3
            }, function () {
                $.ajax({
                    url: "<?php echo U('BusinessPlatform/updateStatus');?>",
                    type: "post",
                    data: obj,
                    dataType: "json",
                    async: false,
                    success: function (ret) {
                        if (ret.status == 1) {
                            // navTab.reload(); //刷新当前页  */
                            $("#" + ret.rel).loadUrl(ret.url, $("[levelfrom]").serializeArray(), "");
                            alertMsg.correct(ret.msg);
                        } else {
                            alertMsg.correct(ret.msg);
                        }

                    }
                });
                // alert(dataId);
                layer.close($layer);
            }, function () {})
        }
    }

    function levelID(id, plat, types) {
        obj.id = id;
        obj.plat = plat;
        obj.types = types;
    }

    function getStatus() {
        $.ajax({
            url: "<?php echo U('BusinessPlatform/getStatus');?>",
            type: "post",
            data: obj,
            dataType: "json",
            async: false,
            success: function (ret) {
                msg = ret.msg;
            }
        });
    }
</script>