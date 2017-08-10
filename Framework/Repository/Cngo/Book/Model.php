<?php
declare(strict_types=1);

namespace Framework\Model\Book;

use Framework\Model\Model\AbstractModel;

class Model extends AbstractModel
{
    public $config = [
        "Schema" => Schema::class,
        "Record" => Record::class
    ];
}
