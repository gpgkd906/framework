<?php

namespace Framework\ViewModel;

use Framework\Core\ViewModel\AbstractViewModel;

class ListViewModel extends AbstractViewModel
{
    private $datas = [];
    
    private $items = [];

    private $useItemViewModel = true;
    
    private $itemViewModel = "Framework\ViewModel\ItemViewModel";
    private $template = null;
    
    private function renderAsHtml()
    {
        if($this->useItemViewModel) {
            $itemViewModel = $this->itemViewModel;
            $this->views[] = $itemViewModel::getOpenTag();
            foreach($this->datas as $data) {
                $this->views[] = new itemViewModel($data);
            }
            $this->views[] = $itemViewModel::getCloseTag();
            echo $this->getTemplate();
        } else {
            require $this->getTemplate();            
        }
    }
   
}
