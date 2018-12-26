<?php if (!defined('THINK_PATH')) exit();?><form id="pagerForm" method="post" action="<?php echo U('Goods/index');?>">
    <input type="hidden" name="keywords" value="<?php echo ($keywords); ?>" />
    <input type="hidden" name="model" value="<?php echo ($model); ?>" />
    <input type="hidden" name="pageNum" value="<?php echo ($page); ?>" />
    <input type="hidden" name="numPerPage" value="<?php echo ($numPerPage); ?>" />
</form>
<div class="pageHeader">
    <form onsubmit="return navTabSearch(this);" action="<?php echo U('Goods/index');?>" method="post" id="GoodsIndex">
        <div class="searchBar">
            <table class="searchContent">
                <tr>
                    <td align="left">
                    </td>
                    <td align="right">
                        关键词：<input type="text" value="<?php echo ($keywords); ?>" id="keywords" name="keywords" autocomplete="off"
                            placeholder="请输入商品名称" style="width: 160px;" value="<?php echo ($keywords); ?>" />
                    </td>


                    <td align="left">
                        <select onchange="changeFloor(this,1)" id="myclass1" name="class1">
                            <option value>请选择分类</option>
                            <?php if(is_array($classFloor1)): $i = 0; $__LIST__ = $classFloor1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($class1["id"] == $vo['id']): ?>selected<?php endif; ?>><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </td>
                    <td align="left">
                        <select onchange="changeFloor(this,2)" id="myclass2" name="class2">
                            <?php if(!empty($class2)): ?><option value="<?php echo ($class2["id"]); ?>" selected><?php echo ($class2["name"]); ?></option><?php endif; ?>
                        </select>
                    </td>
                    <td align="left">
                        <select name="classId" id="myclass3">
                            <?php if(!empty($class3)): ?><option value="<?php echo ($class3["id"]); ?>" selected><?php echo ($class3["name"]); ?></option><?php endif; ?>
                        </select>
                    </td>
                    <td align="left">
                        <select class="combox" name="state" onchange="searchByClass()">
                            <option value>全部</option>
                            <option value="on" <?php if($state == on): ?>selected<?php endif; ?>>上架</option>
                            <option value="off" <?php if($state == off): ?>selected<?php endif; ?>>下架</option>
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
            <li><a class="add" href="<?php echo U('Goods/add');?>" target="navTab"><span>添加</span></a></li>
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('Goods/update');?>?id={sid_user}" target="navTab"><span>修改</span></a></li>
            <li class="line">line</li>
            <li><a title="确定要删除这些记录吗?" target="selectedTodo" rel="decldel" postType="string" href="<?php echo U('Goods/del');?>"
                    class="delete"><span>删除</span></a></li>
            <li class="line">line</li>
            <li><a class="edit" href="<?php echo U('Goods/addgoods');?>?id={sid_user}" target="dialog"><span>商品入库</span></a></li>
        </ul>
    </div>
    <table class="table" width="80%" layoutH="180">
        <thead>
            <tr>
                <th width="2%"><input type="checkbox" group="decldel" class="checkboxCtrl"></th>
                <th width="50" align="center">ID</th>
                <th width="150" align="left">商品名称</th>
                <th width="80" align="left">所属分类</th>
                <th width="80" align="center">封面图片</th>
                <th width="80" align="center">库存数量</th>
                <th width="80" align="center">总销售量</th>
                <th width="80" align="center">上架状态</th>
                <th width="150" align="center">现售价格</th>
                <th width="80" align="center">市场价格</th>
                <th width="150" align="center">创建时间</th>
            </tr>
        </thead>
        <tbody>
            <?php if(is_array($goods)): foreach($goods as $key=>$v): ?><tr target="sid_user" rel="<?php echo ($v["id"]); ?>">
                    <td><input name="decldel" value="<?php echo ($v["id"]); ?>" type="checkbox"></td>
                    <td align="center"><?php echo ($v["id"]); ?></td>
                    <td align="center"><?php echo ($v['name']); ?></td>
                    <td align="center"><?php echo ($v['classname']); ?></td>
                    <td align="center"><img style="height:100%;" src="<?php echo ($v["facePhoto"]); ?>" /></td>
                    <td align="center"><?php echo ($v["hasNum"]); ?></td>
                    <td align="center"><?php echo ($v["buyNum"]); ?></td>
                    <td align="center"><select name="goodsid<?php echo ($v["id"]); ?>" onChange="changestate(this,'<?php echo ($v["id"]); ?>');">
                            <option value="on">上架</option>
                            <option <?php if($v["state"] == off): ?>selected<?php endif; ?> value="off">下架</option>
                        </select></td>
                    <td align="center"><?php echo ($v["jifen"]); ?>金币+￥<?php echo ($v["nowPrice"]); ?></td>
                    <td align="center">￥<?php echo ($v["buyPrice"]); ?></td>
                    <td align="center"><?php echo (date("Y-m-d H:i",$v["createTime"])); ?></td>

                </tr><?php endforeach; endif; ?>
        </tbody>
    </table>
    <div class="panelBar">
        <div class="pages">
            <span>显示</span>
            <select class="combox" name="numPerPage" onchange="navTabPageBreak({numPerPage: this.value})">
                <option value="20" <?php if($numPerPage == 1): ?>selected="selected"<?php endif; ?>>20
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
    function changestate(obj, id) {
        $.ajax({
            url: '<?php echo U("Goods/changestate");?>',
            type: 'post',
            dataType: 'json',
            data: { id: id, val: obj.value },
            success: function (data) {
                if (data != null) {
                    $msg = obj.value == 'on' ? '上架' : '下架';
                    alert('商品' + $msg + '成功');
                }
            }
        });
    }

    function searchByClass() {
        $('#GoodsIndex').submit();
    }

    function changeFloor(obj, floorNum) {
        $.ajax({
            url: '<?php echo U("Goods/changeFloor");?>',
            type: 'post',
            dataType: 'json',
            data: { classId: obj.value },
            success: function (data) {
                if (data != null) {
                    var op = data.options;
                    floorNum = floorNum + 1;
                    $('#myclass' + floorNum + '').html('<option value >请选择</option>');
                    var options = '';
                    for (var i = 0; i < op.length; i++) {
                        options += '<option value="' + op[i].id + '">' + op[i].name + '</option>';
                    }

                    $('#myclass' + floorNum).append(options);
                }
            }
        })
    }
</script>