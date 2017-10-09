<?php

namespace Framework\Module\Cngo\AdminUser;

use Framework\ObjectManager\ObjectManager;
use Framework\Repository\RepositoryManager;
use Framework\Module\Cngo\AdminUser\Authentication\Authentication;

$ObjectManager = ObjectManager::getSingleton();
$ObjectManager->get(RepositoryManager::class)->addEntityPath(__DIR__ . '/Entity');

// $ObjectManager->get(Authentication::class)->initListener();
