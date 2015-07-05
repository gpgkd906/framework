<?php

require "./form2/vendor/autoload.php";
require "TemplateService.php";
require "Collection.php";

$formManager = new Form2\FormManager;

$Template = new TemplateService;

$Collection = new Collection;

$form = $formManager->create("templateService");

$form->append("file", "design");

$form->submit(function($data) use ($form, $Template, $Collection) {
    $file = "./Parser/test.html";
    $content = file_get_contents($file);
    $data = $Template->parse($content);
    $top = $data[0];
    $Collection->addTemplateElement($top);
});


echo $form->start();
$Collection->each(function($id, $set){
    print_r($set);die;
});

echo $form->submit;
echo $form->end();
