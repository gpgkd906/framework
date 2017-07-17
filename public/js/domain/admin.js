$(function() {
    //side active
    (function() {
        var $lis = $("li.active").parents("li");
        $lis.addClass("active");
        $lis.children("ul").removeClass("collapse").addClass("in");
    })();

});