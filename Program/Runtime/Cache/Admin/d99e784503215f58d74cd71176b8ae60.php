<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    .dialogBackground {
        background-color: #000033;
    }
</style>
<div class="pageContent">
    <form method="post" submitButton action="<?php echo U('Integral/doAddrules');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone);"
        enctype="multipart/form-data">
        <div class="pageFormContent" layoutH="56">
            <div class="unit">
                <input type="hidden" name="plat" id="plat" value="<?php echo ($plat); ?>" />
                <input type="hidden" name="level" id="level" value="<?php echo ($level); ?>" />
                <fieldset>
                    <legend>添加设置</legend>
                    <dl class="nowrap">
                        <dt>产品</dt>
                        <dd>
                            <select name="proid" class="combox">
                                <option value="">请选择</option>
                                <?php if(is_array($product)): $i = 0; $__LIST__ = $product;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["id"]); ?>"><?php echo ($val["commodityName"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>达标数量</dt>
                        <dd>
                            <input type="text" id="nums" name="nums" value="" class="required digits" />
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>返积分</dt>
                        <dd>
                            <input type="text" id="integral" name="integral" value="" class="required digits" />
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>类型</dt>
                        <dd>
                            <select name="types" class="combox">
                                <option value="">请选择</option>
                                <option value="1">激活</option>
                                <!-- <option value="2">交易</option> -->
                                <option value="3">笔数</option>
                                <option value="4">招商</option>
                            </select>
                        </dd>
                    </dl>
                </fieldset>
            </div>
        </div>
        <div class="formBar">
            <ul style="float: left">
                <li style="margin-left: 125px; margin-right: 35px">
                    <div class="buttonActive">
                        <div class="buttonContent">
                            <button type="button" id="submitButton">提交</button>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="button">
                        <div class="buttonContent">
                            <button type="button" class="close">返回列表</button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </form>
</div>
<script type="text/javascript">
    function isPhoneNo(phone) {
        var pattern = /^1[34578]\d{9}$/;
        return pattern.test(phone);
    }
    //提交表单
    $(function () {
        $('#submitButton').click(function () {
            var $layer = layer.confirm('确认要添加么？', {
                btn: ['确定', '取消'], //按钮
                icon: 3
            }, function () {
                layer.close($layer);
                $('[submitButton]').submit();
            }, function () { });
        });
    })
</script>