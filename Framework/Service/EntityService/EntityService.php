<?php

namespace Framework\Service\EntityService;

use Framework\Service\AbstractService;
use Framework\Config\ConfigModel;
use Framework\Repository;
use Framework\Repository\Repository\RepositoryInterface;
use Framework\Repository\Repository\EntityInterface;
use Framework\Repository\Repository\AbstractRepository;
use Framework\Repository\Repository\AbstractEntity;
use Framework\Repository\Repository\QueryExecutor;
use Framework\Repository\Repository\SqlBuilder;
use Exception;

use Framework\Repository\Users;
use Framework\Repository\Tickets;

class EntityService extends AbstractService
{
    public function __construct()
    {
        $config = ConfigModel::getConfigModel([
            'scope' => ConfigModel::MODEL
        ]);
        QueryExecutor::setConfig($config->getConfig('connection'));
        AbstractEntity::setConfig($config->getConfig('Entity'));
    }

    public function getRepository($repository)
    {
        if(!is_subclass_of($repository, RepositoryInterface::class)) {
            $temp_repository = Repository::class . '\\' . $repository . '\\Repository';
            if(is_subclass_of($temp_repository, RepositoryInterface::class)) {
                $repository = $temp_repository;
            } else {
                throw new Exception(sprintf('invalid Repository [%s]', $repository));
            }
        }
        return $repository::getSingleton();
    }
    
    private function testSqlBuilder()
    {
        $sqlBuilder = SqlBuilder::createSqlBuilder();
        $sqlBuilder->select('u.userId, t.ticketId')
                   ->from(Users\Entity::class, 'u')
                   ->join([Tickets\Entity::class, 't'], 'userId', 'WITH', 't.userId=u.userId')
                   ->where('u.userId = :userId')
                   ->groupBy('u.userId')
                   ->having('MAX(t.ticketId) < 2')
                   ->setParameters([
                       ':userId' => 1
                   ]);
        $Ticket = $sqlBuilder->getOneResult();
        var_dump($Ticket);
    }
}