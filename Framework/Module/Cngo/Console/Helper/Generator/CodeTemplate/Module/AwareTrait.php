<?php
declare(strict_types=1);

namespace Framework{Namespace};
use Framework\ObjectManager\ObjectManager;

trait {Module}AwareTrait
{
    private static ${Module};

    public function set{Module}({Module}Interface ${Module})
    {
        self::${Module} = ${Module};
    }

    public function get{Module}()
    {
        if (!self::${Module}) {
            $this->set{Module}(ObjectManager::getSingleton()->get({Module}Interface::class, {Module}::class));
        }
        return self::${Module};
    }
}
