<?php if (!defined('LUB_VERSION')) exit(); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>监票</title>
</head>

<body>
<!--<form action="{:U('Api/Detect/mobile_check_in');}" method="post">-->
<form action="{:U('Api/Detect/prison_ticket');}" method="post">
<label>二维码信息</label>
<input name="sn" type="text">
<input type="text" name="code" value="" placeholder="">
<input name="提交" type="submit">
</form>
<table border="1px">
	
	<thead>
		<tr>
			<th>header</th>
		</tr>
	</thead>
	<tbody>
<<?php foreach ($data as $key => $value): ?>
	<tr>
			<td>{$value.sn}</td>
		</tr>

<?php endforeach ?>
	 
	</tbody>
</table>
</body>
</html>