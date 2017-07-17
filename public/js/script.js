/*back-top*/
$(document).ready(function() {  
	$('#pageTop a').click(function () {
		$('html,body').animate({scrollTop:'0px'},1000);return false;
});
});
/*back-top*/

/*target _blank*/
$(document).ready( function () {
    $('.blank').click(function(){
        window.open(this.href, '_blank');
        return false;
    });
});
/*target _blank*/

/*is not smartphone*/
$(document).ready( function () {
	if(!navigator.userAgent.match(/(iPhone|Android)/)){
		$(function() {
			$('.carouselArea ul').jcarousel({
        scroll: 1,
        auto: 0,
　　		wrap: 'circular',
    	});
		});
		
        $(document).on("click", ".tabs li", function() {
            var $this = $(this), $ref;
            if($this.hasClass("current")) {
                return false;
            }
            $(".tabs li").removeClass("current");
            $this.addClass("current");
            $ref = $this.data("ref");
            $(".information > div").css("display", "none").removeClass("current");
            $(".information > div[data-id=" + $ref + "]").css("display", "block").addClass("current");
        });

	}
});
/*is not smartphone*/

/*is smartphone*/
$(document).ready( function () {
	if(navigator.userAgent.match(/(iPhone|Android)/)){
		$("#contentsInner>.sideBar .ns03 h2").click(function(){
			$("#contentsInner>.sideBar .ns03 .sub").slideToggle();
		});

		var $tabs = $(".tabs");
        $("li", $tabs).hide();
        $(".current", $tabs).show();
        $tabs.addClass("tab_close");
        $(".tabs li").click(function() {
            var $this = $(this), $ref;

            if($tabs.hasClass("tab_open")) {
                $("li", $tabs).removeClass("current").hide();
                $this.addClass("current").show();
                $tabs.removeClass("tab_open").addClass("tab_close");
                $ref = $this.data("ref");
                $(".information > div").css("display", "none").removeClass("current");
                $(".information > div[data-id=" + $ref + "]").css("display", "block").addClass("current");
            } else if($tabs.hasClass("tab_close")) {
                $("li", $tabs).show();
                $tabs.removeClass("tab_close").addClass("tab_open");
            }
        });
	}
});
/*is smartphone*/