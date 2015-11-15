<?php

namespace Framework\Controller;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;

class IndexController extends AbstractController
{
    
    public function index()
    {
        /* $book = App::getModel("book"); */
        /* var_dump($book->getAll()); */
        

        $viewModel = ViewModelManager::getViewModel([
            "viewModel" => "IndexViewModel",
            "Model" => "Book"
        ]);
        return $viewModel;
    }

}
