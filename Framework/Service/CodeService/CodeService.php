<?php
declare(strict_types=1);

namespace Framework\Service\CodeService;

use Framework\Service\AbstractService;
use CodeService\Code\Analytic;
use CodeService\Code\Wrapper\AstWrapper;
use Framework\Config\ConfigModel;

class CodeService extends AbstractService
{
    public function analysis($file, $version = null)
    {
        return Analytic::analytic($file, $version);
    }

    public function analysisCode($code, $version = null)
    {
        return Analytic::analyticCode($code, $version);
    }

    public function createCode($namespace = null, $class = null)
    {
        $codeBase = [
            '<?php
declare(strict_types=1);', PHP_EOL, PHP_EOL
        ];
        if ($namespace) {
            $codeBase[] = 'namespace ';
            $codeBase[] = $namespace . ';';
            $codeBase[] = PHP_EOL;
        }
        if ($class) {
            $codeBase[] = 'class ';
            $codeBase[] = $class . ' {}';
            $codeBase[] = PHP_EOL;
        }
        return Analytic::analyticCode(join('', $codeBase));
    }
}
