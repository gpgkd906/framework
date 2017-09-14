<?php
declare(strict_types=1);
namespace Framework\ViewModel;

use Framework\ObjectManager\ObjectManager;
use Framework\Config\ConfigModel;

$config = ConfigModel::getConfigModel(["scope" => ConfigModel::SUPER]);

ViewModelManager::setBasePath($config->get('ApplicationHost'));
ViewModelManager::setObjectManager(ObjectManager::getSingleton());
