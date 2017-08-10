<?php
declare(strict_types=1);

namespace Framework\Model\Book;

use Framework\Model\Model\AbstractRecord;

class Record extends AbstractRecord
{
    static public $config = [
        "Model" => Model::class
    ];
}
