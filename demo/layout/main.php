<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $this->pageTitle;?></title>
<link type="text/css" rel="stylesheet" href="<?php echo $this->getStyle('frame/index.css');?>" />
<script type="text/javascript" src="<?php echo $this->getCommon('jquery.js');?>"></script>
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
</body>
</html>