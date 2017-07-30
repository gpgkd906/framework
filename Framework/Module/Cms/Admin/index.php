<?php

namespace Framework\Module\Cms\Admin;

use Framework\ObjectManager\ObjectManager;
use Framework\Repository\RepositoryManager;

$ObjectManager = ObjectManager::getSingleton();
$ObjectManager->get(RepositoryManager::class)->addEntityPath(__DIR__ . '/Entity');
