<?php

namespace Framework\Controller;

use Framework\Core\AbstractController;
use Framework\Core\App;
use Framework\Core\ViewModel\ViewModelManager;

class IndexController extends AbstractController
{
    
    public function index()
    {
        /* $book = App::getModel("book"); */
        /* var_dump($book->getAll()); */
        

        return ViewModelManager::getViewModel([
            "viewModel" => "IndexViewModel",
            "Model" => "Book"
        ]);
    }

}
