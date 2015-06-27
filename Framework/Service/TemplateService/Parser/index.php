<?php
require "Parser.php";

$html = file_get_contents("test.html");

$parser = new Parser;

$data = $parser->parse($html);

print_r($data);
