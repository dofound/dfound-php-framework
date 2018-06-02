<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->pageTitle;?></title>
<script type="text/javascript" src="<?php echo $this->getCommon('jquery.js');?>"></script>
<link type="text/css" rel="stylesheet" href="<?php echo $this->getStyle('index.css');?>" />
</head>

<body>

<?php include_once $DfInclude;?>

<!-- footer -->
<div id="footer-wrapper">
	<div id="footer">
		<ul>
		<li><a rel="nofollow" href="http://www.dofound.net/help">Help</a></li>
		<li title="<?php echo $DfMemory;?>" >&copy; DoFound Labs Inc. <?php echo date('Y');?>&nbsp;&nbsp;&nbsp;</li>
		</ul>
	</div>
</div>
<div><!-- <script src="http://s14.cnzz.com/stat.php?id=5038983&web_id=5038983&show=pic" language="JavaScript"></script> --></div>
</body>
</html>