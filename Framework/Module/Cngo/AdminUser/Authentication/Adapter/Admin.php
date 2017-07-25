<?php

namespace Framework\Module\Cngo\AdminUser\Authentication\Adapter;

use Framework\Authentication\Adapter\AbstractAdapter;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\AdminUser\Entity\AdminUsers;
use Zend\Authentication\Result;

class Admin extends AbstractAdapter implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function authenticate()
    {
        $AdminUsers = $this->getAdminUser();
        if ($AdminUsers && $this->getCrypt()->verify($this->password, $AdminUsers->getPassword())) {
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
