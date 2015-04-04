$(document).ready(function() {
$("<div id='isven_popup_window_drag_shade'></div><div id='isven_popup_window'><div class='titlebar'><span style='float: left'>&nbsp;<b class='title_text'></b></span><a href='javascript:void(0)' onclick='close_isven_popup_window()' title='关闭'><img src='../images/btn_close.jpg'/>&nbsp;&nbsp;</a></div><iframe name='isven_popup_window_frame_name' frameborder='0' scrolling='no'></iframe></div>").appendTo(document.body);
});
var _isven_popup_window_x, _isven_popup_window_y;
var _isven_popup_window_moving = false;
function show_isven_popup_window(title, width, height, pageUrl) {
    isven_popup_window_show_select(false); // 隐藏select标签
    $('#isven_popup_window .title_text').html(title); // 显示窗口的标题
    var objDrag = $('#isven_popup_window');
    objDrag.css({ display: 'block' });
    var url = -1 == pageUrl.indexOf('?') ? pageUrl + '?guid=' + Math.random() : pageUrl + '&guid=' + Math.random();
    objDrag.find('iframe').attr('src', pageUrl).css({ width: (width + 2) + 'px', height: height + 'px', padding: '5px' });
    var pageViewWidth = parseInt($(window).width());
    var pageViewHeight = parseInt($(window).height());
    var pageScrollTop = parseInt($(document).scrollTop());
    var pageScrollLeft = parseInt($(document).scrollLeft());
    var objTop = (pageViewHeight - height) / 2 + pageScrollTop;
    var objLeft = (pageViewWidth - width) / 2 + pageScrollLeft;
    objDrag.css({ width: (width + 12) + 'px', left: objLeft + 'px', top: objTop + 'px' });
    //if (window.MessageEvent && !document.getBoxObjectFor) {}

    // 添加一个遮罩层
    $('#isven_popup_window_drag_shade').css({ display: 'block', width: $(document).width(), height: $(document).height() });
    $(document.body).css({ overflow: 'hidden' });
    // 当窗体的标题处被鼠标按下后，激活拖动功能
    $('#isven_popup_window .titlebar').mousedown(function(e) {
        _isven_popup_window_x = e.pageX - parseInt(objDrag.css('left'));
        _isven_popup_window_y = e.pageY - parseInt(objDrag.css('top'));
        _isven_popup_window_moving = true;
    });
    // 在鼠标松开之前
    $(document).mousemove(function(e) {
        if (_isven_popup_window_moving) {
            var x = e.pageX - _isven_popup_window_x;
            var y = e.pageY - _isven_popup_window_y;
            objDrag.css({ left: x + 'px', top: y + 'px' });
        }
    }).mouseup(function() {
        _isven_popup_window_moving = false;
    });
    $(document.body).css({ overflow: 'hidden' });
}
function close_isven_popup_window() {
    $('#isven_popup_window').find('iframe').attr('src', '');
    $('#isven_popup_window').css({ display: 'none' });
    $('#isven_popup_window_drag_shade').css({ display: 'none' });
    isven_popup_window_show_select(true);
    $(document.body).css({ overflow: 'auto' });
}
function isven_popup_window_show_select(flag) {
    //        $(document).find("select:not('#dd')").each(function() {
    //            if (flag) $(this).show();
    //            else $(this).hide();
    //        });
}