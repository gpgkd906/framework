<?php
declare(strict_types=1);

namespace Framework\Repository\Repository\AssiociateHelper;

interface AssiociateHelperInterface
{
    const NAME         = 'name';    
    const ASSIOCIATE    = 'Assiociate';
    const ONE_TO_ONE    = 'OneToOne';
    const ONE_TO_MANY   = 'OneToMany';
    const MANY_TO_ONE   = 'ManyToOne';
    const FETCH         = 'fetch';
    const LAZY          = 'LAZY';
    const JOIN_COLUMN   = 'JoinColumn';
    const TARGET_ENTITY = 'targetEntity';
    const TARGET_ENTITY_CLASS = 'targetEntityClass';
    const REFERENCED_COLUMN_NAME = 'referencedColumnName';
    const REFERENCED_COLUMN_VALUE = 'referencedColumnValue';
    const REFERENCED_TABLE = 'referencedTable';
    const ASSIOCIATE_LIST = 'assiociateList';
    const ASSIOCIATE_ENTITY = 'assiociateEntity';
    const ASSIOCIATE_ENTITY_CLASS = 'assiociateEntityClass';
    const ASSIOCIATE_QUERY = 'assiociateQuery';    
}
