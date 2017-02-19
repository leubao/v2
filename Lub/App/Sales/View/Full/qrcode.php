<?php if (!defined('LUB_VERSION')) exit(); ?>
<div class="bjui-pageContent">
  <table class="table table-striped table-bordered table-hover">
  <tbody>
    <tr>
      <td width="100px">二维码</td>
      <td colspan="3"><img src="{$qr}"></td>
    </tr>
    <!--
    <tr>
      <td>下载</td>
      <td colspan="3"><a href="{:U('Wechat/Full/public_qrdown',array('id'=>$id));}">下载</a></td>
    </tr>-->
  </tbody>
</table>
</div>
<input name="id" value="{$data.id}" type="hidden">
<script type="text/javascript">
    function doc_filedownload1(a) {
        $.fileDownload($(a).attr('href'), {
            failCallback: function(responseHtml, url) {
                if (responseHtml.trim().startsWith('{')) responseHtml = responseHtml.toObj()
                $(a).bjuiajax('ajaxDone', responseHtml)
            }
        })
    }
</script>
<!-- url 直接指向文件地址，或返回正确的文件地址 
<a href="Book1.xlsx" onclick="doc_filedownload1(this); return false;">点我下载一个文件</a>
-->
<div class="bjui-pageFooter">
  <ul>
    <li>
      <button type="button" class="btn-close" data-icon="close">关闭</button>
    </li>
  </ul>
</div>