<?php

namespace Framework\ViewModel\Helper;

use Framework\ObjectManager\SingletonInterface;
use Closure;

class ViewHelper implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    public function makeAttrs($config)
    {
        $attrs = [];
        foreach ($config as $key => $val) {
            if (empty($val)) continue;
            $attrs[] = $key . '="' . str_replace('"', '\'', $val) . '"';
        }
        return join(' ', $attrs);
    }

    public function makeLink($config)
    {
        $config = array_merge([
            'href' => '#',
            'class' => '',
            'html' => '',
        ], $config);
        return '<a href="' . $config['href'] . '" class="' . $config['class'] . '">' . $config['html'] . '</a>';
    }

    public function makeButtonLink($config)
    {
        $config = array_merge([
            'href' => '#',
            'class' => '',
            'icon'  => '',
        ], $config);
        return '<a href="' . $config['href'] . '" class="btn ' . $config['class'] . '"><i class="fa ' . $config['icon'] . '"></i></a>';
    }

    public function makeMessage($config)
    {
        $config = array_merge([
            'msg' => '',
            'type' => 'danger',
        ], $config);
        return '<div class="alert alert-' . $config['type'] . '">' . $config['msg'] . '</div>';
    }
}
