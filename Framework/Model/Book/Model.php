<?php

namespace Framework\Model\Book;

use Framework\Core\Model\AbstractModel;

class Model extends AbstractModel
{
    public $config = [
        "Scheme" => "Framework\Model\Book\Scheme",
        "Record" => "Framework\Model\Book\Record"
    ];
}