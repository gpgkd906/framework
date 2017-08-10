<?php
declare(strict_types=1);
/**
 * AppInterface
 *
 * [:package description]
 *
 * Copyright 2015 Chen Han
 *
 * Licensed under The MIT License
 *
 * @copyright Copyright 2015 Chen Han
 * @link
 * @since
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Framework\Application;

/**
 * AppInterface
 * [:class description]
 *
 * @author 2015 Chen Han
 * @package
 * @link
 */
interface ApplicationInterface
{
    public function getConfig();
    
    public function setConfig($Config);

    public function run();
}
