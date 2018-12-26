<?php if (!defined('THINK_PATH')) exit();?><div class="pageContent pageFormContent">
    <form method="post" action="<?php echo U('areaaddfunction');?>" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
        <div class="pageFormContent" layoutH="600">
            <dl class="nowrap">
                <dt>模块名称：</dt>
                <dd>
                    <input name="areaName" class="required" type="text" size="30" value=""/>
                </dd>
            </dl>
            <dl class="nowrap">
                <dt>　</dt>
                <dd>
                    <font style="color: #FF0000;">提示:支持批量添加。中间使用英文状态下的逗号","隔开</font>
                </dd>
            </dl>
            <dl class="nowrap">
                <dt>模块备注：</dt>
                <dd>
                    <textarea name="textarea1" cols="50" rows="2"></textarea>
                </dd>
            </dl>
			<dl class="nowrap">
                <dt>系统模块名称：</dt>
                <dd>
                    <select name="moduleName" class="combox">
                        <option value="">请选择</option>
                        <?php if(is_array($moduleInfo)): foreach($moduleInfo as $key=>$vv): ?><option value="<?php echo ($vv["smID"]); ?>"><?php echo ($vv["smName"]); ?></option><?php endforeach; endif; ?>
                    </select>
                </dd>
            </dl>
            <dl class="nowrap">
                <dt></dt>
                <dd>
                    <font style="color: #FF0000;">提示:如果所属父级选择系统模块名称后则不用重复选择都可以被系统识别</font>
                </dd>
            </dl>
        </div>
        <div class="formBar">
            <ul style="float: left">
                <li style="margin-left: 100px;">
                    <div class="buttonActive">
                        <div class="buttonContent">
                            <button type="submit">保存</button>
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