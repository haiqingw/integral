/**
 * Created by 宁 on 2015/8/25.
 * 登陆JS验证
 */

//记住用户名密码
function Saves() {
	var str_username = $("#j_username").val();
    var str_password = $("#j_password").val();
    if ($("#j_remember").is(":checked")) {
		$.cookie("rmbUserPwdAdmin", "true", { expires: 7 }); //存储一个带7天期限的cookie
        $.cookie("j_usernameAdmin", str_username, { expires: 7 });
        $.cookie("j_passwordAdmin", str_password, { expires: 7 });
    }else {
        $.cookie("rmbUserPwdAdmin", "false", { expire: -1 });
        $.cookie("j_usernameAdmin", "", { expires: -1 });
        $.cookie("j_passwordAdmin", "", { expires: -1 });
    }
}

$(function () {
	$(".submitcss").click(function () {
		//验证用户名称
		if ($("input[name='lrUserName']").val() == "" || $("input[name='lrUserName']").val() == null) {
			layer.open({
				title: '消息',
				content: '请输入用户名',
				skin: 'layui-layer-molv',
				icon: 2,
				shift: 4,
				shadeClose:true
			});
			$("input[name='lrUserName']").focus();
			return false;
		}
		//验证密码
		if ($("input[name='lrUserPwd']").val() == "" || $("input[name='lrUserPwd']").val() == null) {
			layer.open({
				title: '消息',
				content: '请输入密码',
				skin: 'layui-layer-molv',
				icon: 2,
				shift: 4,
				shadeClose:true
			});
			$("input[name='lrUserPwd']").focus();
			return false;
		}
		if ($("input[name='lrCode']").val() == "" || $("input[name='lrCode']").val() == null) {
			layer.open({
				title: '消息',
				content: '请输入验证码',
				skin: 'layui-layer-molv',
				icon: 2,
				shift: 4,
				shadeClose:true
			});
			$("input[name='lrCode']").focus();
			return false;
		}
		$.ajax({
			type: 'POST',
			url: logincheck,
			/*async: false,*/
			data: {
				lrUserName: $("input[name='lrUserName']").val(),
				lrUserPwd: $("input[name='lrUserPwd']").val(),
				lrCode: $("input[name='lrCode']").val(),
				isbc: $('#isbc').prop('checked')
			},
			dataType: 'json',
			beforeSend: function () {
			},
			success: function (data) {
				if(data.Code=="200"){
					Saves();
					layer.msg(data.Msg, {icon: 1, time: 2000,shadeClose:true}, function () {
						window.location.href = data.hrefUrl;
					});
				}

				if(data.Code=="300"){
					layer.open({
						title: '提示',
						content: data.Msg,
						skin: 'layui-layer-molv',
						icon: 2,
						shift: 4,
						shadeClose:true
					});
					$('#verify').trigger('click');
					return false;
				}
			},
			error: function () {

			},
			timeout: 7000,
			complete: function () {
			}
		});
	});

	//回车事件
	$("input[name='lrUserName'],input[name='lrUserPwd'],input[name='lrCode']").keydown(function (e) {
		//监听回车事件，并执行操作。
		if (e.keyCode==13) { $('#login_ok').trigger('click')};
	})

	//记住密码
	 $(document).ready(function () {
        if ($.cookie("rmbUserPwdAdmin") == "true") {
        $("#j_remember").attr("checked", true);
        $("#j_username").val($.cookie("j_usernameAdmin"));
        $("#j_password").val($.cookie("j_passwordAdmin"));
        }
    });

	 //重置
	 $('[reset]').click(function (argument) {
	 	$('[puts]').val('');
	 });
});

