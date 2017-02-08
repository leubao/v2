<?php if (!defined('LUB_VERSION')) exit(); ?>
<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<Managetemplate file="Home/Public/cssjs"/>
<title>售票  - by LubTMP</title>
</head>

<body>
<object classid="clsid:933DB2AB-51BF-4204-9E30-C907FE352A5E" width="0" height="0" id="dtm" codebase="{$config_siteurl}lub/App/Home/Assets/ocx/libFPDev_WL.ocx"> </object>
<button onclick="GetFeatureB64_OnClick();">比对指纹</button>

</body>
</html>
<script type="text/javascript">

var iRet;
var strImage1, strImage2, strImage3;
var strTZ, strMB;
var DevType;
function VerifyB64_OnClick()
{
    strMB = "Z9j657dbdtGG6F0pN6qne+yEZTBwaA8SRYE0gpzDztBH7w6b2wNkOW7qX+n3qKU7rIRlcDBoD0JW2Dzhw83cHFzabCbC8IO01atCq0pvOoeGwHNgXQ04K7ocC/Or2wvO35eWyHYvEDze1bVsRz63c3g63IJNdYq1Roa2b05VHq6syykbPIru1z75CNaXUlKEbEe/+6VkBzx/dscXHgwE9YrvSxsH3Tx9aWIV+1UwPNw/2WwXO6XA8rqa6ic1LjbTI9jupLuEJwKjnW+hcmNrqFG829JFheWzZOBV4/2irzGmjm96OmIFWA+LPoiWycRazeUEEVEJbjNk4FXj/aKv/Q==";
    //strTZ = "Z9y36KxALY7ZrRhOUO/inAvDIve3L0jVgsZzBRuEiddAqEm+/kQjFEu1CLas8/5g998+K2szVB1DXinBIH805K0FaTK/5A8IMFelaalfHOycjDR7f0r39Lfz0CiZpz39V9Wz6fRN+c+m48IPDBQOz587h/1h2B8dVFCxHa7R/09cBH9JbVrlo/vUd+6nHEKQimleB0odJxe9t4AejzJ/00gqwPOUIQVrGJKlIEs9cOVSepuOzpbxrPt/ynxiPTCuORHw5aX9mseQFKEXCVZbxVJ6m47OlvGs+3/KfGI9MK45EfDlpf2ax5AUoRcJVlvFUnqbjs6W8az7f8p8Yj0wYg==";
  iRet = dtm.FPIFpMatchB64(strMB, strTZ, 3);
    if(iRet != 0)
    {
      alert("指纹比对失败，错误码="+iRet);
    }
    else
    {
      alert("指纹比对成功");
    }
}
//--------------------------------------------------------------//
// 获取指纹特征(BASE64格式)
//--------------------------------------------------------------//
function GetFeatureB64_OnClick()
{
  strTZ = "";
    iRet = dtm.FPIGetFeatureB64(DevType, 15000);
    if(iRet == 0)
    {
      strTZ = dtm.FPIGetFingerInfo();
      //document.getElementById('tz').value = strTZ;
      VerifyB64_OnClick();
    }
    else
    {
      alert("采集指纹特征失败!");
    }
}
</script>