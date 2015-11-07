<?php
require "ClientDetectorHelperInterface.php";

class ClientDetectorHelper implements ClientDetectorHelperInterface
{
    /**
     *
     * @api
     * @var mixed $userAgent
     * @access public
     * @link
     */
    private $userAgent = null;

    /**
     *
     * @api
     * @var mixed $clientInfo 
     * @access private
     * @link
     */
    private $clientInfo = [
        "isPersonalComputer" => false,
        "isSmartphone" => false,
        "isTablet" => false,
        "isIos" => false,
        "isAndroid" => false,
        "isWindows" => false,
        "isDetected" => false,
    ];
    
    /**
     *
     * @api
     * @var mixed $browserInfo 
     * @access private
     * @link
     */
    private $browserInfo = [
        "name" => null,
        "version" => null,
        "isDetected" => false,
    ];

    /**
     *
     * @api
     * @var mixed $osInfo 
     * @access private
     * @link
     */
    private $osInfo = [
        "platform" => null,
        "name" => null,
        "version" => null,
        "isDetected" => false,
    ];

    private $browserPatterns = [
        "Firefox" => "/^Mozilla\/5.0 \((?<platform>.+?) rv:[\S]+?\) Gecko\/[\S]+ (?<name>Firefox)\/(?<version>[\S]+)/",
        "Safari" => "/^Mozilla\/\S+ \((?<platform>.+?)\) AppleWebKit\/\S+ \(KHTML[^)]+\) Version\/(?<version>\S+) (?<name>Safari)/",
        "SafariMobile" => "/^Mozilla\/\S+ \((?<platform>.+?)\) AppleWebKit\/\S+ \(KHTML[^)]+\) Version\/(?<version>\S+).+?(?<mobile>Mobile).+?(?<name>Safari)/",
        "Opera1" => "/^(?<name>Opera)[^(]+?\(([^)]+)\).+?Version\/(?<version>[\d.]+)/",
        "Opera2" => "/^Mozilla\/\S+ \((?<platform>.+?)\).+?(?<name>OPR)\/(?<version>[\d.]+)/",
        "IE" => "/^Mozilla\/\S+ \(compatible; MS(?<name>IE) (?<version>[^;]+); (?<platform>[^)]+)/",
        "Chrome" => "/^Mozilla\/\S+ \((?<platform>[^)]+)\) AppleWebKit\/\S+ \(KHTML, like Gecko\).*?(?<name>Chrome)\/(?<version>\S+) (?:Mobile )?Safari/",
    ];

    private $osPatterns = [
        'windows' => '/(?<name>Windows) NT (?<version>[\d.]+)/',
        'mac' => '/Macintosh;.*? Intel (?<name>Mac OS X) (?<version>[\d_]+)/',
        'ios' => '/(?<name>CPU OS) (?<version>[\d_]+) like Mac OS X/',
        'android' => '/(?<name>Android) (?<version>[\d.]+);/',
        'linux' => '/(?<name>Linux) (?<version>[^;]+);/',
    ];

    private $windowVersionTable = [
        
    ];
    
