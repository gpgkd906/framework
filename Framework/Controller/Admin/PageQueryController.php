<?php

namespace Framework\Controller\Admin;

use Framework\Controller\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Zend\Dom\Query;
use Zend\Http\Client;

class PageQueryController extends AbstractController
{
    
    public function index()
    {
        $Session = $this->getServiceManager()->getSessionService();
        $body = $Session->getSection('amazon');
        if(!$body) {
            $Client = new Client();
            $Client->setUri('https://www.amazon.co.jp/s/ref=s9_acsd_al_bw_hr_PCFRNOTE_4_ot?__mk_ja_JP=%83%4A%83%5E%83%4A%83%69&node=2188762051,!2141376051,!2141350051,2127209051,!2141350051,!2141376051&search-alias=computers&field-feature_twelve_browse-bin=2456755051&bbn=2188762051&pf_rd_m=AN1VRQENFRJN5&pf_rd_s=merchandised-search-3&pf_rd_r=V11QRE7KKQXH4CVE569Z&pf_rd_t=101&pf_rd_p=238403089&pf_rd_i=2151981051');
            $Client->setOptions(array(
                'maxredirects' => 0,
                'timeout'      => 30
            ));
            $response = $Client->send();
            $body = $response->getBody();
            $Session->setSection('amazon', $body);
        }
        $Query = new Query();
        $body = file_get_contents('/Users/gpgkd906/dev/framework/amazon.txt');
        $Query->setDocument($body);
        $result = $Query->execute('.s-result-item');
        var_dump(count($result));die;
        foreach($result as $node) {
            var_dump("node");
        }
        die;
    }
}
