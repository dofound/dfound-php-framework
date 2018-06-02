/**
 * @fileoverview 正文页-高清图新版V2.0
 * @requires Zepto、Swipe
 * @author huangke2@staff.sina.com.cn
 * @date 2013-08-02
 */

// id选择器
function $id(el){
    if (!el) {
        return null;
    } else if (typeof el == 'string') {
        return document.getElementById(el);
    } else if (typeof el == 'object') {
        return el;
    }
}

// 识别当前浏览器内核
var dummyStyle = document.createElement('div').style,
    vendor = (function(){
        var i,
            t,
            vendors = 't,webkitT,MozT,msT,OT'.split(','),
            len = vendors.length;

        for (i = 0; i < len; i++) {
            t = vendors[i] + 'ransform';
            if (t in dummyStyle) {
                return vendors[i].substr(0, vendors[i].length - 1);
            }
        }

        return false;
    })();

var oContainer = $('#container'),	    // 图片容器
    oLoading = $('.j_loading'),		    // 加载loading
    iLoadingNum = 0,				    // 加载次数
    iCells = 0,						    // 列数
    iWidth = 145,  					    // 图片宽度，图片中间有10px间距，根据屏幕宽度320PX计算图片宽度
    iSpace = 10,					    // 图片间距
    iOuterWidth = iWidth + iSpace,	    // 除第一列图片的其它图片的宽度
    aTop = [],						    // 图片定位的top值
    aLeft = [],						    // 图片定位的left值
    loadPicTimer = null;

var N = 0, // 组图总数
    cid = '', // 图集id
    maxLength = 0, // 组图最大水平滑动距离
    chScrollTop = 0, // 记录频道页scroll值
    tempCurIndex = 0, // 标记当前组图index值
    nextAlbumUrl = '', // 记录下一组图url
    W = getWinWidth(), // 获取窗口宽度
    H = getWinHeight(), // 获取窗口高度
    picList = $id('pic_list'), // 获取组图节点
    channelTitle = document.title, // 正文页标题
    loadingUrl = 'http://u1.sinaimg.cn/upload/2013/05/14/31608.gif'; // 组图loading时的图片url

