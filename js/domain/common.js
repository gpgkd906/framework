$(function() {
    $(document).on("click", "[data-action]", function() {
        var $this = $(this), $domain = $this.data("domain"), $action = $this.data("action");
        if($domain === "bestory") {
            if(!!common[$action]) {
                common[$action].call(this);
            }
        }
    });
});

var common = (function() {
    var $loading = {};
    return {
        fetch_more_student: function() {
            if(!!$loading.fetch_more_student) {
                return false;
            }
            var $this = $(this), $offset = $this.data("offset");
            $loading.fetch_more_student = true;
            window.view.access("api/student/" + $offset + "/15", null, null, function(res) {
                var $res = $.parseJSON(res);
                if(!$res.html) {
                    $this.remove();
                } else {
                    $(".student.list ul").append($res.html);
                    $this.data("offset", $offset + 15);
                }
                $loading.fetch_more_student = false;
            });
        },
        fetch_more_president: function() {
            if(!!$loading.fetch_more_president) {
                return false;
            }
            var $this = $(this), $offset = $this.data("offset");
            $loading.fetch_more_president = true;
            window.view.access("api/president/" + $offset + "/15", null, null, function(res) {
                var $res = $.parseJSON(res);
                if(!$res.html) {
                    $this.remove();
                } else {
                    $(".ceo.list ul").append($res.html);
                    $this.data("offset", $offset + 15);
                }
                $loading.fetch_more_president = false;
            });
        },
        fetch_more_president_by_category: function() {
            if(!!$loading.fetch_more_president_by_category) {
                return false;
            }
            var $this = $(this), $offset = $this.data("offset"), $category = $this.data("category");
            $loading.fetch_more_president_by_category = true;
            window.view.access("api/category_president/" + $category + "/" + $offset + "/15", null, null, function(res) {
                var $res = $.parseJSON(res);
                if(!$res.html) {
                    $this.remove();
                } else {
                    $(".ceo.list ul").append($res.html);
                    $this.data("offset", $offset + 15);
                }
                $loading.fetch_more_president_by_category = false;
            });
        }
    }
})();