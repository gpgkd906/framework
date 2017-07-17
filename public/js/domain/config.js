window.view = window.view || {};

view.www = "http://app.my-oyakoukou.net/";

view.access = function($api, $method, $data, $success) {
    if(!$method) {
        $method = "get";
    }
    $option = {
        type: $method,
        success: $success
    }
    $option.dataType = "json";
    if($method.toLowerCase() === "get") {
        $option.dataType = "html";
    }
    if(!!$data) {
        $option.data = $data;
    }
    $.ajax(window.view.www + $api, $option);
}
