     var x = 10;
     var y = 10;
     function getimgtip(){
     $("div.progressName a[href][target]").mouseover(function (e) {
         //alert($(this).attr("href"));//qhjsw.net
         var tooltip = "<div id=\"msgtip\"><img id=\"ig\" src=\"" + $(this).attr("href") + "\"  alt=\"\" /></div>";
        // alert(tooltip);
         $("body").append(tooltip);
             $("#msgtip").css({
                 "top": (e.pageY + y) + "px",
                 "left": (e.pageX + x) + "px"
             }).show("fast");   //设置x坐标和y坐标，并且显示
     }).mouseout(function () {
         $("#msgtip").remove();   //移除 
     }).mousemove(function (e) {
         $("#msgtip").css({
             "top": (e.pageY + y) + "px",
             "left": (e.pageX + x) + "px"
         });
     });
 }
	