$(function() {
    var $loaded = {};
    $(document).on("click", ".article_loader", function() {
        var $this = $(this), $id = $this.data("id"), $type = $this.data("type");
        if(!!$loaded[$id]) {
            return false;
        }
        view.access("api/articles/", null, {id: $id, type: $type}, function(res) {
            var $res = $.parseJSON(res), $html = $($res.html);
            $this.parents(".panel").find(".panel-body").html($html);
        });
    });
});