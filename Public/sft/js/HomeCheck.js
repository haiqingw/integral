// JavaScript Document
$.fn.checkinput = function () {
    //Judge.EmptyJson.Ver
    var Judge = {
        empty: {//验证非空
            Ver: function (value,fun,puts) {
                return (value != ""&&value!=puts);
            }, message: "{0}"
        },
        emptyo: {//验证值为0时（空）
            Ver: function (value,fun,puts) {
                return (value!="0");
            }, message: "{0}"
        },
        tel: {//验证电话号码
            Ver: function (value) {
                var reg = /^1[3|4|5|7|8][0-9]\d{8}$/;
                return reg.test(value);
            }, message: "{0}（格式不规范）！"
        },
        care:{
        	 Ver: function (value) {
                var reg = /^(\d{16}|\d{17}\d{18}|\d{19})$/;
                return reg.test(value);
            }, message: "{0}格式不规范！"
        },
        number:{
           Ver: function (value) {
				var reg = /^[0-9]+$/g;
                return reg.test(value);
            },
            message: '只允许输入数字！'	
        },
        email: {//验证电子邮箱
            Ver: function (value) {
                var reg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                return reg.test(value);
            }, message: "{0}格式不规范！"
        },
        relmon: {//验证金额
            Ver: function (value) {
                // var reg = /^\d+(\.\d+)?$/; 正浮点数
                // var regt = /^[0-9]*[1-9][0-9]*$/;正整数
                var reg = /^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/;
                return reg.test(value);
            }, message: "{0}不规范！"
        },
        qq: {
            Ver: function (value) {
                var reg = /[1-9]\d{4,}$/;
                return reg.test(value);
            }, message: "{0}格式不规范！"
        },
        sfz: {
            Ver: function (value) {
                var reg = /(^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$)|(^[1-9]\d{5}\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{2}$)/;
                return reg.test(value);
            }, message: "{0}（格式不规范）！"
        },
        zffs: {//验证身份证号码
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
            message:"{0}！"
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
                layer.open({
                    content: mes,
                    skin: 'msg',
                    time: 2 //2秒后自动关闭
                  });
                // toastr.warning(mes,"通知");
                //弹窗提示
                Input.focus();
                ret = false;
                return false;
            }

        }
    });
return ret;
}