// 组图浏览组件
var PicSlider = {
    //oParent: picList.parentNode, // 获取组图节点父节点
    oParent: $id('pics_area'),
    curIndex: 0, // 当前组图图片index值
    sDirection: '', // 当前组图水平滑动方向(left、right)
    sDistance: 0,   // 当前组图水平滑动距离
    startX: 0, // 当前组图手指触摸屏幕x轴坐标值
    startY: 0, // 当前组图手指触摸屏幕y轴坐标值
    curLeft: 0, // 当前组图left值
    showFlag: true, // 是否显示组图信息

    // 单张图片加载
    picLoad: function(o, str, index){
        var oImg = new Image();

        oImg.onload = function(e){
            o.src = str; // 加载实际图片

            var imgHeight = 0,
                winWidth = getWinWidth(),
                winHeight = getWinHeight(),
                iWidth = parseInt(this.width),
                iHeight = parseInt(this.height);

            if (iWidth >= iHeight) {
                PicSlider.showFlag && (o.className = 'top');
                o.setAttribute('data-imgtype', 'horizontal');

                var tWidth = 0,
                    tHeight = 0,
                    standard = 3 / 2,
                    scale = iWidth / iHeight;

                if (winWidth < winHeight) {  // 竖屏处理
                    tWidth = winWidth;

                    if (scale >= standard) {
                        tHeight = parseInt(tWidth / standard);
                    } else {
                        tHeight = parseInt(tWidth / scale);
                    }
                } else {  // 横屏处理
                    tHeight = winHeight;

                    if (scale >= standard) {
                        tWidth = parseInt(tHeight * standard);
                    } else {
                        tWidth = parseInt(tHeight * scale);
                    }
                }

                if (scale == 1) {
                    imgHeight = tHeight;
                } else {
                    imgHeight = tHeight + 32;
                }

                o.style.width = tWidth + 'px';
                o.style.height = tHeight + 'px';
            } else {
                imgHeight = iHeight;
                o.className = '';
                o.setAttribute('data-imgtype', 'vertical');
            }

            o.setAttribute('data-imgheight', imgHeight);

            if ((index == 0) || ((tempCurIndex > 0) && (index == tempCurIndex))) {
                // 设置当前组图描述区域高度
                PicSlider.setInfoHeight(imgHeight);
            }
        };

        oImg.src = str;
        oImg.onerror = function(e){
            o.src = 'http://u1.sinaimg.cn/upload/h5/img/default.gif'; // 默认加载缺省图片
        };

        // 30s后图片还未下载，出提醒文字，点击重新加载
        var timer = setTimeout(function(){
            if (o.src != str) {
                o.parentNode.innerHTML = '<span id="refresh" class="refresh_btn">网络无响应，请刷新</span>';
                clearTimeout(timer);
            }
        }, 30000);
    },

    // 触摸touchstart调用函数
    start: function(e){
        // 移除阻止默认事件的侦听
        setTimeout(function(){
            if ('ontouchstart' in document.documentElement) {
                document.body.removeEventListener('touchmove', this.stopDefaultEvt, false);
            } else {
                document.body.removeEventListener('MSPointerMove', this.stopDefaultEvt, false);
            }
        }, 0);

        // 获取第一次touch坐标值
        if (typeof e.touches != 'undefined') {
            PicSlider.startX = e.touches[0].pageX;
            PicSlider.startY = e.touches[0].pageY;
        } else {
            PicSlider.startX = e.pageX;
            PicSlider.startY = e.pageY;
        }

        // 重置组图水平滑动方向
        PicSlider.sDirection = '';

        // 记录当前组图left值
        if ('webkit' == vendor.toLowerCase()) {
            PicSlider.curLeft = PicSlider.getTransformCoordinate(picList.style.webkitTransform, 'x');
        } else {
            PicSlider.curLeft = parseInt(picList.style.left);
        }
    },

    // 移动时判断是左滑还是右滑，左滑时预加载当前张起第4张，无图片时加载推荐页；滑动距离在|4|px之内，算点击事件 targetTouches、changedTouches
    move: function(e){
        var distX = 0,
            distY = 0;

        // 获取触摸滑动位移值(x轴、y轴方向位移)
        if (typeof e.touches != 'undefined') {
            distX = e.touches[0].pageX - PicSlider.startX;
            distY = e.touches[0].pageY - PicSlider.startY;
        } else {
            distX = e.pageX - PicSlider.startX;
            distY = e.pageY - PicSlider.startY;
        }

        // 判断触摸滑动是否是水平方向滑动
        if (Math.abs(distY) < Math.abs(distX)) {
            // 水平向左滑动
            if (distX < -4) {
                PicSlider.sDirection = 'left';
            // 水平向右滑动
            } else if (distX > 4) {
                PicSlider.sDirection = 'right';
            }

            if ((PicSlider.curIndex > 0) || ((PicSlider.sDirection == 'left') && (PicSlider.curIndex == 0))) {
                // 隐藏图片简介
                PicSlider.togglePicInfo(false);
            }

            // 设置滑动动画
            PicSlider.setTransform(distX + PicSlider.curLeft, 0);
        }
    },

    // 触摸touchend调用函数
    end: function(e){
        var toggleTimer = null,
            direct = PicSlider.sDirection,
            curIndex = PicSlider.curIndex;

        if ('left' == direct) {
            if (N == curIndex) {
                // 设置滑动动画
                PicSlider.setTransform(-maxLength, 500);
                // 滑动进入下一组图
                (nextAlbumUrl != '') && (location.href = nextAlbumUrl);
            } else {
                // 加载下一张图片
                PicSlider.next();
                // 显示图片简介
                toggleTimer = setTimeout(function(){
                    PicSlider.togglePicInfo(true);
                    clearTimeout(toggleTimer);
                }, 500);
            }
        } else if ('right' == direct) {
            if (0 == curIndex) {
                // 设置滑动动画
                PicSlider.setTransform(0, 500);
            } else {
                // 加载上一张图片
                PicSlider.prev();
                // 显示图片简介
                //toggleTimer = setTimeout(function(){
                    PicSlider.togglePicInfo(true);
                    //clearTimeout(toggleTimer);
                //}, 500);
            }
        } else {
            var el = e.target;

            //点刷新重新加载当前图片
            if ('refresh' == el.id ) {
                el.parentNode.innerHTML = '<img src="' + loadingUrl + '" data-imgsrc="' + MaskLayer.localData[curIndex].picurl + '" />';

                var curImg = picList.getElementsByTagName('img')[curIndex];
                // 单张图片加载
                PicSlider.picLoad(curImg, MaskLayer.localData[curIndex].picurl, curIndex);
            } else {
                if (curIndex != N) {
                    PicSlider.showFlag = !PicSlider.showFlag;
                    // 显示或隐藏组图信息
                    PicSlider.toggleInfo();
                }
            }
        }
    },

    // 加载上一张图片
    prev: function(){
        // 显示组图信息
        PicSlider.toggleInfo(true);
        // 设置当前滑动距离值
        PicSlider.sDistance = PicSlider.sDistance + W < 0 ? PicSlider.sDistance + W : 0;
        // 设置滑动动画
        PicSlider.setTransform(PicSlider.sDistance, 500);

        if (0 == PicSlider.sDistance) {
            PicSlider.curIndex = 0;
        } else {
            PicSlider.curIndex--;
        }

        var curImg = picList.getElementsByTagName('img')[PicSlider.curIndex],
            curImgHeight = parseInt(curImg.getAttribute('data-imgheight')) || 0;

        // 设置当前组图相关信息
        PicSlider.setInfo(PicSlider.curIndex);
        // 设置当前组图描述区域高度
        PicSlider.setInfoHeight(curImgHeight);
        // ajax请求pv统计
        PicSlider.sendAjaxStatistics('right');
    },

    // 加载下一张图片
    next: function(){
        // 获取组图最大水平滑动距离
        maxLength = W * N;
        // 设置当前滑动距离值
        PicSlider.sDistance = PicSlider.sDistance - W > -maxLength ? PicSlider.sDistance - W : -maxLength;
        PicSlider.curIndex++

        if (PicSlider.curIndex <= N) {
            // 设置滑动动画
            PicSlider.setTransform(PicSlider.sDistance, 500);
            // 滑动动画结束执行函数
            PicSlider.transformEnd();

            if (PicSlider.curIndex == N) {
                $('.pics_info').hide();
                $('.pics_op').css('height', '0px');
                $('.headerbox').css('top', '0px'); // 推荐页强制显示返回按钮

                // ajax请求pv统计(推荐页PV统计)
                PicSlider.sendAjaxStatistics();
            } else {
                // 设置当前组图相关信息
                var curImg = picList.getElementsByTagName('img')[PicSlider.curIndex],
                    curImgHeight = parseInt(curImg.getAttribute('data-imgheight')) || 0;

                // 设置当前组图相关信息
                PicSlider.setInfo(PicSlider.curIndex);
                // 设置当前组图描述区域高度
                PicSlider.setInfoHeight(curImgHeight);
                // ajax请求pv统计
                PicSlider.sendAjaxStatistics('left');
            }
        }
    },

    // 设置当前组图相关信息(标题、简介、组图总数、当前图片index值)
    setInfo: function(index){
        var cData = MaskLayer.localData[index],
            title = cData.title,
            intro = cData.intro;

        if ($.trim(title) !== '') {
            $('#image_title').text(title);
        }

        if (0 == index) {
            if (!intro) {
                $('#image_intro').text($('#album_intro_' + cData.aid).val());
            } else {
                $('#image_intro').text(intro);
            }
        } else {
            if ($.trim(intro) !== '') {
                $('#image_intro').text(intro);
            }
        }

        $('#image_count').text((index + 1) + '/' + N);
        $('#down').attr('href', cData.original);
    },

    // 设置当前组图描述区域高度
    setInfoHeight: function(h){
        var oImgIntro = $('#image_intro');

        if (h) {
            var //curWinHeight = getWinHeight(),
                curWinHeight = $('body').height(),
                blankHeight = curWinHeight - 50 - h - 36 - 2 * 8,
                imgInfoHeight = parseInt(oImgIntro.height());

            if (blankHeight >= imgInfoHeight) {
                oImgIntro.height(blankHeight).addClass('pics_info_t_center');
            } else {
                oImgIntro.css('height', '').removeClass('pics_info_t_center');
            }
        } else {
            oImgIntro.css('height', '').removeClass('pics_info_t_center');
        }
    },

    // 是否显示当前图片简介
    togglePicInfo: function(flag){
        var oPicInfo = $('.pics_info') || null;

        if (oPicInfo) {
            if (flag) {
                if (PicSlider.showFlag && (PicSlider.curIndex < N)) {
                    oPicInfo.show();
                }
            } else {
                PicSlider.showFlag && oPicInfo.hide();
            }
        }
    },

    // 设置滑动动画
    setTransform: function(d, duration){
        picList.style.mozTransitionDuration = duration + 'ms';
        picList.style.transitionDuration = duration + 'ms';
        picList.style.webkitTransitionDuration = duration + 'ms';

        if ('webkit' == vendor.toLowerCase()) {
            picList.style.webkitTransform = 'translate3d(' + d + 'px, 0, 0)';
        } else {
            picList.style.left = d + 'px';
        }
    },

    // 滑动动画结束执行函数
    transformEnd: function(e){
        var curIndex = PicSlider.curIndex;

        if (curIndex < (N - 2)) {
            var curImg = picList.getElementsByTagName('img')[curIndex + 2];

            if (/31608\.gif/i.test(curImg.src)) {
                // 单张图片加载
                PicSlider.picLoad(curImg, curImg.getAttribute('data-imgsrc'), curIndex + 2);
            }
        } else if (curIndex == (N - 2)) {
            if ('' == picList.children[N].innerHTML) {
                // 加载推荐页模块
                PicSlider.lastRecomm();
            }
        }
    },

    // 获取transform坐标值
    getTransformCoordinate: function(v, coordinate){
        var coordinateVal = 0,
            pattern = /([0-9-]+)+(?![3d]\()/gi,
            pos = v.toString().match(pattern);

        if (pos.length) {
            var coordinatePos = coordinate == 'x' ? 0 : coordinate == 'y' ? 1 : 2;
            coordinateVal = parseFloat(pos[coordinatePos]);
        }

        return coordinateVal;
    },

    // 组图信息显示或隐藏
    toggleInfo: function(flag){
        var oImgs = $('#pic_list').find('img[data-imgtype="horizontal"]');

        if (false == PicSlider.showFlag) {
            (oImgs.length > 0) && oImgs.removeClass('top');
            // 隐藏组图信息
            $('.headerbox').css('top', '-50px');
            $('.pics_op').css('height', '0px');
            $('.pics_info').hide();
        } else {
            (oImgs.length > 0) && oImgs.addClass('top');
            // 显示组图信息
            $('.headerbox').css('top', '0px');
            $('.pics_op').css('height', '35px');

            if (flag) {
                var showTimer = setTimeout(function(){
                    $('.pics_info').show();
                    clearTimeout(showTimer);
                }, 500);
            } else {
                $('.pics_info').show();
            }
        }
    },

    // 推荐页模块
    lastRecomm: function(){
        $('#recommand').remove();

        var para = 0,
            winWidth = getWinWidth();

        if (winWidth >= 320) {
            para = 'column';
        }

        var recommand = document.createElement('div');
        recommand.id = 'recommand';

        $.ajax({
            url: '/dpool/hdpic/ajax_action.php?action=rec&ch=' + MaskLayer.ch + '&sid=' + MaskLayer.sid + '&aid=' + MaskLayer.aid + '&cid=' + cid + '&dpr=' + window.devicePixelRatio + '&r=' + Math.random(),
            dataType: 'json',
            success: function(data){
                var albumData = data.hot_album || null;

                if (!albumData) {
                    return false;
                }

                for (var i in albumData) {
                    var lsC = document.createElement('div'),
                        imgC = document.createElement('span'),
                        info = document.createElement('p');

                    imgC.style.backgroundImage = 'url(' + albumData[i].img + ')';
                    info.innerHTML = albumData[i].name;

                    if (albumData[i].is_album == 1) {
                        lsC.appendChild(imgC);
                        lsC.appendChild(info);
                        recommand.appendChild(lsC);

                        (function(index, para){
                            if (window.addEventListener) {
                                var evtType = ('ontouchend' in document.documentElement) ? 'touchend' : 'click';
                                lsC.addEventListener(evtType, function(){
                                    if ('' == PicSlider.sDirection) {
                                        // 初始化组图正文页
                                        if (para == 'column') {
                                            //MaskLayer.initMask(albumData[index].ch, albumData[index].sid, albumData[index].aid, para);
                                            location.href = albumData[index].url + '&page=0&vt=4#' + para;
                                        } else {
                                            //MaskLayer.initMask(albumData[index].ch, albumData[index].sid, albumData[index].aid);
                                            location.href = albumData[index].url + '&page=0&vt=4';
                                        }
                                        document.title = albumData[index].name;
                                    }
                                }, false);
                            } else {
                                lsC.attachEvent('onclick', function(){
                                    // 初始化组图正文页
                                    if (para == 'column') {
                                        //MaskLayer.initMask(albumData[index].ch, albumData[index].sid, albumData[index].aid, para);
                                        location.href = albumData[index].url + '&page=0&vt=4#' + para;
                                    } else {
                                        //MaskLayer.initMask(albumData[index].ch, albumData[index].sid, albumData[index].aid);
                                        location.href = albumData[index].url + '&page=0&vt=4';
                                    }
                                    document.title = albumData[index].name;
                                }, false);
                            }
                        })(i, para);
                    } else {
                        var aEle = document.createElement('a');

                        aEle.setAttribute('href', albumData[i].url);
                        aEle.setAttribute('target', '_blank');
                        aEle.setAttribute('data-sudaclick', 'recommandAdvertisement');
                        aEle.appendChild(imgC);
                        aEle.appendChild(info);
                        lsC.appendChild(aEle);
                        recommand.appendChild(lsC);
                    }
                }

                var oLastLi = picList.getElementsByTagName('li')[N];
                oLastLi.innerHTML = '';
                oLastLi.appendChild(recommand);

                var linkHeight = 0;
                nextAlbumUrl = data.next_url || '';

                if (nextAlbumUrl != '') {
                    var oAlbumLink = document.createElement('div');
                    oAlbumLink.id = 'j_next_album_link';
                    oAlbumLink.className = 'next_album_link';
                    oAlbumLink.innerHTML = '继续左滑进入下一组图';
                    //oAlbumLink.setAttribute('data-link', nextAlbumUrl);
                    oLastLi.appendChild(oAlbumLink);
                    linkHeight = parseInt($('#j_next_album_link').height());
                }

                var aRedEle = document.createElement('a'),
                    aRedImgEle = document.createElement('div');
 
                aRedImgEle.id = 'j_weibo_red_link';
                aRedImgEle.className = 'weibo_red_link';
                aRedEle.setAttribute('href', 'http://sina.cn/redirect.php?pid=713');
                aRedEle.setAttribute('target', '_blank');
                //aRedEle.setAttribute('title', '游戏频道独代产品《霸气江湖》');
                aRedEle.setAttribute('data-sudaclick', 'weiboGameSpread');
                aRedEle.appendChild(aRedImgEle);
                oLastLi.appendChild(aRedEle);
                linkHeight += parseInt($('#j_weibo_red_link').height());

                W = getWinWidth();                              // 当前窗口宽度
				H = getWinHeight();                             // 当前窗口高度

                //设置推荐页组图的布局
				var margin = 10,								// 图片间距
                    defaultW = 640,                             // 当前窗口默认宽度
                    templetH = H - 50 - linkHeight,                     // 获取当前推荐页内容高度 50 = '.addlmtit'返回菜单导航的高度49, linkHeight = '.next_album_link'下一组图提示文字高度
					scale = (213 / 320).toFixed(2),				// H/W比例 = 0.86 实际图片高/宽比例：213/320
					temp = (H < W) ? H : W,	                    // 宽和高取最小的值为宽
					width = (temp < defaultW) ? temp : defaultW,// 最大宽不超过640
					wrapW = width - margin * 2, 				// 容器宽度：W - 图片两个margin
					picW = parseInt((width - margin * 3) / 2),	// 一张图片的宽度
					picH = parseInt(picW * scale);				// 图片高度 = 宽度 * H/W比例

				if ((picH + 29) * 2 > templetH) {		        // 29 = 图片高度24 + 图片margin-top=5; 50 = '.addlmtit'返回菜单导航的高度49
					height = templetH;
					temp = (height < width) ? height : width;
					width = (temp < defaultW) ? temp : defaultW;
					wrapW = width - margin * 2;
					picW = parseInt((width - margin * 3) / 2);
					picH = parseInt(picW * scale);
				}

				$(recommand).css({
					'width': wrapW + 'px',
					'line-height': picH + 'px'
				});
				$('#recommand div').css('width', picW);
				$('#recommand span').css('height', picH);
            }
        });
    },

    // 阻止默认事件
    stopDefaultEvt: function(e){
        e = e || window.event;
        if (e && e.preventDefault) {
            e.preventDefault();
        } else {
            e.returnValue = false;
        }

        return false;
    },

    // ajax请求pv统计
    sendAjaxStatistics: function(direct){
        var source = '',
            href = location.href,
            direct = direct || PicSlider.sDirection;

        if (href.indexOf('&prd=') != -1) {
            source = '&prd=beauty'; // 判断是否来源美女检索器
        }

        if (href.indexOf('&wm=') != -1) {
            var hArr = href.split('&');

            if (hArr) {
                var i,
                    val = '',
                    len = hArr.length;

                for (i = 0; i < len; i++) {
                    val = hArr[i];

                    if ((val != '') && (val.indexOf('wm=') != -1)) {
                        var j,
                            wm_val = '',
                            wmArr = val.split('#');

                        for (j = 0; j < wmArr.length; j++) {
                            wm_val = wmArr[j];

                            if ((wm_val != '') && (wm_val.indexOf('wm=') != -1)) {
                                source += '&' + wm_val;
                                break;
                            }
                        }
                    }
                }
            }
        }

        $.ajax({
            url: '/dpool/hdpic/pv.html?ch=' + MaskLayer.ch + '&sid=' + MaskLayer.sid + '&aid=' + MaskLayer.aid + '&direct=' + direct + '&tindex=' + N + '&cindex=' + (PicSlider.curIndex + 1) + source + '&r' + Date.now() + Math.random().toString().slice(2) // 发送PV数据请求
        });
    },

    // 初始化
    init: function(){
        this.curIndex = 0;
        this.sDirection = '';
        this.sDistance = 0;
        this.startX = 0;

        if (channelTitle == document.title) { // 判断是否是频道页点击链接进去的
            document.title = MaskLayer.localData[0].album_title;
        }

        // 设置当前组图相关信息
        this.setInfo(0);
        // 显示或隐藏组图信息
        this.toggleInfo();

        // 绑定事件
        if (window.addEventListener) {
            if ('ontouchstart' in document.documentElement) {
                this.oParent.removeEventListener('touchstart', this.start, false);
                this.oParent.removeEventListener('touchmove', this.move, false);
                this.oParent.removeEventListener('touchend', this.end, false);
                document.body.removeEventListener('touchmove', this.stopDefaultEvt, false);
                this.oParent.addEventListener('touchstart', this.start, false);
                this.oParent.addEventListener('touchmove', this.move, false);
                this.oParent.addEventListener('touchend', this.end, false);
                document.body.addEventListener('touchmove', this.stopDefaultEvt, false);
            } else {
                this.oParent.removeEventListener('MSPointerDown', this.start, false);
                this.oParent.removeEventListener('MSPointerMove', this.move, false);
                this.oParent.removeEventListener('MSPointerUp', this.end, false);
                document.body.removeEventListener('MSPointerMove', this.stopDefaultEvt, false);
                this.oParent.addEventListener('MSPointerDown', this.start, false);
                this.oParent.addEventListener('MSPointerMove', this.move, false);
                this.oParent.addEventListener('MSPointerUp', this.end, false);
                document.body.addEventListener('MSPointerMove', this.stopDefaultEvt, false);
            }
        }
    }
};

var MaskLayer = {
    ch: 0,
    sid: 0,
    aid: 0,
    layerId: 'layer',
    localData: [],

    // 组图加载函数
    loadPics: function(para){
        var str = '',
            html = '',
            dpr = window.devicePixelRatio;

        W = getWinWidth();                              // 当前窗口宽度
		H = getWinHeight();                             // 当前窗口高度
        picList.className = 'pics_transition';

        if (location.hash.indexOf('#column') != -1) {
            str = '&dpr=2&column=1';
        } else {
            str = '&dpr=' + dpr;
        }

        $.ajax({
            url: 'http://dp.sina.cn/dpool/hdpic/dpool/hdpic/ajax_action.php?action=image&ch=' + MaskLayer.ch + '&sid=' + MaskLayer.sid + '&aid=' + MaskLayer.aid + '&w=' + W + '&h=' + H + str,
            dataType: 'json',
            success: function(data){
                if (!data) {
                    picList.innerHTML = '<li>数据获取失败，请刷新重试</li>';
                    return false;
                }

                MaskLayer.localData = data;
                N = data.length;
                picList.style.width = (N + 1) * W + 'px';

                for (var index in data) {
                    html += '<li style="width:' + W + 'px;"><img title="' + data[index].title + '" src="' + loadingUrl + '" data-imgsrc="' + data[index].picurl + '" /></li>';
                }

                // 为推荐页预留位置
                html += '<li style="width:' + W + 'px;"></li>';

                picList.innerHTML = html;

                // 初始化PicSlider
                PicSlider.init();

                var imgs = picList.getElementsByTagName('img');

                // 第一次加载前3张，不足3张的全部加载，包括推荐页
                if (N < 3) {
                    for (var i = 0; i < N; i++) {
                        // 加载单张图片
                        PicSlider.picLoad(imgs[i], data[i].picurl, i);
                    }

                    // 加载推荐页模块
                    PicSlider.lastRecomm();

                    if (!para) {
                        // 重置当前组图index值
                        tempCurIndex = 0;
                    }
                } else {
                    if (!para) {
                        for (var i = 0; i < 3; i++) {
                            // 加载单张图片
                            PicSlider.picLoad(imgs[i], data[i].picurl, i);
                        }

                        // 重置当前组图index值
                        tempCurIndex = 0;
                    } else {
                        // 针对组图图片定位处理
                        if ((para == 'location') && location.href.indexOf('&page=') != -1) {
                            var flag = false,
                                curUrl = '',
                                urlArr = location.href.split('#'),
                                urlLen = urlArr.length;

                            for (var i = 0; i < urlLen; i++) {
                                if (flag) {
                                    break;
                                }

                                curUrl = urlArr[i];

                                if ((curUrl != '') && (curUrl.indexOf('&page=') != -1)) {
                                    var scurUrl = '',
                                        curlArr = curUrl.split('&'),
                                        curlLen = curlArr.length;

                                    for (var j = 0; j < curlLen; j++) {
                                        scurUrl = curlArr[j];

                                        if ((scurUrl != '') && (scurUrl.indexOf('page=') != -1)) {
                                            tempCurIndex = parseInt(scurUrl.split('=')[1]) || 0;
                                            flag = true;
                                            break;
                                        }
                                    }
                                }
                            }
                        }

                        if (N <= (tempCurIndex + 2)) {
                            for (var k = 0; k < N; k++) {
                                // 加载单张图片
                                PicSlider.picLoad(imgs[k], data[k].picurl, k);
                            }

                            // 加载推荐页模块
                            PicSlider.lastRecomm();
                        } else {
                            var loadNum = (tempCurIndex + 2) < 3 ? 3 : (tempCurIndex + 2);

                            for (var k = 0; k < loadNum; k++) {
                                // 加载单张图片
                                PicSlider.picLoad(imgs[k], data[k].picurl, k);
                            }
                        }
                    }
                }

                if (tempCurIndex > 0) {
                    // 获取组图最大水平滑动距离
                    maxLength = W * N;
                    // 重置组图水平滑动方向
                    PicSlider.sDirection = 'left';
                    // 设置当前组图index值
                    PicSlider.curIndex = tempCurIndex;

                    var tempDistance = -tempCurIndex * W;

                    // 记录当前组图left值
                    PicSlider.curLeft = tempDistance;
                    // 设置当前滑动距离值
                    PicSlider.sDistance = tempDistance;
                    // 设置滑动动画
                    PicSlider.setTransform(tempDistance, 0);

                    if (PicSlider.curIndex <= N) {
                        if (PicSlider.curIndex == N) {
                            $('.pics_info').hide();
                            $('.pics_op').css('height', '0px');
                            $('.headerbox').css('top', '0px'); // 推荐页强制显示返回按钮

                            // 设置当前组图相关信息
                            $('#image_count').text(N + '/' + N);
                            // ajax请求pv统计(推荐页PV统计)
                            PicSlider.sendAjaxStatistics();
                        } else {
                            /*var curImg = picList.getElementsByTagName('img')[PicSlider.curIndex],
                                curImgHeight = parseInt(curImg.getAttribute('data-imgheight')) || 0;*/

                            // 设置当前组图相关信息
                            PicSlider.setInfo(PicSlider.curIndex);
                            // 设置当前组图描述区域高度
                            //PicSlider.setInfoHeight(curImgHeight);
                            // 显示组图信息
                            //PicSlider.toggleInfo();
                        }
                    }
                }

                if (PicSlider.curIndex < N) {
                    // ajax请求pv统计
                    PicSlider.sendAjaxStatistics('left');
                }

                $('#image_total').text(N);
            }
        });

        // 底部footer数据加载
        $.ajax({
            url: '/dpool/hdpic/ajax_action.php?action=data&ch=' + MaskLayer.ch + '&sid=' + MaskLayer.sid + '&aid=' + MaskLayer.aid + '&r=' + Math.random(),
            dataType: 'json',
            success: function(data){
                if (data) {
                    var num = 0,
                        cmnt_num = 0,
                        share_num = 0,
                        praise_num = 0,
                        back_url = '';

                    back_url = data.goback || '';
                    cmnt_num = parseInt(data.cmnt_num) || 0;
                    share_num = parseInt(data.weibo_share_num) || 0;
                    praise_num = parseInt(data.up) || 0;

                    if (back_url != '') {
                        var oBackBtn = $('#top_back_btn');
                        (oBackBtn.length > 0) && oBackBtn.data('href', back_url);
                    }

                    num = (cmnt_num > 999) ? '999+' : cmnt_num;
                    if (cmnt_num > 0) {
                        $('#comment').text(num);
                    } else {
                        $('#comment').text('评论');
                    }
                    $('#comment').attr('href', data.cmnt_url);

                    num = (share_num > 999) ? '999+' : share_num;
                    if (share_num > 0) {
                        $('#weibo_share').text(num);
                    } else {
                        $('#weibo_share').text('分享');
                    }

                    num = (praise_num > 999) ? '999+' : praise_num;
                    if (praise_num > 0) {
                        $('#updown').text(num);
                    } else {
                        $('#updown').text('赞');
                    }

                    //sina.cn统计
                    __sinacnCollect__(data.pc_url, sina_uid, ustat);
                }
            }
        });
    },

    // 初始化组图正文页 参数ch：频道channel ID、sid：图集ID、aid：幻灯片ID、flag：标示window.devicePixelRatio = 2
    initMask: function(ch, sid, aid, flag, para){
        // 解决由浪首及其各频道进入此正文页后，需要点击两次浏览器返回按钮才能真正返回的问题
        if ((location.pathname.indexOf('channel.php') != -1) || (location.pathname.indexOf('column.php') != -1) || ((location.pathname.indexOf('index.php') != -1) && (location.href.indexOf('hdpic/index.php') < 0))) {
            if (flag && (flag != '')) {
                location.hash = '#' + flag + '#a_' + ch + '_' + sid + '_' + aid;
            } else {
                location.hash = '#a_' + ch + '_' + sid + '_' + aid;
            }
        }

        // 清除transition动画导致left回滚的问题
        picList.className = '';
        picList.innerHTML = '';
        picList.style.mozTransitionDuration = '0ms';
        picList.style.transitionDuration = '0ms';
        picList.style.webkitTransitionDuration = '0ms';

        //webkit内核浏览器偏移动画采用transform，其他用left
        if ('webkit' == vendor.toLowerCase()) {
            picList.style.webkitTransform = 'translate3d(' + 0 + 'px, 0, 0)';
        } else {
            picList.style.left = '0px';
        }

        //清除上次组图信息
        $('#image_count').text('');
        $('.pics_info').hide();
        $('.pics_op').css('height', '0px');
        $('.headerbox').css('top', '-50px');

        MaskLayer.ch = ch;
        MaskLayer.sid = sid;
        MaskLayer.aid = aid;
        chScrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;

        // 显示正文页
        $('#' + MaskLayer.layerId).removeClass('hid');
        // 隐藏频道页
        $('#mainbox').hide();

        setTimeout(function(){
            window.scrollTo(0, 1);      // 500ms地址栏隐藏(加500ms延迟，防止旋转时screen计算错误)
            W = getWinWidth();
            H = getWinHeight();
            picList.style.lineHeight = picList.style.height = H + 'px';
            picList.innerHTML = '<li style="width:' + W + 'px;"><img src="' + loadingUrl + '" /></li>';
            MaskLayer.loadPics(para);  // 加载组图
        }, 500);
    },

    // 隐藏正文页，返回频道页
    clsLayer: function(){
        location.hash = '';
        // 重置当前组图index值
        tempCurIndex = 0;

        // 清空瀑布流dom结构并隐藏瀑布流浮层
        $('.containerbox').hide();
        if ($('.titlistbtn').hasClass('cur')) {
            $('.titlistbtn').removeClass('cur');
            $('#image_count').text('').show();
            $('#image_total').text('').hide();
        }
        // 隐藏正文页
        $('#' + MaskLayer.layerId).addClass('hid');
        // 重新设置页面title
        document.title = channelTitle;
        // 清空组图dom结构
        picList.innerHTML = '';
        // 初始化curIndex默认值为0
        PicSlider.curIndex = 0;

        // 移除绑定的事件
        if ('ontouchstart' in document.documentElement) {
            PicSlider.oParent.removeEventListener('touchstart', PicSlider.start, false);
            PicSlider.oParent.removeEventListener('touchmove', PicSlider.move, false);
            PicSlider.oParent.removeEventListener('touchend', PicSlider.end, false);
            document.body.removeEventListener('touchmove', PicSlider.stopDefaultEvt, false);
        } else {
            PicSlider.oParent.removeEventListener('MSPointerDown', PicSlider.start, false);
            PicSlider.oParent.removeEventListener('MSPointerMove', PicSlider.move, false);
            PicSlider.oParent.removeEventListener('MSPointerUp', PicSlider.end, false);
            document.body.removeEventListener('MSPointerMove', PicSlider.stopDefaultEvt, false);
        }

        // 显示频道页
        $('#mainbox').show();
        // 设置scrollTop值
        document.documentElement.scrollTop = chScrollTop;
        document.body.scrollTop = chScrollTop;
    }
};

// 分享到微博
function shareToWb(){
    var oShareBtn = $('#weibo_share');

    if (oShareBtn.length > 0) {
        var pArr = [],
            oCount = $('#image_count');

        oShareBtn.on('click tap', function(){
            pArr = oCount.text().split('/');
            location.href = '/dpool/hdpic/share_weibo.php?ch=' + MaskLayer.ch + '&sid=' + MaskLayer.sid + '&aid=' + MaskLayer.aid + '&page=' + pArr[0];
        });
    }
}

// 初始化赞
function initApproval(){
    var oDownBtn = $('#updown');

    if (oDownBtn.length > 0) {
        oDownBtn.on('click tap', function(){
            var that = $(this);

            if (that.hasClass('liked')) {
                return false;
            }

            var text = '',
                count = 0;

            text = that.text();
            if (text != '赞') {
                if (text.indexOf('+') != -1) {
                    count = '999+';
                } else {
                    count = parseInt(text);
                }
            }

            if (typeof count == 'number') {
                count++;
            }

            // 用户已点选/取消"喜欢"
            $.ajax({
                url: '/dpool/hdpic/ajax_action.php?action=up&ch=' + MaskLayer.ch + '&aid=' + MaskLayer.aid + '&sid=' + MaskLayer.sid + '&like=true'
            });

            that.addClass('liked').text(count);
        });
    }
}

// 初始化图集排版切换按钮
function initPicLayoutSwitchBtn(){
    var oPicCount = $('#image_count'),
        oPicTotal = $('#image_total'),
        oPicLayer = $('.containerbox'),
        oSwitchBtn = $('.titlistbtn');

    if (oSwitchBtn.length > 0) {
        var that = null,
            oImgs = null;

        oSwitchBtn.on('click tap', function(){
            that = $(this);

            if (that.hasClass('cur')) {
                $('.pics').css('visibility', 'visible');

                // 添加阻止默认事件的侦听
                if ('ontouchstart' in document.documentElement) {
                    document.body.removeEventListener('touchmove', PicSlider.stopDefaultEvt, false);
                    document.body.addEventListener('touchmove', PicSlider.stopDefaultEvt, false);
                } else {
                    document.body.removeEventListener('MSPointerMove', PicSlider.stopDefaultEvt, false);
                    document.body.addEventListener('MSPointerMove', PicSlider.stopDefaultEvt, false);
                }

                that.removeClass('cur');
                (oPicCount.length > 0) && oPicCount.show();
                (oPicTotal.length > 0) && oPicTotal.hide();
                (oPicLayer.length > 0) && oPicLayer.hide();
            } else {
                $('.pics').css('visibility', 'hidden');

                // 移除阻止默认事件的侦听
                if ('ontouchstart' in document.documentElement) {
                    document.body.removeEventListener('touchmove', PicSlider.stopDefaultEvt, false);
                } else {
                    document.body.removeEventListener('MSPointerMove', PicSlider.stopDefaultEvt, false);
                }

                //oImgs = oContainer.find('a');

                //if (oImgs.length < 1) {
                    // 初始化数组中的Top、Left值
                    aTop = [];
                    aLeft = [];
                    for (var i = 0; i < iCells; i++) {
                        aTop[i] = 0;
                        aLeft[i] = iOuterWidth * i;
                    }

                    getData();
                //}

                that.addClass('cur');
                (oPicCount.length > 0) && oPicCount.hide();
                (oPicTotal.length > 0) && oPicTotal.show();
                (oPicLayer.length > 0) && oPicLayer.show();
            }
        });
    }
}

// 阻止事件冒泡
function stopBubble(e){
    if (e && e.stopPropagation) {
        e.stopPropagation();
    } else {
        window.event.cancelBubble = true;
    }

    return false;
}

// 阻止默认事件
function stopDefaultEvt(e){
    e = e || window.event;
    if (e && e.preventDefault) {
        e.preventDefault();
    } else {
        e.returnValue = false;
    }

    return false;
}

// 获取窗口宽度
function getWinWidth(){
    var wWidth = 480;

    if (window.innerWidth) {
        wWidth = window.innerWidth;
    } else if (document.body && document.body.clientWidth) {
        wWidth = document.body.clientWidth;
    } else if (document.documentElement && document.documentElement.clientWidth) {
        wWidth = document.documentElement.clientWidth;
    }

    return wWidth;
}

// 获取窗口高度
function getWinHeight(){
    var wHeight = 640;

    if (window.innerHeight) {
        wHeight = window.innerHeight;
    } else if (document.body && document.body.clientHeight) {
        wHeight = document.body.clientHeight;
    } else if (document.documentElement && document.documentElement.clientHeight) {
        wHeight = document.documentElement.clientHeight;
    }

    return wHeight;
}

// 重新布局组图
function relayoutPics(para){
    var re = /#a_(\d+)_(\d+)_(\d+)/g;

    if ((result = re.exec(location.href)) != null) {
        // 初始化组图正文页
        if (location.hash.indexOf('#column') != -1) {
            if (para && (para == 'resize')) {
                MaskLayer.initMask(result[1], result[2], result[3], 'column', para);
            } else {
                MaskLayer.initMask(result[1], result[2], result[3], 'column');
            }
        } else {
            if (para && (para == 'resize')) {
                MaskLayer.initMask(result[1], result[2], result[3], '', para);
            } else {
                MaskLayer.initMask(result[1], result[2], result[3]);
            }
        }
    }

    var cidM = location.search.match(new RegExp('[\?\&]cid=([^\&]*)(\&?)', 'i'));
    cid = cidM ? cidM[1] : '';
}

// 图集瀑布流切换到组图滑动
function picsSwitch(){
    var oPicCount = $('#image_count'),
        oPicTotal = $('#image_total'),
        oPicLayer = $('.containerbox'),
        oSwitchBtn = $('.titlistbtn');

    if ((oSwitchBtn.length > 0) && oSwitchBtn.hasClass('cur')) {
        $('.pics').css('visibility', 'visible');

        // 添加阻止默认事件的侦听
        setTimeout(function(){
            if ('ontouchstart' in document.documentElement) {
                document.body.removeEventListener('touchmove', PicSlider.stopDefaultEvt, false);
                document.body.addEventListener('touchmove', PicSlider.stopDefaultEvt, false);
            } else {
                document.body.removeEventListener('MSPointerMove', PicSlider.stopDefaultEvt, false);
                document.body.addEventListener('MSPointerMove', PicSlider.stopDefaultEvt, false);
            }
        }, 1000);

        oSwitchBtn.removeClass('cur');
        (oPicCount.length > 0) && oPicCount.show();
        (oPicTotal.length > 0) && oPicTotal.hide();
        (oPicLayer.length > 0) && oPicLayer.hide();
    }
}

// 初始化顶部左侧返回按钮
function initTopBackBtn(){
    var href = '',
        oMain = $('#mainbox'),
        oBtn = $('#top_back_btn'),
        oPicLayer = $('.containerbox');

    (oBtn.length > 0) && oBtn.on('click tap', function(){
        if ((oPicLayer.length > 0) && (oPicLayer.css('display') != 'none')) {
            picsSwitch();
        } else {
            if (oMain.length > 0) {
                MaskLayer.clsLayer();
            } else {
                href = $(this).data('href') || '/dpool/hdpic/index.php';
                location.href = href;
            }
        }
    });
}

// 图集瀑布流 开始
// 获取窗口宽度	
function getWindowWidth(){
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

    return {'low': iLow , 'high': iHigh};
}

// 计算列数，设置图片区域宽度
function setContainerWidth(){
    iCells = Math.floor(getWindowWidth() / iOuterWidth);

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

// 获取数据
function getData(){
    // 设置loading状态
    var setLoading = function(type){
        if (type === 'open') {
            oLoading.css('display', 'block');
        } else if (type === 'close') {
            oLoading.css('display', 'none');
        }
    }
    
    //oContainer.html('');
    oContainer[0].innerHTML = '';

    // 打开loading
    setLoading('open');

    // 如果数据返回null，则退出
    var obj = MaskLayer.localData;
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
        var link = '/dpool/hdpic/view.php?ch=' + obj.ch + '&sid=' + obj.sid + '&aid=' + obj.aid + '&page=' + obj.index + '&vt=4#column';
        var outer = $('<a href="' + link + '" ></a>');

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

        tmpImg.src = obj.picurl;
        tmpImg.onload = function(){
            // 如果没有新的加载时，持续对加载完的图片进行定位。
            var picInfo = {
                'src'	: this.src,
                'width'	: this.width,
                'height': this.height,
                'ch'	: obj.ch,
                'sid'	: obj.sid,
                'aid'	: obj.aid,
                'index' : index
            };

            positionPic(picInfo);	// 图片定位
            iLoadingNum++;			// 加载计数
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
}

// 重刷图片区域
function replayContainer(){
    var oLayer = $('.containerbox');

    if ((oLayer.length > 0) && (oLayer.css('display') != 'none')) {
        var iLen = iCells;

        // 计算列数，设置图片区域宽度
        setContainerWidth();

        if (iLen == iCells) {
            return false;
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

        // 移除阻止默认事件的侦听
        setTimeout(function(){
            if ('ontouchstart' in document.documentElement) {
                document.body.removeEventListener('touchmove', PicSlider.stopDefaultEvt, false);
            } else {
                document.body.removeEventListener('MSPointerMove', PicSlider.stopDefaultEvt, false);
            }
        }, 1000);
    }
}

// 标示onpageshow
var isPageShow = false;

// 初始化onpageshow事件
function initPageShow(){
    /*window.onpageshow = function(){
        setTimeout(function(){
            // 重刷图片区域
            replayContainer();
            // 重新布局组图
            relayoutPics();
        }, 100);
    };*/

    var pageShow = function(){
        if (isPageShow) {
            setTimeout(function(){
                // 重刷图片区域
                replayContainer();
                // 重新布局组图
                relayoutPics();
            }, 100);
        }
    };

    $(window).on('pageshow', pageShow);
}
// 图集瀑布流 结束

$(function(){
    var resizeTimer = null,
        resizeEvt = 'onorientationchange' in window ? 'orientationchange' : 'resize'; // 当前窗口改变触发事件

    window.addEventListener(resizeEvt, function(){
        if (resizeTimer) {
            clearTimeout(resizeTimer);
            resizeTimer = null;
        }

        resizeTimer = setTimeout(function(){
            window.scrollTo(0, 1);  // 500ms地址栏隐藏(加500ms延迟，防止旋转时screen计算错误)
            replayContainer();  // 重刷瀑布流图片区域

            if (!$('#layer').hasClass('hid')) {
                tempCurIndex = PicSlider.curIndex || 0;  // 标记当前组图index值

                // 重新布局组图
                if (tempCurIndex > 0) {
                    relayoutPics('resize');
                } else {
                    relayoutPics();
                }
            }

            isPageShow = true;
        }, 500);
    }, false);

    setTimeout(function(){
        window.scrollTo(0, 1);      // 500ms地址栏隐藏(加500ms延迟，防止旋转时screen计算错误)
        // 解决由浪首及其它各频道进入正文页后，再次刷新时造成的两次请求问题
        if ((location.pathname.indexOf('channel.php') != -1) || (location.pathname.indexOf('column.php') != -1) || ((location.pathname.indexOf('index.php') != -1) && (location.href.indexOf('hdpic/index.php') < 0))) {
            relayoutPics();         // 初始化组图
            isPageShow = true;
        }
        initPageShow();             // 初始化onpageshow事件
        initTopBackBtn();           // 初始化顶部左侧返回按钮
        shareToWb();                // 初始化分享到微博
        initApproval();             // 初始化赞
        initPicLayoutSwitchBtn();   // 初始化图集排版切换按钮
    }, 500);
});