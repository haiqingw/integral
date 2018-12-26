(function ($) {
    $.dynSelect = function (arg) {
        var flag=0; var _data; var id="#"+arg.inputID; var _dom;
        $(id).click(function(){
            _dom="#"+$(this).attr('id');
            var _top=$(this).offset().top+$(this).height()+5;
            var _left=$(this).offset().left;
            var _width=$(this).width()+4;
            if (!flag){ 
                $("<div class='dynSelect'></div>").appendTo('body');
                $('.dynSelect').css({width:_width+'px',top:_top+'px',left:_left+'px'});
                $.ajax({
                  type: "post",
                  url: arg.dataUrl,
                  dataType: "html",
                  success:function(data){
                    $('.dynSelect').html(data);
                    _data=data;
                  }
                }); 
            } flag++;
        }).keyup(function(){
            var newData=_data.split('</p>');
            var _newData="";
            for(var i=0;i<newData.length-1;i++){
                if(newData[i].indexOf($(this).val())>0){
                    _newData+=newData[i]+"</p>";
                }
            }
            $('.dynSelect').html(_newData);
        }).blur(function(){
            if($('.dynSelect').html()==""){
                $('.dynSelect').remove();
                $(this).val("");
                flag=0;
            }
        });
        $(document).on('click','.dynSelect p',function(){
            $(_dom).val($(this).html());
            _dom=null;
            $('.dynSelect').remove();
            flag=0;
        });
        $("body").bind('dblclick',function(){
            $('.dynSelect').remove();
            flag=0;
        });
        $('.btnLook').click(function(){
            $(id).val("");
            $('.dynSelect').remove();
            flag=0;
        });
    };
})(jQuery);
