$(function() {
    return false;
    $('textarea').tinymce({
        // Location of TinyMCE script
        script_url : window.view.www + "js/tinymce.min.js",
        language : "ja",
        theme: "modern",
        skin: "pepper-grinder",
        fontsize_formats: "8pt 9pt 10pt 11pt 12pt 26pt 36pt",
        plugins: [
            "advlist autolink lists link image charmap print preview anchor",
            "searchreplace visualblocks code emoticons",
            "insertdatetime media table contextmenu paste"
        ],
        toolbar1: "insertfile undo redo | fontsizeselect | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | preview code | media link image | forecolor backcolor emoticons",
    });
});