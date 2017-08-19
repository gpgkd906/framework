<?php
/**
 * PHP version 7
 * File CodeService.php
 * 
 * @category Service
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Service\CodeService;

use Framework\Service\AbstractService;
use CodeService\Code\Analytic;
use CodeService\Code\Wrapper\AstWrapper;
use Framework\Config\ConfigModel;

/**
 * Class CodeService
 * 
 * @category Class
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
class CodeService extends AbstractService
{
    /**
     * Method analysis
     *
     * @param string $file    filePath
     * @param mixed  $version PhpVersion
     * 
     * @return AstWrapper
     */
    public function analysis($file, $version = null)
    {
        return Analytic::analytic($file, $version);
    }

    /**
     * Method analysisCode
     *
     * @param string $code    PhpCode
     * @param mixed  $version PhpVersion
     * 
     * @return AstWrapper
     */
    public function analysisCode($code, $version = null)
    {
        return Analytic::analyticCode($code, $version);
    }

    /**
     * Method createCode
     *
     * @param string|null $namespace Namespace
     * @param string|null $class     Class
     * 
     * @return AstWrapper
     */
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
