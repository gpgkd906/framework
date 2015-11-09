<?php

namespace Framework\Model\Model\AssiociateHelper;

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
    const TARGET_RECORD = 'targetRecord';
    const TARGET_RECORD_CLASS = 'targetRecordClass';
    const REFERENCED_COLUMN_NAME = 'referencedColumnName';
    const REFERENCED_COLUMN_VALUE = 'referencedColumnValue';
    const REFERENCED_TABLE = 'referencedTable';
    const ASSIOCIATE_LIST = 'assiociateList';
    const ASSIOCIATE_RECORD = 'assiociateRecord';
    const ASSIOCIATE_RECORD_CLASS = 'assiociateRecordClass';
    const ASSIOCIATE_QUERY = 'assiociateQuery';    
}
