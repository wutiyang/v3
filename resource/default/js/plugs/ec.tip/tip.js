/** 
 * 确认、提示 插件
 * @class   allPage
 * @author  zhuzhengwei
 * @lastModified  2014/09/17
 * @dependon jquery-1.4.1.min.js or later、 ec.lib.js
 */
!(function ($){
    $.fn.tip = function (opt) {
        var $this = $(this);
        var defaults = {
            width : 'auto',
            className : '',
            mainWidth : 1200,
            callback : null, //回调方法
            showBtn : false, //是否显示按钮
            isOk : null, //点击确定时触发事件
            isCancel : null, //点击取消时触发事件
            okText : "OK",
            cancelText : "Cancel",
            autoHide : true,
            position : "bl", //['bl', 'br', 'left', 'right'], 下左， 下右，左， 右
            msg : "loading..."
        };
        var opt = $.extend(defaults, opt);

        //模板
        var html = '<div class="ec_tips" id="ecTips">';
                html += '<i class="icon_tips_arr"></i>';
                html += '<div class="ec_tips_content">';
                    html += '<div class="ec_tips_msg">'+ opt.msg +'</div>';
                html += '</div>';
                html += '<div class="ec_tips_btn">';
                    html += '<a class="ec_tips_btn_ok" href="javascript:;">'+ opt.okText +'</a>';
                    html += '<a class="ec_tips_btn_cancel" href="javascript:;">'+ opt.cancelText +'</a>';
                html += '</div>';
            html += '</div>';
        //绑定按钮事件
        var _bindBtnEvent = function (tpl) {
            if(!tpl) return;
            var $thix = $(tpl);
            $okBtn = $thix.find('.ec_tips_btn_ok');
            $cancelBtn = $thix.find('.ec_tips_btn_cancel');
            $closeBtn = $thix.find('.ec_tips_btn_close');

            $okBtn.add($cancelBtn).add($closeBtn).off('click').on('click', function () { $thix.hide(); });

            if(opt.isOk) $okBtn.off('click').on('click', function () { opt.isOk.call($thix);  });
            if(opt.isCancel) $cancelBtn.off('click').on('click', function () { opt.isCancel.call($thix); });
            if(opt.isClose) $closeBtn.off('click').on('click', function () { opt.isClose.call($thix); });
        };
        //定位
        var _position = function ($tpl, $ele) {
            var $thix = $ele;
            var offset = $thix.offset();
            var $arrTop = $tpl.find('.icon_tips_arr');
            var miniWidth = (opt.width > 0) ? opt.width : 100;
            var winWidth = $(window).width(); //浏览器宽度
            var sideWidth = Math.ceil((winWidth - opt.mainWidth) / 2); //浏览器两边空白区域宽度
            var getCss = function () {
                var css = {
                    "eleCss" : { "width" : $ele.data('width') || opt.width },
                    "arrCss" : {}
                };

                switch(opt.position) {
                    case "bl" :
                        var left = Math.ceil(offset.left) - sideWidth;
                        if(opt.mainWidth - left <= miniWidth) {
                            opt.position = "br";
                            return getCss();
                            break;
                        }
                        css.eleCss.left =  left + 'px';
                        css.eleCss.top = Math.ceil(offset.top + $thix.height()) + 10 + 'px';

                        css.arrCss.left = "5px";
                        css.arrCss.top = "-7px";
                        break;
                    case "br" :
                        css.eleCss.right = Math.ceil(winWidth - (offset.left + $thix.width())) - sideWidth + 'px';
                        css.eleCss.top = Math.ceil(offset.top + $thix.height()) + 10 + 'px';

                        css.arrCss.right = "5px";
                        css.arrCss.top = "-7px";
                        break;
                    case "left" :
                        css.eleCss.right = Math.ceil(winWidth - offset.left) - sideWidth + 10 + 'px';
                        css.eleCss.top = Math.ceil(offset.top) + 'px';

                        css.arrCss.right = "-7px";
                        css.arrCss.top = "5px";
                        break;
                    case "right" :
                        var left = Math.ceil(offset.left + $thix.width()) - sideWidth + 10;
                        if(opt.mainWidth - left <= miniWidth) {
                            opt.position = "left";
                            return getCss();
                            break;
                        }
                        css.eleCss.left = left + 'px';
                        css.eleCss.top = Math.ceil(offset.top) + 'px';

                        css.arrCss.left = "-7px";
                        css.arrCss.top = "5px";
                        break;
                }

                return css;
            };

            var css = getCss();
            $arrTop.css({"left":"auto", "right":"auto"}).css(css.arrCss);
            $tpl.css({"left":"auto", "right":"auto"}).css(css.eleCss).prop("class", "ec_tips " + opt.className + ' ' + opt.position);
        };

        $this.each(function (i, n) {
            var $thix = $(n);
            var targetId = $thix.data('targetid');
            var $tpl = $('#ecTips');
            var $thisContent;
            var $title;
            var $okBtn;
            var $cancelBtn;
            var msg = $thix.data('tip') || ((targetId) ? $('#' + targetId).html().trim() : opt.msg);

            if(!msg) return;
            if(!$tpl[0]) {
                $tpl = $(html);
                $('<div></div>').css({
                    'width' : opt.mainWidth,
                    'height' : 1,
                    'position' : 'relative',
                    'z-index' : 11,
                    'margin' : '-1px auto 0 auto'
                }).append($tpl).prependTo('body');
            }

            //设置内容
            $tpl.show().find('.ec_tips_msg').html(msg);
            _bindBtnEvent($tpl);
            _position($tpl, $thix);

            if(!opt.showBtn) { $tpl.find('.ec_tips_btn').addClass('hide'); }

            //回调和自动隐藏
            if(opt.callback && $.isFunction(opt.callback)) {
                opt.callback.call($thix, $tpl);
            } else if(opt.autoHide){
                setTimeout(function () {$tpl.hide()}, 1200);
            }

        });
    };
})(jQuery);