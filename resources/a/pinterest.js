/**
 * 功能描述： 主实实现美女检索器的瀑布流功能。
 * 背景：主要是由于图片来源不提供图片尺寸。因此前端需要做一次性加载8张时，需要8张图片都loading完成，后再排序，然后统一定位到页面上。 
 * 采用上述原因：考虑到图片大小不同，加载时间不同，如果不统一加载完后再排序进行定位的话，图片来源的顺序会被打乱，因为改成单次处理的，则加载快的图片会优化插入做定位处理
 * @update : 由于图片随机给出，为了快速加载图片，采取加载完每一张图片后直接定位的规则
 * @author : wangfeng8
 * @date 2012-08-21
 * @update huangke2(huangke2@staff.sina.com.cn) 2013-09-06
 * @requires zepto
 */

$(function(){
	var oContainer = $('#container'),	    // 图片容器
		oLoadMore = $('#j_loadMore'),	    // 加载更多button
		oLoading = $('.j_loading'),		    // 加载loading
        oTabs = $('#j_mmType').find('a'),   // 切换tabs
		iCounts = 1,					    // 加载计数
		iLoadingNum = 0,				    // 加载次数
		iCells = 0,						    // 列数
		iWidth = 145,  					    // 图片宽度，图片中间有10px间距，根据屏幕宽度320PX计算图片宽度
		iSpace = 10,					    // 图片间距
		iOuterWidth = iWidth + iSpace,	    // 除第一列图片的其它图片的宽度
		aTop = [],						    // 图片定位的top值
		aLeft = [],						    // 图片定位的left值
		sPicType = '',					    // 当前图片类型，是“萝莉、全裸、爆乳、女神” 
		loadPicTimer = null,
		resizeEvt = 'onorientationchange' in window ? 'orientationchange' : 'resize';	// 屏幕窗口大小改变事件

	// 获取窗口宽度	
	function getWinWidth(){
		var wWidth = 320;

		if (document.body && document.body.clientWidth) {
			wWidth = document.body.clientWidth;
		} else if (document.documentElement && document.documentElement.clientWidth) {
			wWidth = document.documentElement.clientWidth;
		}

		return wWidth;
	}

	// 获取最低列的索引
	function getCellsIndex(){
		var i,
            iLow = 0,
            iHigh = 0,
            iLowVal = aTop[0],
            iHighVal = aTop[0];

		for (i = 1; i < aTop.length; i++) {
			if (aTop[i] < iLowVal) {
                iLow = i;
				iLowVal = aTop[i];
			} else if (aTop[i] > iHighVal) {
                iHigh = i;
				iHighVal = aTop[i];
			}
		}

		return {'low' : iLow , 'high' : iHigh};
	}

	// 计算列数，设置图片区域宽度
	function setContainerWidth(){
		iCells = Math.floor(getWinWidth() / iOuterWidth);

		if (iCells > 5) {
			iCells = 5;
		}

		oContainer.css('width', iCells * iOuterWidth - iSpace);
	}

	setContainerWidth();

	// 初始化第一行每列图片的left和top值
	for (var i = 0; i < iCells; i++) {
		aTop[i] = 0;
		aLeft[i] = iOuterWidth * i;
	}

	// 获取正确的请求地址
	var ajax_url = location.href;
	if (typeof api_url != 'undefined') {
		ajax_url = api_url;
	}

	// 获取数据
	function getData(){
		// 设置loading状态
		var setLoading = function(type){
			if (type === 'open') {
				oLoadMore.css('display', 'none');
				oLoading.css('display', 'block');
			} else if (type === 'close') {
				oLoadMore.css('display', 'block');
				oLoading.css('display', 'none');
			}
		}

		// 打开loading
		setLoading('open');

        var url = encodeURI(ajax_url + 'tag=' + sPicType + '&counts=' + iCounts);
		$.ajax({
			url: url,
			type: 'get',
			data: {},
			dataType: 'json',
			cache: false,
			timeout: 30000,	// 超时设置为30秒钟
			success: function(data){
				if (data.status) {
					// 如果数据返回null，则退出
					var obj = data.data;
					if (Object.prototype.toString.call(obj) !== '[object Array]' || obj.length == 0) {
						return false;
					}

					// 图片定位
					var positionPic = function(obj){
						if (obj.length == 0) {
							return false;
						}

						var oImg = $('<img />'),									//创建图片对象
							iHeight = parseInt(obj.height * (iWidth / obj.width)),	//图片高度
							index = getCellsIndex().low;							//最低列的索引

						oImg.css({
							width	:	iWidth,
							height	:	iHeight,
							left	:	aLeft[index],
							top		:	aTop[index]
						});

						oImg.attr('src', obj.src);
						// 为定位top增加上间距
						aTop[index] += iHeight + 10;
						// var link = 'http://dp.sina.cn/dpool/hdpic/view.php?ch=' + obj.ch + '&sid=' + obj.sid + '&aid=' + obj.aid + '&vt=4#column';
						var outer = $('<a href="' + obj.link + '" ></a>');

						outer.append(oImg);
						oContainer.append(outer);

						// 最高列的索引
						var index = getCellsIndex().high;
                        // 设置图片区域的高度
						oContainer.css('height', aTop[index]);
					}

					// 加载单个图片，获取当前图片信息
					var loadPic = function(index, obj){
						var tmpImg = new Image();

						tmpImg.src = obj.img_url;
						tmpImg.onload = function(){
							// 如果没有新的加载时，持续对加载完的图片进行定位。
							//alert(iCounts+':'+obj.counts);
							//if (obj.counts == iCounts) {
								var picInfo = {
									'src'	: this.src,
									'width'	: this.width,
									'height': this.height,
									'link'	: obj.link,
									'ch'	: obj.ch,
									'sid'	: obj.sid,
									'aid'	: obj.aid
								};

								positionPic(picInfo);	// 图片定位
								iLoadingNum++;			// 加载计数
							//} else {
							//	loadPicTimer == null;
							//}
						}
					}

                    // 循环加载图片
					var len = obj.length;
					for (var i = 0; i < len; i++) {
						loadPic(i, obj[i]);
					}

					// 计算加载完成, 如果加载完所有图片，或是加载图片超过30秒，则默认为加载完成
					var start = new Date().getTime();
					loadPicTimer = setInterval(function(){
						var end = new Date().getTime(),
							t = parseInt((end - start) / 1000);

						if (iLoadingNum >= len || t == 30) {
							iLoadingNum = 0;
							clearInterval(loadPicTimer);
							// 关闭loading
							setLoading('close');
						}
					}, 300);
				} else {
					//关闭loading
					setLoading('close');
                    alert(data.message);
				}
			},
			error: function(){
				//关闭loading
				setLoading('close');
				alert('您的网络不给力,请点击重试!');
			}
		});
	}

	// 屏幕旋转，窗口大小改变处理
	$(window).on(resizeEvt, function(){
        // 重刷图片区域
		replayContainer();
	});
    
    // 重刷图片区域
    function replayContainer(){
        var iLen = iCells;

		// 计算列数，设置图片区域宽度
		setContainerWidth();

		if (iLen == iCells) {
			return;
		}

		// 初始化数组中的Top、Left值
		aTop = [];
		aLeft = [];

		for (var i = 0; i < iCells; i++) {
			aTop[i] = 0;
			aLeft[i] = iOuterWidth * i;
		}

		// 重新定位图片位置
		oContainer.find('img').each(function(){
			var index = getCellsIndex().low;
			$(this).css({
				left : aLeft[index],
				top	 : aTop[index]
			})

			aTop[index] += $(this).height() + 10;

			var index = getCellsIndex().high;
			oContainer.css('height', aTop[index]);	// 设置图片区域的高度。
		});
    }

	// 导航菜单初始化
	function navBind(that){
		var that = $(that);

		sPicType = that.text();
		that.addClass('active').siblings().removeClass('active');

		// 加载计数
		iCounts++;

		// 初始化内容
		oContainer.css('height', '60px');
		oContainer.find('img').remove();

		// 初始化数组中的Top、Left值
		aTop = [];
		aLeft = [];

		for (var i = 0; i < iCells; i++) {
			aTop[i] = 0;
			aLeft[i] = iOuterWidth * i;
		}

		// 请求数据
		getData();
	}

    // 初始化onpageshow事件
    function initPageShow(){
        window.onpageshow = function(){
            setTimeout(function(){
                // 重刷图片区域
                replayContainer();
            }, 100);
        };
    }

	// 初始化
	setTimeout(function(){
		getData();	    								            // 请求数据，瀑布流初始化
        initPageShow();                                             // 初始化onpageshow事件
		oLoadMore && oLoadMore.on('click', getData);	            // 加载更多
		oTabs && oTabs.on('click', function(){navBind(this);});     // 导航事件绑定
	}, 100);
});