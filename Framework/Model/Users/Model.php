<?php

namespace Framework\Model\Users;

use Framework\Model\Model\AbstractModel;

class Model extends AbstractModel
{
    public $config = [
        "Schema" => Schema::class,
        "Record" => Record::class
    ];
}
