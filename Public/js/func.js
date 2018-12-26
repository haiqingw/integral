$.fn.numeral = function () {
    $(this).css("ime-mode", "disabled");
    this.bind("keypress", function (e) {
        var code = (e.keyCode ? e.keyCode : e.which);  //兼容火狐 IE   
        if (!$.browser.msie && (e.keyCode == 0x8))  //火狐下 不能使用退格键  
        {
            return;
        }
        return code >= 48 && code <= 57 || code == 46;

    });
    this.bind("blur", function () {

        if (this.value.lastIndexOf(".") == (this.value.length - 1)) {
            this.value = this.value.substr(0, this.value.length - 1);
        } else if (isNaN(this.value)) {
            this.value = " ";
        } 
    });
    this.bind("paste", function () {
        var s = clipboardData.getData('text');
        if (!/\D/.test(s));
        value = s.replace(/^0*/, '');
        return false;
    });
    this.bind("dragenter", function () {
        return false;
    });
    this.bind("keyup", function () {
        this.value = this.value.replace(/[^\d.]/g, "");
        //必须保证第一个为数字而不是.
        this.value = this.value.replace(/^\./g, "");
        //保证只有出现一个.而没有多个.
        this.value = this.value.replace(/\.{2,}/g, ".");
        //保证.只出现一次，而不能出现两次以上
        this.value = this.value.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
    });
};

