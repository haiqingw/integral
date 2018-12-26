<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
    .dialogBackground {
        background-color: #000033;
    }
</style>
<div class="pageContent">
    <form method="post" action="<?php echo U('Integral/addPlatRun');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, dialogAjaxDone)">
        <div class="pageFormContent" layoutH="58">
            <div class="unit">
                <fieldset>
                    <legend>新增可兑换平台</legend>
                    <dl class="nowrap">
                        <dt>平台（系统用户）</dt>
                        <dd>
                            <input type="hidden" name="orgLookup.id" id="plat" value="${orgLookup.id}" />
                            <input type="text" class="required" id="username" name="orgLookup.orgName" value="" size="30"
                                suggestFields="orgNum,orgName" suggestUrl="" lookupGroup="orgLookup" readonly="readonly" />
                            <a class="btnLook" href="<?php echo U('Integral/lookup');?>" lookupGroup="orgLookup" rel="lookup" rel="lookup">查找带回</a>
                        </dd>
                    </dl>
                    <dl class="nowrap">
                        <dt>购买周期</dt>
                        <dd>
                            <select class="buslevel required" id="buslevel" name="buslevel">
                                <option value="">请选择周期</option>
                            </select>
                        </dd>

                    </dl>
                </fieldset>
            </div>
        </div>
        <div class="formBar">
            <ul style="float: left">
                <li style="margin-left: 20px;">
                    <div class="buttonActive">
                        <div class="buttonContent">
                            <button type="submit">提交</button>
                        </div>
                    </div>
                </li>
                <li style="margin-left: 20px;">
                    <div class="button">
                        <div class="buttonContent">
                            <button type="button" class="close">取消</button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </form>
</div>
<script>
    /**
     * 选择商户等级
     */
    function selectLevel(id) {
        if (id != "" || id != 'undefined') {
            $.ajax({
                url: "<?php echo U('Integral/get_bus_level');?>",
                data: {
                    id: id
                },
                type: "post",
                dataType: "json",
                success: function (ret) {
                    if (ret.status) {
                        var html = '<option value="">请选择周期</option>';
                        for (i in ret.data) {
                            html += '<option value="' + ret.data[i].englishname + '">' +
                                ret.data[i].classname + '</option>';
                        }
                        $('#buslevel').html(html);
                    } else {
                        alertMsg.error(ret.msg);
                    }
                }
            });
        }
    }
</script>