    /**
     * @return boolean true/false 
     */
    public function isPc()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isPersonalComputer"];
    }

    /**
     * @return boolean true/false 
     */
    public function isSp()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isSmartphone"];
    }

    /**
     * @return boolean true/false 
     */
    public function isMb()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isMobile"];
    }

    /**
     * @return boolean true/false 
     */
    public function isTablet()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isTablet"];
    }

    /**
     * @return boolean true/false 
     */
    public function isIos()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isIos"];
    }

    /**
     * @return boolean true/false 
     */
    public function isIphone()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isIos"] && $clientInfo["isSmartphone"];
    }

    /**
     * @return boolean true/false 
     */
    public function isIpad()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isIos"] && $clientInfo["isTablet"];
    }

    /**
     * @return boolean true/false 
     */
    public function isAndroid()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isAndroid"];
    }

    /**
     * @return boolean true/false 
     */
    public function isAndroidPhone()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isAndroid"] && $clientInfo["isSmartphone"];
    }

    /**
     * @return boolean true/false 
     */
    public function isAndroidTablet()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isAndroid"] && $clientInfo["isTablet"];        
    }
    
    /**
     * @return boolean true/false 
     */
    public function isWindowsPhone()
    {
        $clientInfo = $this->getClientInfo();
        return $clientInfo["isWindows"] && $clientInfo["isSmartphone"];        
    }

    /**
     * @return string "IE", "Chrome", "Safari", "Firefox", "Opera"
     */
    public function getBrowserName()
    {
        $browserInfo = $this->getBrowserInfo();
        return $browserInfo["name"];
    }

    /**
     * @return float 
     */
    public function getBrowserVersion()
    {
        $browserInfo = $this->getBrowserInfo();
        return $browserInfo["version"];
    }

    public function getOsName()
    {
        $osInfo = $this->getOsInfo();
        return $osInfo["name"];
    }

    public function getOsVersion()
    {
        $osInfo = $this->getOsInfo();
        return $osInfo["version"];
    }
    
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        $this->clientInfo = [
            "isPersonalComputer" => false,
            "isSmartphone" => false,
            "isTablet" => false,
            "isIos" => false,
            "isAndroid" => false,
            "isWindows" => false,
            "isDetected" => false,
        ];
        $this->browserInfo["isDetected"] = false;
        $this->osInfo["isDetected"] = false;
    }
    /**
     * @return string user_agent_string
     */
    public function getUserAgent()
    {
        if($this->userAgent === null) {
            if(isset($_SERVER["HTTP_USER_AGENT"])) {
                $this->userAgent = $_SERVER["HTTP_USER_AGENT"];
            }
        }
        return $this->userAgent;
    }

    /**
     * 
     * @api
     * @param mixed $clientInfo
     * @return mixed $clientInfo
     * @link
     */
    private function setClientInfo ($clientInfo)
    {
        return $this->clientInfo = $clientInfo;
    }

    /**
     * 
     * @api
     * @return mixed $clientInfo
     * @link
     */
    private function getClientInfo ()
    {
        if($this->clientInfo["isDetected"] === false) {
            $userAgent = $this->getUserAgent();
            switch(true) {
            case strpos($userAgent, "iPad") !== false:
                $this->clientInfo["isTablet"] = true;
                $this->clientInfo["isIos"] = true;
                break;

            case strpos($userAgent, "iPod") !== false:
            case strpos($userAgent, "iPhone") !== false:
                $this->clientInfo["isSmartphone"] = true;
                $this->clientInfo["isIos"] = true;
                break;

            case strpos($userAgent, "Android") !== false:
                $this->clientInfo["isAndroid"] = true;
                if(strpos($userAgent, "Mobile") !== false) {
                    $this->clientInfo["isSmartphone"] = true;
                } else {
                    $this->clientInfo["isTablet"] = true;
                }
                break;
                
            case strpos($userAgent, "Windows") !== false:
                $this->clientInfo["isWindows"] = true;
                if(strpos($userAgent, "Phone") !== false) {
                    $this->clientInfo["isSmartphone"] = true;
                } else {
                    $this->clientInfo["isPersonalComputer"] = true;
                }
                break;
                
            case strpos($userAgent, "BlackBerry") !== false:
                $this->clientInfo["isSmartphone"] = true;
                break;
                
            case preg_match("/^(DoCoMo|KDDI|SoftBank|UP\.Browser|J-PHONE|Vodafone|MOT-)/", $userAgent) :
            case preg_match("/^Mozilla\/3.0\(WILLCOM/", $userAgent) :
            case preg_match("/^Mozilla\/3.0\(DDIPOCKET/", $userAgent) :
            case preg_match("/^(emobile|Huawei|IAC\/1.0 \(H31IA;)/", $userAgent) :
                $this->clientInfo["isMobile"] = true;
                break;
                
            default:
                $this->clientInfo["isPersonalComputer"] = true;                
                break;
            }
            $this->clientInfo["isDetected"] = true;
        }
        return $this->clientInfo;
    }
    
    /**
     * 
     * @api
     * @param mixed $browserInfo
     * @return mixed $browserInfo
     * @link
     */
    private function setBrowserInfo ($browserInfo)
    {
        return $this->browserInfo = $browserInfo;
    }
    
    /**
     * 
     * @api
     * @return mixed $browserInfo
     * @link
     */
    private function getBrowserInfo ()
    {
        if($this->browserInfo["isDetected"] === false) {
            $userAgent = $this->getUserAgent();
            $patterns = $this->getBrowserPatterns();
            foreach($patterns as $pattern) {
                $info = null;
                if(preg_match($pattern, $userAgent, $info)) {
                    //new Opera has name as OPR, wo have to fix it
                    if($info["name"] === "OPR") {
                        $info["name"] = "Opera";
                    }
                    $this->browserInfo["name"] = $info["name"];
                    $this->browserInfo["version"] = $info["version"];
                    $this->browserInfo["platform"] = $info["platform"];
                }
            }
            $this->browserInfo["isDetected"] = true;
        }
        return $this->browserInfo;
    }

    /**
     * 
     * @api
     * @param mixed $osInfo
     * @return mixed $osInfo
     * @link
     */
    private function setOsInfo ($osInfo)
    {
        return $this->osInfo = $osInfo;
    }

    /**
     * 
     * @api
     * @return mixed $osInfo
     * @link
     */
    private function getOsInfo ()
    {
        if($this->osInfo["isDetected"] === false) {
            $browserInfo = $this->getBrowserInfo();
            $platform = $browserInfo["platform"];
            $patterns = $this->getOsPatterns();
            foreach($patterns as $pattern) {
                $info = null;
                if(preg_match($pattern, $platform, $info)) {
                    if($info["name"] === 'CPU OS') {
                        $info["name"] = 'Ios';
                    }
                    if($info["name"] === 'Windows') {
                        if(isset($this->windowVersionTable[$info['version']])) {
                            $info['version'] = $this->windowVersionTable[$info['version']];
                        }
                    }
                    $this->osInfo["name"] = $info["name"];
                    $this->osInfo["version"] = str_replace('_', '.', $info["version"]);
                }
            }

            $this->osInfo["isDetected"] = true;
        }
        return $this->osInfo;
    }

    private function getBrowserPatterns()
    {
        return $this->browserPatterns;
    }

    private function getOsPatterns()
    {
        return $this->osPatterns;
    }
}
