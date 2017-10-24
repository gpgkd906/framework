<?php
declare(strict_types=1);
namespace Std\ViewModel;

use Framework\ObjectManager\ObjectManager;

ObjectManager::getSingleton()
    ->get(ViewModelManagerInterface::class)
    ->setRenderer(ObjectManager::getSingleton()->create(null, Renderer::class));
