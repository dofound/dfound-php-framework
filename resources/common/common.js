/*** 滚动效果 ***/
var _pscroll = {};
_pscroll.pageScroll = function(height){
    if(jQuery.browser.mozilla || jQuery.browser.msie){
        scrolldelay = setTimeout('_pscroll.pageScroll('+height+')',1);
        if(document.documentElement.scrollTop < height){
			clearTimeout(scrolldelay);
		} else {
	        window.scrollBy(0,-20);
		}
    }else{
        scroll(0, height);
    }
}