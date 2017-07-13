<?php

namespace Framework\Module\Cngo\Admin;

use Framework\ObjectManager\ObjectManager;
use Framework\Repository\RepositoryManager;

ObjectManager::getSingleton()
->get(RepositoryManager::class)->addEntityPath(__DIR__ . '/Entity');
