$(function() {
    $(document).on("dblclick", "form label[data-fixable]", function() {
        var $this = $(this), $html, $input, $fixable = $this.next();
        $store = $this.find("label").detach();
        $value = $fixable.val();
        $input = $("<input type='text' class='form-control' value='" + $value +"'>");
        $input.blur(function() {
            var $new = $(this).val();
            if(!!$new) {
                $fixable.val($new);
                $(this).remove();
                $this.html($new);
            } else {
                $this.html($value);         
            }
        });
        $this.html($input);
        $input.focus();
    });
    $(document).on("click", ".twinkle.twinkle-add", function() {
        var $form = $(".twinkle_form"), $title, $id, $submit, $html;
        if($title = prompt("追加したい項目名を入力してください")) {
            $id = "twinkle" + (new Date().valueOf());
            $submit = $form.find(".form-group:eq(-1)");
            $html = $(['<div class="form-group">',
                       '<label style="display:inline">',
				       '<label data-fixable="profile9" style="background-color:#bce8f1">' + $title + '</label>',
					   '<input type="hidden" name="fixable[' + $id + ']" value="' + $title + '"><label class="form_label form_radio"><input type="radio" name="visible[' + $id + ']" value="1" checked="">表示</label><label class="form_label form_radio"><input type="radio" name="visible[' + $id + ']" value="0">非表示</label><i class="fa fa-times pull-right twinkle twinkle_delete" data-deleteble="' + $id + '"></i></label>',
				       '<input type="text" name="twinkle_add[' + $id + ']" value="" class=" form-control"></div>'].join(""));
            $html.insertBefore($submit);
            $html.find(":text").focus();
            $('html, body').animate({
                scrollTop: $html.offset().top
            }, 1000);
        };     
    });
    $(document).on("click", ".twinkle.twinkle_delete", function(){
        var $this = $(this), $deleteble = $this.data("deleteble"), $form = $(".twinkle_form");
        if(confirm("この項目を削除しますか?\n\r確定すれば画面上から消えて仮削除となります、そのまま更新すると本削除となります。")) {
            $form.append('<input type="hidden" name="twinkle_delete[' + $deleteble + ']" value="' + $deleteble + '">');
            $this.parent().parent().remove();
        }
    });
});