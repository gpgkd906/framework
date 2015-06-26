<?php
require "Parser.php";

$html = file_get_contents("test.html");

$parser = new Parser;

$data = $parser->parseContent($html);

var_dump($data);
