/** 
 * 详情页商品图片展示插件
 * @class   good.php
 * @author  zhuzhengwei
 * @lastModified  2014/09/17
 * @dependon jquery-1.4.1.min.js or later, ec.box
 */
(function ($) {
    $.fn.imgZoom = function (options) {
        var _default = {
            width: 350,
            height : 350,
            boxTpl : null,
            thumSize : 6, //缩略图数量
            showBox : true //是否弹窗展示大图片
        };
        var opt = $.extend(_default, options);
        var $thix = $(this);
        if(!$thix[0]) return;

        var $imgZoom = $thix.find('.imgZoom');
        var $imgZommList = $thix.find('.imgZoomLi');
        var $imgZoomLeftBtn = $thix.find('.arrow_left');
        var $imgZoomRightBtn = $thix.find('.arrow_right');
        var unClass = 'arrow_disabled';
        var $thumLi = $imgZommList.children('li');
        var imgLen = $thumLi.length;
        var thumLiWidth = 0;
        var pageCount = (imgLen > opt.thumSize) ? Math.ceil(imgLen / opt.thumSize) : 1;
        var positionLeft = 0;
        var colIndex = 1;
        var _imgCache = ec.util.cache.get('imgZoomList', []);
        var boxImgZommIndex = 0;
        var _imgLoading = function ($ele, src) {
            var $imgObj = $ele.hide();
            if($.inArray(src, _imgCache) > -1){
                $ele.prop('src', src).show();
                return;
            }
            var obj = new Image();
            obj.src = src;
            obj.onload=function(){
                $imgObj.prop('src', this.src).show();
                _imgCache.push(this.src);
                ec.util.cache.set('imgZoomList', _imgCache);
                $imgObj = obj = null;
            }
        };
        //弹出框预览图片切换
        var _boxImgZoomTab = function (ele, act) {
            if(act == 'left'){
                boxImgZommIndex --;
            } else {
                boxImgZommIndex ++;
            }
            if(boxImgZommIndex >= imgLen) {
                boxImgZommIndex = 0;
            }
            if(boxImgZommIndex < 0) {
                boxImgZommIndex = imgLen-1;
            }
            var $imgObj = $(ele).siblings('.box_zoom_content').children('img');
            var src = $thumLi.eq(boxImgZommIndex).find('img').data('original500');
            _imgLoading($imgObj, src);
        };
        var _checkBtn = function (page) {
            if(page <= 1){
                $imgZoomLeftBtn.addClass(unClass);
                $imgZoomRightBtn.removeClass(unClass);
                return;
            }
            if(page >= pageCount) {
                $imgZoomRightBtn.addClass(unClass);
                $imgZoomLeftBtn.removeClass(unClass);
                return;
            }

            $imgZoomLeftBtn.add($imgZoomRightBtn).removeClass(unClass);
        };

        var gotoPage = function (page) {
            if(page < 1 || page > pageCount || colIndex == page) return;

            var left = 0 - (page-1) * (opt.thumSize * thumLiWidth);
            $imgZommList.stop().animate({
                "left" : left
            }, 200);
            _checkBtn(page);
            colIndex = page;
        };


        $thix.find('.img_zoom_original').css({"width": opt.width + "px", "height": opt.height + "px"});
        $thix.each(function (i, n) {
            if($thumLi.length > 0){
                thumLiWidth = $thumLi.eq(0).innerWidth();
                $thumLi.on('click', function () {
                    var $thix = $(this);
                    var imgSrc = $thix.find('img').data('original350');
                    _imgLoading($imgZoom, imgSrc);
                    $thix.addClass('current').siblings().removeClass('current');
                });
            }

            if(imgLen > opt.thumSize) {
                $imgZoomLeftBtn.on('click', function () {
                    gotoPage(colIndex-1);
                }).addClass(unClass).show();
                $imgZoomRightBtn.on('click', function () {
                    gotoPage(colIndex+1);
                }).show();
            }

            if(opt.showBox){
                //弹出框预览效果
                $imgZoom.on('click', function () {
                    var $thix = $(this);
                    var index = ($thumLi.length > 0) ? $('.current', $imgZommList).index() : 0;
                    var imgSrc = (index > 0) ? $thumLi.eq(index).find('img').data('original500') : ($thix.data('original500') || $thix[0].src);
                    var html = opt.boxTpl;
                    ec.ui.box(html, {
                        "boxclass": "box_img_zoom",
                        "width" : 600,
                        "height" : 580,
                        "showTitle" : false,
                        "showButton" : false,
                        "onopen" : function (box) {
                            var $thix = box;
                            _imgLoading($thix.find('img'), imgSrc);

                            if($thumLi.length < 1) return;

                            var $btnLeft = $thix.find('.box_zoom_arr_l');
                            var $btnRight = $thix.find('.box_zoom_arr_r');

                            $btnLeft.on('click', function () {
                                _boxImgZoomTab($(this), 'left');
                            }).show();
                            $btnRight.on('click', function () {
                                _boxImgZoomTab($(this), 'right');
                            }).show();
                        }
                    }).open();
                    boxImgZommIndex = index;
                });

            }

        });
        return gotoPage;
    };
})(jQuery);
