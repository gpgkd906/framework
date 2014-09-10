$(function() {
    //postzip
    $(document).on("click", "#get_postzip", function() {
        var $postzip = [$("[name=postzip1]").val(), $("[name=postzip2]").val()].join("");
        window.view.access("api/postzip/" + $postzip, null, {postzip: $postzip}, function(req) {
            var $res = $.parseJSON(req).res[0];
            window.$res = $res;
            $("[name=address1]").val($res.city + $res.town);
        });
    });
});