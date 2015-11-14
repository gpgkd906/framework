<?php

namespace Framework\Service\EntityService;

use Framework\Service\AbstractService;
use Framework\Config\ConfigModel;
use Framework\Repository\Repository\QueryExecutor;
use Framework\Repository\Repository\SqlBuilder;
use Framework\Repository\Repository\AbstractRepository;
use Framework\Repository\Repository\AbstractEntity;

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
    
    public function getEntityManager()
    {
        //$record = new Users\Entity;
        //var_dump($record);
        $this->testSqlBuilder();
    }
    
    private function testSqlBuilder()
    {
        $sqlBuilder = SqlBuilder::createSqlBuilder();
        $sqlBuilder->select(Tickets\Entity::class)
                   ->from(Users\Entity::class, 'u')
                   ->join([Tickets\Entity::class, 't'], 'userId', 'WITH', 'userId')
                   ->where('u.userId = :userId')
                   ->setParameter([
                       ':userId' => 1
                   ]);
        
        $Ticket = $sqlBuilder->getOneResult();
        $User = $Ticket->getUser();
        foreach($User->getTicket() as $Tt) {
            var_dump($Tt->toArray(), $Ticket->toArray());
        }
    }
}