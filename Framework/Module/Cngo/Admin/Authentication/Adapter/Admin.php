<?php

namespace Framework\Module\Cngo\Admin\Authentication\Adapter;

use Framework\Authentication\Adapter\Common;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\Admin\Entity\AdminUsers;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;

class Admin extends Common implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function authenticate()
    {
        $AdminUsers = $this->getAdminUser();
        $crypt = new Bcrypt();
        if ($AdminUsers && $crypt->verify($this->password, $AdminUsers->getPassword())) {
            return new Result(Result::SUCCESS, $AdminUsers->toArray(), ['Authenticated successfully.']);
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Invalid credentials.']);
        }
    }

    public function getAdminUser()
    {
        $AdminUsersRepository = $this->getEntityManager()->getRepository(AdminUsers::class);
        $AdminUsers = $AdminUsersRepository->findOneBy([
            'login' => $this->username,
            'deleteFlag' => 0
        ]);
        return $AdminUsers;
    }
}
