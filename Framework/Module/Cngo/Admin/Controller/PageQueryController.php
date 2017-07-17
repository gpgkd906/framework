<?php

namespace Framework\Module\Cngo\Admin\Controller;

use Framework\Controller\AbstractController;
use Framework\ViewModel\ViewModel\ViewModelManager;
use Zend\Dom\Query;
use Zend\Http\Client;
use Framework\Module\Cngo\Admin\Controller\AbstractAdminController;
use Framework\Service\CacheService\CacheServiceAwareInterface;

class PageQueryController extends AbstractAdminController implements CacheServiceAwareInterface
{
    use \Framework\Service\CacheService\CacheServiceAwareTrait;

    public function index()
    {
        $Cache = $this->getCacheService()->getCache('admin');
        $body = $Cache->getItem('amazon');
        if (!$body) {
            $Client = new Client();
            $Client->setUri('https://realestate.yahoo.co.jp/new/mansion/areasearch/281/');
            $Client->setOptions(array(
                'maxredirects' => 0,
                'timeout'      => 30
            ));
            $response = $Client->send();
            $body = $response->getBody();
            $Cache->setItem('amazon', $body);
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
