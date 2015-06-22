<?php
namespace Framework\Service\AwsService;
use Framework\Core\AbstractService;

class AwsService extends AbstractService
{
    
    public function __construct()
    {
        $Config = ConfigModel::getConfigModel();
        var_dump($Config);
    }
}
