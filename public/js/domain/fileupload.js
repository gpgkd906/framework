$(function(){
    // Initialize the jQuery File Upload widget:
    $('#scaffold').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        autoUpload: true,
        url: window.view.www + "api/file"
    });
});

