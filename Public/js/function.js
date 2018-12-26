var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
var phoneReg = /^((\+86-)|(86-))?(13[0-9]|15[0-9]|18[0-9]|14[0-9]|17[0-9])\d{8}$|^((\+86-)|(86-))?(\d{3,4}-)?\d{7,8}$/;
var passReg = /^(?![0-9]+$)(?![a-zA-Z]+$)[0-9A-Za-z]{6,21}$/;
function myAjax($url,$data,$back){
    var $index;
    $.ajax({
        url: $url, 
        type: "POST", 
        dataType: "JSON", 
        data:$data,
        beforeSend:function(){
            $index = layer.load(0, {shade:  [0.1,'#fff'] }); 
        },
        success: function (data){
            $back(data);
        },
        error:function(data){ },
        complete:function(){
            layer.close($index);
        }
    });
}
function mAjax($url,$data,$back){
    var $index;
    $.ajax({
        url: $url, 
        type: "POST", 
        dataType: "JSON", 
        data:$data,
        beforeSend:function(){
            $index = layer.open({type: 2,shadeClose: false}); 
        },
        success: function (data){
            $back(data);
        },
        error:function(data){ },
        complete:function(){
            layer.close($index);
        }
    });
}
function mAjaxNL($url,$data,$back){
    var $index;
    $.ajax({
        url: $url, 
        type: "POST", 
        dataType: "JSON", 
        data:$data,
        beforeSend:function(){
            $index = layer.open({type: 2,shadeClose: false,shade: 'background-color: rgba(0,0,0,0)' }); 
        },
        success: function (data){
            $back(data);
        },
        error:function(data){ },
        complete:function(){
            layer.close($index);
        }
    });
}
function layeralert($content,$back){
    layer.open({
        title: [
            '提示',
            'background-color:#0498a1; color:#fff;line-height:50px;height:50px;'
        ]
        ,anim: 'up'
        ,content: $content
        ,btn: ['确认', '取消']
        ,shadeClose: false,
        yes: function(){
            if($back == undefined){
                closelayer();
            }else{
                $back();
            }
        }
    });
}
function closelayer(){
	layer.closeAll();
}
function layermsg($content){
    layer.open({
	    content: $content
	    ,skin: 'msg'
		,time: 2 // 2秒后自动关闭
    });
}
/**
 * 判断是否是空
 * @param value
 */
function is_define(value) {
	if (value == null || value == "" || value == "undefined" || value == undefined || value == "null" || value == "(null)" || value == 'NULL' || typeof (value) == 'undefined') {
		return false;
	} else {
		value = value + "";
		value = value.replace(/\s/g, "");
		if (value == "") {
			return false;
		}
		return true;
	}
}
function useDot(tid,sid,obj){
    var evalText = doT.template($(tid).text());
    $(sid).html(evalText(obj));
}
function appDot(tid,sid,obj){
    var evalText = doT.template($(tid).text());
    $(sid).append(evalText(obj));
}

//获取天数差 开始日期 结束日期
function getDayCha(s1,s2){
    s1 = new Date(s1.replace(/-/g, "/"));
    s2 = new Date(s2.replace(/-/g, "/"));
    var days = s2.getTime() - s1.getTime();
    var time = parseInt(days / (1000 * 60 * 60 * 24));
    return time;
}
function json(ret){
    alert(JSON.stringify(ret));
}
