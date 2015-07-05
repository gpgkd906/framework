<?php

require "TemplateService.php";

$template = new TemplateService;

$data = $template->parse();

file_put_contents("template", var_export($data, true));
