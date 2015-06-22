<?php

namespace Framework\Model\Book;

use Framework\Core\Model\AbstractModel;

class Model extends AbstractModel
{
    public $config = [
        "Schema" => Schema::class,
        "Record" => Record::class
    ];
}
