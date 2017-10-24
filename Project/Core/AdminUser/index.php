<?php

namespace Project\Core\AdminUser;

use Framework\ObjectManager\ObjectManager;
use Std\Repository\RepositoryManager;
use Project\Core\AdminUser\Authentication\Authentication;

$ObjectManager = ObjectManager::getSingleton();
$ObjectManager->get(RepositoryManager::class)->addEntityPath(__DIR__ . '/Entity');

$ObjectManager->get(Authentication::class)->initListener();
