function NoClickDelay(el) {
    this.element = typeof el == 'object' ? el: document.getElementById(el);       
    if (window.Touch)  this.element.addEventListener('touchstart', this, false);
}
NoClickDelay.prototype = {
    handleEvent: function(e) {               
        switch (e.type) {                       
        case 'touchstart':
            this.onTouchStart(e);
            break;                       
        case 'touchmove':
            this.onTouchMove(e);
            break;                       
        case 'touchend':
            this.onTouchEnd(e);
            break;               
        }       
    },
    onTouchStart: function(e) {
        e.preventDefault(); this.moved = false;
        this.theTarget = document.elementFromPoint(e.targetTouches[0].clientX, e.targetTouches[0].clientY);               
        if (this.theTarget.nodeType == 3) this.theTarget = theTarget.parentNode;
        this.theTarget.className += ' pressed';
        this.element.addEventListener('touchmove', this, false);
        this.element.addEventListener('touchend', this, false);       
    },
    onTouchMove: function(e) {
        this.moved = true;
        this.theTarget.className = this.theTarget.className.replace(/ ?pressed/gi, '');       
    },
    onTouchEnd: function(e) {
        this.element.removeEventListener('touchmove', this, false);
        this.element.removeEventListener('touchend', this, false);                     
        if (!this.moved && this.theTarget) {
            this.theTarget.className = this.theTarget.className.replace(/ ?pressed/gi, '');                        
            var theEvent = document.createEvent('MouseEvents');
            theEvent.initEvent('click', true, true);
            this.theTarget.dispatchEvent(theEvent);                
        }
        this.theTarget = undefined;       
    }
};
//消除 iphone 中 onlick 延时 end
var flag=true;
(function($){
		
    $.numberInput=function(options){
        $(options.Params).bind({
            touchstart : function(){
               // if($(this).hasClass('no'))return false;
                var innerT = $(this).html().toString().replace(/\s+/g, "");
                var numberString = $(options.Display).text();
				
                if(innerT == options.Backspace){
					if(numberString!="0.00"){
                    	numberString = clearPoint(numberString);
					
					}
					if(numberString.length==0){
						numberString = "0.00";
						flag=true;
						}
					
                }else if(innerT == options.Confirmation){
                    hideNumberInput();
                }else if(innerT == options.Clear){
                    numberString = "0.00";
					flag=true;
                }else{
					//alert(numberString.substr(numberString.length-1,1));
					
					var r = /^[1-9]\d*$/;
					if(r.test(innerT) || r.test(numberString)){
					if(flag){
								numberString = innerT;
								flag=false;
							
						
						}else{
                            //if(limitPoint(numberString+innerT)){
							    //numberString += innerT;
                            //}
                            numberString = limitPoint(numberString,innerT);
							
							}
				
				}
                }
				//alert(numberString);
                changeDisplayStatus(numberString);
				
            }
        });
        $(options.HideKeyboard).bind({
            touchstart : function(){
                hideNumberInput();
            }
        });
        $(options.Display).bind({
            touchstart : function(){
                showNumberInput();
            }
        });
        function clearPoint(row){
            var ex = row.split(".");
            if(ex[1] != undefined){
                if(ex[1] != "0"){
                    return row.slice(0,-1) + "0";
                }
                if(ex[1] == "0"){
                    return row.slice(0,-2);
                }
            }
            return row.slice(0,-1);
        }
        function limitPoint(r1,r2){
            var row = r1 + r2;
            var ex = row.split(".");
            if(ex[1] != undefined && ex[1] != ""){
                if(ex[1].length > 1){
                    if(ex[1].slice(0,1) == "0"){
                        return ex[0]+"."+r2;
                    }
                    return r1;
                }
            }
            if(r2 == ".") return row+"0";
            return row;
        }
        function changeDisplayStatus(val){
			
            var ex = val.split(".");
            if(ex[0].length>5)return false;
            test(val);
            var $html=$(options.Display);
            $html.html(val);
            if($html.html().length>0){
                $html.addClass('active');
            }else{
                $html.removeClass('active');
            }
        }
        function showNumberInput() {
            /*$('<div id="numberInputBg"></div>').appendTo('body');
            $('#numberInputBg').css({
                position:'fixed',
                top:'0',
                left:'0',
                width:'100%',
                height:'100%',
                background:'rgba(0,0,0,0)',
                zIndex:'998'
            });*/
            $(options.Keyboard).css('zIndex','999').addClass('active');
            $(options.PayButton).hide();
        }
        function hideNumberInput() {
            //$('#numberInputBg').remove();
            $(options.Keyboard).removeClass('active');
            $(options.PayButton).show();
        }
        function test(val) {
            var reg = /^\d+(\.\d{1,2})?$/;
            if(reg.test(val) && val!=0 && val!=''){
                $(options.ErrorBorder).removeClass("error");
              //  $(options.PayButton).removeClass('no');
                $(options.PayButtonJJ).addClass('active');
            }else{
                $(options.ErrorBorder).addClass("error");
               // $(options.PayButton).addClass('no');
                $(options.PayButtonJJ).removeClass('active');
            }
        }
    }
})(jQuery);
