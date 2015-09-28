<?php

namespace Framework\Model\Users;

use Framework\Model\Model\AbstractSchema;

class Schema extends AbstractSchema
{
    protected $name = "m_users";

    protected $columns = [
        "userId" => "m_user_id",
        "email"  => "email",
        "password" => "password",
    ];

    protected $primaryKey = "m_user_id";
}
