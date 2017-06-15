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
        $Session = $this->getObjectManager()->getSessionService();
        $body = $Session->getSection('amazon');
        if (!$body) {
            $Client = new Client();
            $Client->setUri('https://realestate.yahoo.co.jp/new/mansion/areasearch/281/');
            $Client->setOptions(array(
                'maxredirects' => 0,
                'timeout'      => 30
            ));
            $response = $Client->send();
            $body = $response->getBody();
            $Session->setSection('amazon', $body);
        }
        $Query = new Query();
        $Query->setDocument($body);
        $result = $Query->execute('.s-result-item');
        var_dump(count($result));
        die;
        foreach ($result as $node) {
            var_dump("node");
        }
        die;
    }
}
