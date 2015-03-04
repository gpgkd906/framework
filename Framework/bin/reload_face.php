<?php

require "function.php";

$account_model = model_core::select_model("account", $config["model_dir"], $config["DSN"]);
$profile_model = model_core::select_model("profiles");
$tmp = array();

foreach($account_model->getAll() as $record) {
    if(!empty(trim($record->facebook_id))) {
        $face = "https://graph.facebook.com/" . $record->facebook_id . "/picture?type=large";
        $profile_model->find("account_id", $record->id)->set("face", $face)->update();
    }
}

echo "complete";