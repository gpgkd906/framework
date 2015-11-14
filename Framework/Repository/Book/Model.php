<?php

namespace Framework\Model\Book;

use Framework\Model\Model\AbstractModel;

class Model extends AbstractModel
{
    public $config = [
        "Schema" => Schema::class,
        "Record" => Record::class
    ];
}
