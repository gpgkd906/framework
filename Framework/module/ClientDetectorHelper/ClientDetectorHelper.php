<?php
require "ClientDetectorHelperInterface.php";

class ClientDetectorHelper extends ClientDetectorHelperInterface
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
        "name" => null,
        "version" => null,
        "isDetected" => false,
    ];

    private $pattern = [
        "Firefox" => "/Mozilla\/[\S]+ \((.+?) rv:[\S]+?\) Gecko\/([\S]+) Firefox\/([\S]+)/",
        "Safari" => "",
        "Chrome" => "",
        "Opera" => "",
        "IE" => "Mozilla/4.0 (compatible;)",
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
    
    /**
     * まずapacheと仮定した上で取得する
     * 取得できない場合は、nginxと仮定して取得する
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

    private function setUserAgent($userAgent)
    {
        return $this->userAgent = $userAgent;
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
            switch(true) {
                //FirefoxのUAが綺麗ので先に取る
            case strpos($userAgent, "Firefox") :
                $this->browserInfo["name"] = "Firefox";
                $start = strpos($userAgent, "Firefox");
                $end = strpos($userAgent, " ", $start);
                if($end) {
                    $sub = substr($ua, $start, $end - $start);
                } else {
                    $sub = substr($ua, $start);
                }
                $subs = explode("/", $sub);
                $this->browserInfo["version"] = floatval($sub[1]);
                break;
                //次はSafari/Mobile Safari
            case strpos($userAgent, "Safari") && strpos($userAgent, "Chrome") === false :
                if(strpos($userAgent, "Mobile")) {
                    $this->browserInfo["name"] = "Mobile Safari";
                } else {
                    $this->browserInfo["name"] = "Safari";
                }
                
                break;
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
            $userAgent = $this->getUserAgent();

            
            $this->osInfo["isDetected"] = true;
        }
        return $this->osInfo;
    }
}
