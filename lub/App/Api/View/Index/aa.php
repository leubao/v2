<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SUI 模板</title>
    <link rel="stylesheet" href="//g.alicdn.com/sui/sui3/0.0.18/css/sui.min.css">
  </head>
  <body>
    <h1>Hello, world!</h1>
    <p>这里换成你自己的内容</p>
    <script>
      setInterval(function(){
        $.get("<?php echo U('Cron/index/index');?>",function(data,status){});
      }, 10000);
    </script>
    <script type="text/javascript" src="//g.alicdn.com/sj/lib/jquery/dist/jquery.min.js"></script>
    <script type="text/javascript" src="//g.alicdn.com/sui/sui3/0.0.18/js/sui.min.js"></script>
  </body>
</html>