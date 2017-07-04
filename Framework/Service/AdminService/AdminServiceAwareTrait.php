<?php

namespace Framework\Service\AdminService;

trait AdminServiceAwareTrait
{
    private $AdminService;

    public function setAdminService(AdminService $AdminService)
    {
        $this->AdminService = $AdminService;
    }

    public function getAdminService()
    {
        return $this->AdminService;
    }
}
