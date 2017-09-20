<?php
declare(strict_types=1);

namespace Framework\Module\Cngo\AdminUser\Authentication\Adapter;

use Framework\Authentication\Adapter\AbstractAdapter;
use Framework\Repository\EntityManagerAwareInterface;
use Framework\Module\Cngo\AdminUser\Entity\Users;
use Zend\Authentication\Result;

class Admin extends AbstractAdapter implements EntityManagerAwareInterface
{
    use \Framework\Repository\EntityManagerAwareTrait;

    public function authenticate()
    {
        $Users = $this->getAdminUser();
        if ($Users && $this->getCrypt()->verify($this->password, $Users->getPassword())) {
            return new Result(Result::SUCCESS, $Users->toArray(), ['Authenticated successfully.']);
        } else {
            return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Invalid credentials.']);
        }
    }

    public function getAdminUser()
    {
        $UsersRepository = $this->getEntityManager()->getRepository(Users::class);
        $Users = $UsersRepository->findOneBy([
            'login' => $this->username,
            'deleteFlag' => 0
        ]);
        return $Users;
    }
}
