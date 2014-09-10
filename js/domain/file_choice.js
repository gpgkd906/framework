$(function() {
    $blocker = $('#blocker'), $file_zone = $blocker.find(".file_zone");
    $(document).on("click", ".file_choice, .paginate_button a", view.file.load);
    $(document).on("click", "span.media_list", view.file.bechoiced);
    $(document).on("click", "a.media_choice", view.file.choice);
    $(document).on("click", ".media_remove", view.file.remove)
});

window.view = window.view || {};

view.file = (function() {
    var $choice_box = {
        store: {},
        add: function(key, value) {
            this.store[key] = value;
        },
        remove: function(key) {
            if(!!this.store[key]) {
                delete this.store[key];
            }
        },
        has: function(key) {
            return !!this.store[key];
        },
        each: function($call) {
            var $i, $v;
            for($i in this.store) {
                $v = this.store[$i];
                $call($i, $v);
            }
        }
    };
    //init
    $(".media_choiced").each(function() {
        var $this = $(this);
        $choice_box.add($this.find("input").val(), $this.find("img").attr("src"));
    });
    return {
        load: function() {
            var $page = $(this).data("page");
            if(!$page) {
                $page = $(this).attr("href").match(/&page=(\d+)/)[1];
            }
            $.blockUI({ 
                message: $blocker, 
                css: { top: '10%', height: "80%", width: "80%", left: "10%" },
                onUnblock: function() {
                }
            }); 
            window.view.access("api/media/" + $page, null, null, function(res) {
                var $res = $.parseJSON(res), $html = $($res.html);
                window.view.file.refresh.call($html);
                $file_zone.html($html);
            });
            return false;
        },
        refresh: function() {
            $(this).find("input:checkbox").each(function() {
                var $this = $(this), $id = $this.data("id");
                if($choice_box.has($id)) {
                    $this.prop("checked", true);
                }
            });
        },
        bechoiced: function() {
            var $box = $(this).find("input:checkbox"), $id = $box.data("id"), $thumbnail = $box.data("thumbnail"), $checked = $box.prop("checked");
            if($checked) {
                $choice_box.remove($id);
                $box.prop("checked", false);
            } else {
                $choice_box.add($id, $thumbnail);
                $box.prop("checked", true);                
            }
        },
        choice: function() {
            var $file_choiced = $(".file_choiced");
            $(".media_choiced").remove();
            $choice_box.each(function($id, $thumbnail) {
                var $img = ['<div class="col-xs-6 col-md-3 media_choiced">',
                            '<a href="javascript:void(0)" class="thumbnail"><i class="fa fa-times pull-right media_remove"></i>',
                            '<img data-src="holder.js/100%x180" alt="100%x180" src="' + $thumbnail + '" height="70">',
                            '<input type="hidden" name="files[]" value="' + $id + '">',
                            '</a></div>'].join("");
                $file_choiced.append($img);
            });
            $.unblockUI();
        },
        remove: function() {
            $(this).parents(".media_choiced").fadeOut(300, function() {
                $(this).remove();
            });
        }                                      
    }
})();