<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Cache-Control" content="no-cache" />
        <meta name="format-detection" content="telephone=no" />
        <meta name="keywords" content="手机新浪网,新浪首页,新闻资讯,新浪新闻，新浪无线" />
        <meta name="description" content="手机新浪网是新浪网的手机门户网站，为亿万用户打造一个手机联通世界的超级平台，提供24小时全面及时的中文资讯，内容覆盖国内外突发新闻事件、体坛赛事、娱乐时尚、产业资讯、实用信息等。手机新浪网iphone版 - sina.cn" />
        <meta name="baidu-tc-cerfication" content="8e29905d75883a6a9826b14780445719" />
        <meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no" name="viewport" id="viewport" />
        <link rel="apple-touch-icon" href="http://u1.sinaimg.cn/3g/image/upload/0/110/176/19509/64477c90.png?pos=108&amp;vt=4" />
        <title>AKB48萌妹柏木由纪清新家居写真 甜美可爱</title>
        <link rel="stylesheet" type="text/css" href="http://mjs.sinaimg.cn/wap/dpool/public/201310161120/css/common.min.css" />
        <link rel="stylesheet" type="text/css" href="http://mjs.sinaimg.cn/wap/dpool/public/201310161120/css/red.css" />
        <link rel="stylesheet" type="text/css" href="http://mjs.sinaimg.cn/wap/dpool/hdpic/photo/201402270952/css/style.min.css" />
    </head><body>


<!-- 图片瀑布流容器 -->
<div data-sudaclick="allPics" class="containerbox" style="display:none;">
	<div id="container"></div>
	<div class="loading j_loading"></div>
</div>
<!-- /图片瀑布流容器 -->
<div class="wrapNbox" id="j_main">
	<div class="wrapbox">
<?php include_once $DfInclude;?>
    </div>
</div>
<script src="http://mjs.sinaimg.cn/wap/dpool/hdpic/photo/201402270952/js/zepto.min.js"></script>
<script type="text/javascript" src="<?php echo $this->getStyle('pics.js');?>"></script>
<script>
var isPageShowFlag = false;
var pageshow = function(){
        if (isPageShowFlag) {
            setTimeout(function(){
                // 重刷图片区域
                replayContainer();
                // 重新布局组图
                
                MaskLayer.initMask(3,55021,22054,'column','resize');
                
            }, 100);
        }
    };
$(window).on('pageshow', pageshow);

window.onload = function(){
	setTimeout(function(){
		var resizeEvt = 'onorientationchange' in window ? 'orientationchange' : 'resize'; // 当前窗口改变触发事件
		window.addEventListener(resizeEvt, function(){
			setTimeout(function(){
				
				window.scrollTo(0, 1);      // 500ms地址栏隐藏(加500ms延迟，防止旋转时screen计算错误)
				MaskLayer.initMask(3,55021,22054,'column','resize');
				isPageShowFlag = true;
			}, 500);
		}, false);
		window.scrollTo(0, 1);      // 500ms地址栏隐藏(加500ms延迟，防止旋转时screen计算错误)
		MaskLayer.initMask(3,55021,22054,'column','location');
		isPageShowFlag = true;

	},500);
}

</script>
</body>
</html>