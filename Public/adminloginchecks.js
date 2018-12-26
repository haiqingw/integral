/**
 * Created by 宁 on 2015/12/2.
 * 登录控制器验证js
 */
$(function () {
        document.onkeydown = function (e) {
            var ev = document.all ? window.event : e;
            if (ev.keyCode == 13) {
                $(".bn").trigger("click");
            }
        }

    $(".bn").click(function () {
        //验证用户账号
        if ($("#ur").val() == "" || $("#ur").val() == null) {
            layer.open({
                title: '消息',
                content: '请输入用户账号',
                skin: 'layui-layer-molv',
                icon: 2,
                shift: 4,
                shadeClose:true
            });
            $("#ur").focus();
            return false;
        }
        //验证用户密码
        if ($("#pw").val() == "" || $("#pw").val() == null) {
            layer.open({
                title: '消息',
                content: '请输入用户密码',
                skin: 'layui-layer-molv',
                icon: 2,
                shift: 4,
                shadeClose:true
            });
            $("#pw").focus();
            return false;
        }
        $.ajax({
            type: 'POST',
            url: $url,
            async: false,
            data: {lrname: $("#ur").val(), lrpwd: $("#pw").val()},
            dataType: 'JSON',
            beforeSend: function () {
                var index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
            },
            success: function (data) {
                if (data.anStatus == 2000) {
                    layer.msg(data.anMessage, {icon: 1, time: 2000,shadeClose:true},
                        function (){
                        window.location.href = $urls;
                    });
                } else {
                    layer.open({
                        title: '消息',
                        content: data.anMessage,
                        skin: 'layui-layer-molv',
                        icon: 2,
                        shift: 4,
                        shadeClose:true
                    });
                    return false;
                }
            },
            error: function () {
                layer.open({
                    title: '消息',
                    content: '网络错误,请稍后再试',
                    skin: 'layui-layer-molv',
                    icon: 2,
                    shift: 4,
                    shadeClose:true
                });
            },
            timeout: 10000,
            complete: function () {
                layer.closeAll("loading");
            }
        });
    });
});
