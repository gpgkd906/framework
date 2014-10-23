<?php

require "mws.class.php";

$mws = new mws;

$mws->set_access_key('AKIAJMGMLFFEAKS2XQ3A');
$mws->set_secret_access_key('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');
$mws->set_application_name('blanc');
$mws->set_application_version('0.1');
$mws->set_merchant_id('A3E093YS1TKFV3');
$mws->set_marketplace_id('A1VC38T7YXB528');

$mws->setQuery("All");

$response = $mws->ListMatchingProducts();

var_dump($response);