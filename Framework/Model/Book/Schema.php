<?php

namespace Framework\Model\Book;

use Framework\Core\Model\AbstractSchema;

class Schema extends AbstractSchema
{
    protected $name = "m_book";

    protected $columns = [
        "bookId" => "book_id",
        "title"  => "title",
        "detail" => "detail",
        "registerDate" => "register_dt",
        "updateDate" => "update_dt"
    ];

    protected $primaryKey = "book_id";
}
