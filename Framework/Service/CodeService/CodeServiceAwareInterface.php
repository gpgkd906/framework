<?php
/**
 * PHP version 7
 * File CodeServiceAwareInterface.php
 * 
 * @category Service
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
declare(strict_types=1);

namespace Framework\Service\CodeService;

/**
 * Interface CodeServiceAwareInterface
 * 
 * @category Interface
 * @package  Framework\Service
 * @author   chenhan <gpgkd906@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     https://github.com/gpgkd906/framework
 */
interface CodeServiceAwareInterface
{
    /**
     * Method setCodeService
     *
     * @param CodeService $CodeService CodeService
     * 
     * @return mixed
     */
    public function setCodeService(CodeService $CodeService);

    /**
     * Method getCodeService
     *
     * @return CodeService $CodeService
     */
    public function getCodeService();
}
