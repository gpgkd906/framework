<?php

$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0 FirePHP/0.7.2";
//$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0";
//$ua = "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR ";
//var_dump(strlen($ua));

$firefox = "/Mozilla\/5.0 \((.+?) rv:[\S]+?\) Gecko\/([\S]+) Firefox\/([\S]+)/";
$result = preg_match($firefox, $ua, $match);
var_dump($result, $match);
die;
$end = strpos($ua, " ", $start);
if($end) {
    $sub = substr($ua, $start, $end - $start);
} else {
    $sub = substr($ua, $start);
}
var_Dump($start, $end, $sub);
