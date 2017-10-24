<?php
/**
 * PHP version 7
 * File CodeServiceAwareTrait.php
 *
 * @category Service
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Std\CodeService;

/**
 * Trait CodeServiceAwareTrait
 *
 * @category Trait
 * @package  Std
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
trait CodeServiceAwareTrait
{
    private static $_CodeService;

    /**
     * Method setCodeService
     *
     * @param CodeService $CodeService CodeService
     *
     * @return mixed
     */
    public function setCodeService(CodeService $CodeService)
    {
        self::$_CodeService = $CodeService;
    }

    /**
     * Method getCodeService
     *
     * @return CodeService $CodeService
     */
    public function getCodeService()
    {
        return self::$_CodeService;
    }
}
