<?php

namespace Framework\Service\AdminService;

interface AdminServiceAwareInterface
{
    public function setAdminService(AdminService $AdminService);
    public function getAdminService();
}
