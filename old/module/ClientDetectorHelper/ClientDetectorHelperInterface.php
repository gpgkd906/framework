<?php
/**
 * Ecsuitesデバイス判定機能Interface
 * ファイルパス: \module\Core\KdlView\src\KdlView\Helper\Front\ClientDetectorHelper.php
 */

/**
*serviceにする
*isTabletを追加
*/
interface ClientDetectorHelperInterface
{
    /**
     * @return boolean true/false 
     */
    public function isPc();

    /**
     * @return boolean true/false 
     */
    public function isSp();

    /**
     * @return boolean true/false 
     */
    public function isMb();

    /**
     * @return boolean true/false 
     */
    public function isTablet();

    /**
     * @return boolean true/false 
     */
    public function isIos();

    /**
     * @return boolean true/false 
     */
    public function isIphone();

    /**
     * @return boolean true/false 
     */
    public function isIpad();

    /**
     * @return boolean true/false 
     */
    public function isAndroid();

    /**
     * @return boolean true/false 
     */
    public function isAndroidPhone();

    /**
     * @return boolean true/false 
     */
    public function isAndroidTablet();

    /**
     * @return boolean true/false 
     */
    public function isWindowsPhone();

    /**
     * @return string "IE", "Chrome", "Safari", "Firefox", "Opera"
     */
    public function getBrowserName();

    /**
     * @return float 
     */
    public function getBrowserVersion();

    /**
     * @return string user_agent_string
     */
    public function getUserAgent();
}
