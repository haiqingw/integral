// JavaScript Document
$.fn.checkinput = function () {
    //Judge.EmptyJson.Ver
    var Judge = {
        empty: {//验证非空
        	Ver: function (value,fun,puts) {
                return (value != ""&&value!=puts);
            }, message: "请{0}"
        },
        emptyo: {//验证值为0时（空）
            Ver: function (value,fun,puts) {
                return (value!="0");
            }, message: "请{0}"
        },
        tel: {//验证电话号码
            Ver: function (value) {
                var reg = /^((\+86-)|(86-))?(13[0-9]|15[0-9]|18[0-9]|14[0-9]|17[0-9])\d{8}$|^((\+86-)|(86-))?(\d{3,4}-)?\d{7,8}$/;
                return reg.test(value);
            }, message: "{0}格式不规范！"
        },
        email: {//验证电子邮箱
        	Ver: function (value) {
                var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                return reg.test(value);
            }, message: "{0}格式不规范！"
        },
        qq: {
            Ver: function (value) {
                var reg = /[1-9]\d{4,}$/;
                return reg.test(value);
            }, message: "{0}格式不规范！"
        },
        sfz: {//验证身份证号码
            Ver: function (value) {
                var reg = '支付方式';
				return reg.test(value);
            },
            message: '{0}没有选择！'
        },
		checkbox:{
			Ver:function(value,param,Input)
			{
			var $this =$("[name='"+Input.attr("name")+"']");
			var i =0;
			$this.each(function(index,element)
			{
					if ($(this).attr("checked")=="checked")
					 {
						i++;
					 }
			});
			 if (i>0) { return true;}
			 else{return false;}
			},
			message:"请{0}！"
			},
        zcpass: {//验证密码
            Ver: function (value) {
                return !(/^(([A-Z]*|[a-z]*|\d*|[-_\~!@#\$%\^&\*\.\(\)\[\]\{\}<>\?\\\/\'\"]*)|.{0,5})$|\s/.test(value));
            },
            message: '密码由字母和数字组成，至少6位'
        },
        compassw: {
        	Ver: function (value, param) {
                return value == $(param).val();
            },
            message: '两次输入不匹配！'
        }
    }

    var ret = true;
	var YanZhengMa = 0;
    var Input = undefined;
    $(this).each(function () {
		Input = $(this);

        var str = Input.attr("emp");
        var jsonstr = eval("(" + str + ")");

        var value = Input.val();
        for (var num in jsonstr) {
            if (!Judge[num].Ver(value,jsonstr[num],Input.attr("mess"))) {
                var con = Judge[num].message;
				mes = con.replace("{0}",Input.attr("mess"));

				//弹窗提示
                toastr.warning(mes,"通知");
				//弹窗提示
                Input.focus();
                ret = false;
				return false;
            }

        }
    });
return ret;
}