<?php
use qpf\error\Error;
?>
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title><?php echo  QPF::lang('qpf/web', $name) .' | '. $code; ?></title>
<style type="text/css">
body{
	font-family: 'Verdana', sans-serif;
	background:#FAFAFA;
	width: 100%;
	overflow-x:hidden;
}
.wrap{
	margin:0 auto;
}
.logo h1{
	font-size:200px;
	color:#202020;
	text-align:center;
	margin-bottom:1px;
	text-shadow:4px 4px 1px #dadada;
}	
.logo p{
	color:#771e1e;
	font-size:20px;
	margin-top:1px;
	text-align:center;
}	
.logo p span{
	color:lightgreen;
}	
.sub a{
	color:#202020;
	text-decoration:none;
	padding:5px;
	font-size:13px;
	font-family: arial, serif;
	font-weight:bold;
}	
</style>
</head>


<body>
	<div class="wrap">
		<div class="logo">
			<h1><?php echo $code; ?></h1>
			<p><?php echo (Error::isDebug2() ? $message : QPF::lang('qpf/web', 'http_404')); ?></p>
			<div class="sub">
			   <p><a href="<?php echo $home; ?>"><?php echo QPF::lang('qpf/web', 'Back to home'); ?></a></p>
			</div>
		</div>
	</div>
</body>
</html>