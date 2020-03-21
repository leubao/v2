!function ($) {
	function is_card(str) {
        //是否是符合标准的身份证号码
        //是否已经注册
    }
    function is_mobile(str) {
        var re = /^1\d{10}$/;
        if (re.test(str)) {
            return true;
        } else {
            return false;
        }
    }
    //判断是否在微信中打开
    function is_weixn(){
        var ua = navigator.userAgent.toLowerCase();
        if(ua.match(/MicroMessenger/i)=="micromessenger") {
            return true;
        } else {
            return false;
        }
    }
}(jQuery);