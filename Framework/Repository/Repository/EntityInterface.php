<?php

namespace Framework\Repository\Repository;

interface EntityInterface
{
    const INSERT = 'Insert';
    const UPDATE = 'Update';
    const DELETE = 'Delete';
    const SELECT = 'Selete';
    //
    const TABLE        = 'Table';
    const COLUMN       = 'Column';
    const ID           = 'Id';
    const PRIMARY_KEY  = 'PrimaryKey';
    const ENTITY       = 'Entity';
    const PROPERTY     = 'Property';
    const QUERY        = 'Query';
    const NAME         = 'name';
    const PROPERTY_MAP = 'propertyMap';
    const COLUMN_MAP   = 'columnMap';
    const MODEL_CLASS  = 'modelClass';
    const PRIMARY_PROPERTY = 'primaryProperty';
    const SETTER       = 'setter';
}