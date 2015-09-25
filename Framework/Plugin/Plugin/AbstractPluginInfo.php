<?php

namespace Framework\Plugin\Plugin;

abstract class AbstractPluginInfo
{
    protected $name;
    protected $version;
    protected $description;
    protected $author;
    private $identify;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getVersion()
    {
        return $this->version;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    final public function getIdentify()
    {
        if($this->identify === null) {
            $this->identify = get_called_class();
        }
        return $this->identify;
    }
}
