<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent">
    <form method="post" id="myform" action="<?php echo U('Class/addFunction');?>" class="pageForm required-validate" onsubmit="return iframeCallback(this);"
        target="callbackframe" enctype="multipart/form-data">
        <div class="pageFormContent" layoutH="56">
            <div class="unit">
                <fieldset>
                    <legend>商品二级分类添加</legend>
                    <dl class="nowrap">
                        <dt>所属父级</dt>
                        <dd>
                            <select name="pId" class="combox">
                                <option value="0" selected>顶级</option>
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
                        <dt>分类名称</dt>
                        <dd>
                            <input type="text" name="name" class="required" value="" placeholder="填写分类名称" />
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>分类图片</dt>
                        <dd>
                            <input name="photo" type="file" class="required" />
                            <p style="clear: both; width: 100%">
                                <font style="margin-left: 130px; color: #FF0000;">提示:图片尺寸为237*70</font>
                            </p>
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>分类排序</dt>
                        <dd>
                            <input type="text" name="sort" value="" placeholder="填写排序" />
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