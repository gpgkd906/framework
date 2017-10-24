<?php
declare(strict_types=1);

namespace Project\Core\Console\Controller;

use Std\Controller\AbstractConsole;
use Std\Repository\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Std\Repository\EntityManagerAwareInterface;
// php app/console doctrine:mapping:convert metadata_format ./src/App/MyBundle/Resources/config/doctrine --from-database --filter="Yourtablename"

class DoctrineController extends AbstractConsole implements EntityManagerAwareInterface
{
    use \Std\Repository\EntityManagerAwareTrait;

    public function index()
    {
        // cut off the noisy argument
        unset($_SERVER['argv'][1]);
        $_SERVER['argv'] = array_values($_SERVER['argv']);
        // migration from doctrine-command
        $EntityManager = $this->getEntityManager();
        ConsoleRunner::run(ConsoleRunner::createHelperSet($EntityManager));
    }

    public function getDescription()
    {
        return 'doctrine-command Alias';
    }

    public function getHelp()
    {
        return <<<HELP
Help
Usage:
    php bin/console.php doctrine [<args>...]

generate-entities, etc.:
    php bin/console.php doctrine orm:convert-mapping --namespace="NAMESPACE\\" --force --from-database annotation ./
    php bin/console.php doctrine orm:generate-entities ./ --generate-annotations=true
HELP;
    }
}
