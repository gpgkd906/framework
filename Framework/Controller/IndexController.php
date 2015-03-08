<?php

namespace Framework\Controller;

use Framework\Core\AbstractController;
use Framework\Core\App;

class IndexController extends AbstractController
{
    
    public function index()
    {
        $book = App::getModel("book");
        var_dump($book->getAll());
        
    }

}
