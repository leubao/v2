<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageHeader">
    <div class="bjui-searchBar">
        <button type="button" class="btn-success" id="lookupback" data-toggle="lookupback" data-args="{id:'1', name:'自由职业'}" data-lookupid="collect_idcard" data-icon="reply-all" data-warn="未找到有效采集数据">完成采集</button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button type="button" class="btn-info" data-icon="bug" id="read_card">读卡</button>
    </div>
</div>
<div class="bjui-pageContent">
    <div class="m20">
        <textarea id="cardText" autofocus="autofocus" class="input-nm" style="height:200px;" cols="74" rows="10" disabled></textarea>
        
    </div>
</div>

<script>
$(document).ready(function(){
    var cardText = '';
    $('#cardText').val(cardText);

    //开启身份采集端口监听
    $('#lookupback').on('click', function() {
        var args = "{id:'cardid', name:'"+cardText+"'}";
        $('#lookupback').data('args',args);
        //socket.emit('stopRead');
    });
    var readUrl = 'http://127.0.0.1:8080/api/ReadMsg';
    $('#read_card').on('click', function() {
        $.ajax({
          url: readUrl,
          type: 'GET',
          dataType: 'JSON',
          success: function (res) {
            if(res.retcode == '0x90 0x1'){
              setCardText(res.cardno)
            } else {
              $(this).alertmsg('error',"error:"+res.retmsg+"~");
            }
          },
          error: function (e) {
            $(this).alertmsg('error',e.status+':'+e.statusText);
          }
        })
    });
    function setCardText(card) {
        cardText = $('#cardText').val();
        //判断是否重复
        if(cardText){
            console.log(cardText)
            var cardArr = cardText.split('|');
            // var len = cardArr.length;
            // for(var j = 0, len = len; j < len; j++){
            //     if(card == cardArr[j] && j < len){
            //         return false;
            //     }else{
            //         cardText = cardText +'|'+ card;
            //     }
            // }
            var isWrite = true;
            cardArr.forEach(function(item, index, arr) {
                console.log(item)
                if(card == item){
                    isWrite = false;
                    return false;
                }
            });
            if(isWrite){
                cardText = cardText +'|'+ card;
            }else{
                return false;
            }
            
        } else {
            cardText = card;
        }
        $('#cardText').val(cardText);
    }
     
})
  function Base64() {  
   
        // private property  
        _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";  
   
            
        // public method for decoding  
        this.decode = function (input) {  
            var output = "";  
            var chr1, chr2, chr3;  
            var enc1, enc2, enc3, enc4;  
            var i = 0;  
            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");  
            while (i < input.length) {  
                enc1 = _keyStr.indexOf(input.charAt(i++));  
                enc2 = _keyStr.indexOf(input.charAt(i++));  
                enc3 = _keyStr.indexOf(input.charAt(i++));  
                enc4 = _keyStr.indexOf(input.charAt(i++));  
                chr1 = (enc1 << 2) | (enc2 >> 4);  
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);  
                chr3 = ((enc3 & 3) << 6) | enc4;  
                output = output + String.fromCharCode(chr1);  
                if (enc3 != 64) {  
                    output = output + String.fromCharCode(chr2);  
                }  
                if (enc4 != 64) {  
                    output = output + String.fromCharCode(chr3);  
                }  
            }  
            output = _utf8_decode(output);  
            return output;  
        }  
       
       
        // private method for UTF-8 decoding  
        _utf8_decode = function (utftext) {  
            var string = "";  
            var i = 0;  
            var c = c1 = c2 = 0;  
            while ( i < utftext.length ) {  
                c = utftext.charCodeAt(i);  
                if (c < 128) {  
                    string += String.fromCharCode(c);  
                    i++;  
                } else if((c > 191) && (c < 224)) {  
                    c2 = utftext.charCodeAt(i+1);  
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));  
                    i += 2;  
                } else {  
                    c2 = utftext.charCodeAt(i+1);  
                    c3 = utftext.charCodeAt(i+2);  
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));  
                    i += 3;  
                }  
            }  
            return string;  
        }  
    }   
</script>