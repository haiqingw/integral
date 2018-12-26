<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
    <form method="post" id="myform" action="<?php echo U('Goods/addFunction');?>" class="pageForm required-validate" onsubmit="return iframeCallback(this);"
        target="callbackframe" enctype="multipart/form-data">
        <div class="pageFormContent" layoutH="56">
            <div class="unit">
                <fieldset>
                    <legend>商品信息编辑</legend>
                    <div class="tabs" currentIndex="0" eventType="click">
                        <div class="tabsHeader">
                            <div class="tabsHeaderContent">
                                <ul>
                                    <li><a href="JavaScript:;"><span>商品基础信息</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <dl class="nowrap">
                        <dt>选择分类</dt>
                        <dd>
                            <select name="classId" class="combox">
                                <option value>请选择分类</option>
                                <?php if(is_array($classes)): $i = 0; $__LIST__ = $classes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option>
                                    <?php if(is_array($vo['child'])): foreach($vo['child'] as $key=>$f): ?><option value="<?php echo ($f["id"]); ?>">
                                            <?php switch($f["floorId"]): case "2": ?>&nbsp;&nbsp;&nbsp;&nbsp;--<?php break;?>
                                                <?php case "3": ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--<?php break;?>
                                                <?php case "4": ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--<?php break;?>
                                                <?php case "5": ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;;&nbsp;&nbsp;;&nbsp;--<?php break;?>
                                                <?php default: endswitch;?>
                                            <?php echo ($f["name"]); ?>
                                        </option><?php endforeach; endif; endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </dd>
                    </dl>
                    
                    <dl class="nowrap">
                        <dt>商品名称</dt>
                        <dd>
                            <input type="text" name="name" class="required" value="" placeholder="请填写商品名称" />
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>封面图片</dt>
                        <dd>
                            <input name="facePhoto" type="file" class="required" />
                            <p style="clear: both; width: 100%">
                                <font style="margin-left: 130px; color: #FF0000;">提示:图片尺寸为237*70</font>
                            </p>
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>上架状态</dt>
                        <dd>
                            <select name="state" class="combox">
                                <option value="on">上架</option>
                                <option value="off">下架</option>
                            </select>
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>市场价格</dt>
                        <dd>
                            <input type="text" name="buyPrice" value="" placeholder="请商品市场价格" />
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>现售价格（积分加金额）</dt>
                        <dd>
                            <input type="text" name="jifen" value="" placeholder="所需积分" />
                            <input type="text" name="nowPrice" value="" placeholder="所需金额" />
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>商品详情</dt>
                        <dd>
                            <script id="editor" type="text/plain" name="detail" style="width:800px;height:500px;"></script>
                        </dd>
                    </dl>
                </fieldset>
            </div>
        </div>
        <div class="formBar">
            <ul style="float:left">
                <li style="margin-left: 125px; margin-right: 35px">
                    <div class="buttonActive">
                        <div class="buttonContent">
                            <button type="submit">保存</button>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="button">
                        <div class="buttonContent">
                            <button type="button" class="close">关闭</button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </form>
</div>
<script type="text/javascript" src="/Public/ueditor/ueditor.config.js"></script>
<script type="text/javascript" src="/Public/ueditor/ueditor.all.js"></script>
<script type="text/javascript">
    var editor = new baidu.editor.ui.Editor();
    //var ue = UE.getEditor('editor');
    editor.render("editor");

    function changeGrade(obj) {
        $.ajax({
            url: '<?php echo U("Goods/gradechange");?>',
            type: 'post',
            dataType: 'json',
            data: { gradeId: obj.value },
            success: function (data) {
                if (data != null) {
                    var sclass = data.search;
                    var options = '';
                    $('#w_combox_class').html('');
                    for (var s = 0; s < sclass.length; s++) {
                        options += '<option value="' + sclass[s].id + '">' + sclass[s].name + '</option>';
                    }
                    $('#w_combox_class').append(options);
                }
            }
        });
    }
</script>