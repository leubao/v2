<html xmlns="http://www.w3.org/1999/xhtml" class="hb-loaded">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title></title>
<link href="{$config_siteurl}statics/dwz/themes/default/style.css" rel="stylesheet" type="text/css">
<link href="{$config_siteurl}statics/dwz/themes/css/core.css" rel="stylesheet" type="text/css">
<!--[if IE]>
<link href="http://os.chengde360.com/statics/dwz/themes/css/ieHack.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if lte IE 9]>
<script src="http://os.chengde360.com/statics/dwz/js/speedup.js" type="text/javascript"></script>
<![endif]-->
<script src="{$config_siteurl}statics/dwz/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="{$config_siteurl}statics/dwz/js/jquery.cookie.js" type="text/javascript"></script>
<script src="{$config_siteurl}statics/dwz/js/jquery.validate.js" type="text/javascript"></script>
<script src="{$config_siteurl}statics/dwz/js/jquery.bgiframe.js" type="text/javascript"></script>
<script src="{$config_siteurl}statics/xheditor/xheditor-1.2.1.min.js" type="text/javascript"></script>
<script src="{$config_siteurl}statics/xheditor/xheditor_lang/zh-cn.js" type="text/javascript"></script>
<script src="{$config_siteurl}statics/dwz/js/dwz.min.js" type="text/javascript"></script>
<script src="{$config_siteurl}statics/dwz/js/dwz.regional.zh.js" type="text/javascript"></script>

<link href="{$config_siteurl}statics/item/css/seat.css?=702" type="text/css" rel="stylesheet">
<body>
  <h2 class="contentTitle">录入指纹</h2>
  <div class="pageContent" layoutH="42">
    <OBJECT classid="clsid:933DB2AB-51BF-4204-9E30-C907FE352A5E" width="0" height="0" id="dtm" codebase="{$config_siteurl}lub/App/Crm/asset/ocx/libFPDev_WL.ocx"> </OBJECT>
    <div style="float:left; display:block; overflow:hidden; width:400px; padding:0 10px; line-height:21px;">
      <a class="button" href="javascript:void(0)" title="第一次录入指纹" onclick="fingerprint(this.id);" id="finger_1"><span>第一次录入指纹</span></a><input type="hidden" value="" id="finger1" /><span id="text1" style="color:green;line-height:25px"></span><br /><br />
      <a class="button" href="javascript:void(0)" title="第二次录入指纹" onclick="fingerprint(this.id);" id="finger_2"><span>第二次录入指纹</span></a><input type="hidden" value="" id="finger2" /><span id="text2" style="color:green;line-height:25px"></span><br /><br />
      <input name="id" value="{$id}" type="hidden" id="userid"/>
      <a class="button" id="print_btn"><span>提交</span></a>
    </div>
  </div>  
</body>  
</html>
<script type="text/javascript">
$(function(){
  $("#print_btn").bind("click",function(){
    var flag = 1;
    var finger1 = $("#finger1").val();
    var finger2 = $("#finger2").val();

    if(finger1 == ""){
      alert("第一次录入指纹 不能为空！");
      var flag = 0;
      return false;
    }
    if(finger2 == ""){
      alert("第二次录入指纹 不能为空！");
      var flag = 0;
      return false;
    }
    if(flag == 1){
      $.get("index.php?g=Crm&m=Index&a=fingerprint&finger1="+finger1+"&finger2="+finger2+"&id="+$("#userid").val(), function(data){
        alert("录入成功！");
      });
    }  
  });
});






var iRet;
var strImage1, strImage2, strImage3;
var strTZ, strMB;
var DevType;
//--------------------------------------------------------------//
//录入指纹
//--------------------------------------------------------------//
function fingerprint(id){
  if(DevDetect_OnClick()){      //检测设备
    GetTemplate_OnClick(id);    //录入指纹
  }

}

//--------------------------------------------------------------//
//自动检测设备
//--------------------------------------------------------------//
function DevDetect_OnClick()
{
  iRet = dtm.FPIDevDetect();  
  if(iRet == 0)
  {
    //alert("USB设备，iRet="+iRet);
    DevType=iRet;
    return true;   
  }
  else if(iRet == -1)
  {
    alert("没有找到设备，iRet="+iRet);
    DevType=0; 
    return false;   
  } 
  else
  {
    //alert("串口设备，串口号：COM"+iRet);
    DevType=iRet;
    return true;      
  } 
  //dtm.bShowImg = false; 
}

//--------------------------------------------------------------//
// 获取指纹模板
//--------------------------------------------------------------//
function GetTemplate_OnClick(id)
{
  ids = id.split("_");

  strMB = "";
    iRet = dtm.FPIGetTemplate(DevType, 15000);
    if(iRet == 0)
    {
      strMB = dtm.FPIGetFingerInfo();
      strImage1 = dtm.FPIGetImageData(1);
      strImage2 = dtm.FPIGetImageData(2);
      strImage3 = dtm.FPIGetImageData(3);
      document.getElementById('finger'+ids[1]).value = strMB;
      document.getElementById('text'+ids[1]).innerHTML = "已录入";
    }
    else
    {
      alert("采集指纹模板失败!");
    }
}


</script>