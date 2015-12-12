<?php

namespace Framework\ViewModel\Admin\Cms\Page;

use Framework\ViewModel\ViewModel\AbstractViewModel;
use Framework\ViewModel\Admin\Component\TableViewModel;
use Framework\Model\Cms\PageModel;

class ListViewModel extends AbstractViewModel
{    
    protected $template = '/template/admin/cms/page/list.html';

    protected $config = [
        'model' => PageModel::class,
        'container' => [
            'TableTree' => [
                [
                    'viewModel' => TableViewModel::class,
                    'id' => 'TableTree',
                    'head' => [
                        'パス' => 'file',
                        '場所' => 'dir',
                        'アクション' => 'action',
                    ]
                ],
            ]
        ],
        'script' => ['/js/table_collapse.js'],
    ];
    
    public function getEntities()
    {
        $list = parent::getEntities();
        $tmplist = [];
        foreach($list as $row) {
            $dir = $row['dir'];
            if(!isset($tmplist[$dir])) {
                $tmplist[$dir] = [];
            }
            $tmplist[$dir][] = $this->formatRow($row);
        }
        ksort($tmplist);
        $sections = array_shift($tmplist);
        $entities = [];
        foreach($sections as $idx => $section) {
            //format section
            $section = $this->formatSection($section);
            $sections[$idx] = $section;
            //format entity
            $sectionName = $section['key'];
            $entity = [];
            foreach($tmplist as $dir => $dirList) {
                if(strpos($dir, $sectionName) === 0) {
                    $entity = array_merge($entity, $dirList);
                    unset($tmplist[$dir]);
                }
            }
            usort($entity, function($a, $b) {
                return strcmp($a['file'], $b['file']);
            });
            $entity = array_map(function($row) {
                $row['file'] = str_repeat('　', $row['depth']) . $row['file'];
                return $row;
            }, $entity);
            $entities[$sectionName] = $entity;
        }
        $tableViewModel = $this->getChild('TableTree');
        $tableViewModel->setSection($sections);
        return $entities;
    }

    private function formatSection($section)
    {
        $section['id'] = $section['nameHash'];
        $section['title'] = $section['file'];
        $section['key']  = $section['fullPath'];
        $section['action'] = $this->getViewHelper()->makeLink([
            'href' => '/admin/cms/page/register/?pid=' . $section['nameHash'],
            'class' => 'btn btn-success btn-lg btn-block',
            'html' => 'セクション[' . $section['title'] . ']でページを作成',
        ]);
        return $section;
    }

    private function formatRow($row)
    {
        $row['DirFlag'] = $row['fileSize'] < 0 ? 'フォルダー' : 'ファイル';
        $row['attrs'] = $this->getViewHelper()->makeAttrs([
            'data-toggle' => 'table-collapse',
            'data-target' => $row['fileHash'],
            'data-reference' => $row['dirHash'],
            'data-depth' => $row['depth'],
        ]);
        if($row['fileSize'] < 0) {
            $row['action'] = $this->getViewHelper()->makeButtonLink([
                'href' => '/admin/cms/page/register/?pid=' . $row['nameHash'],
                'class' => 'btn-primary btn-circle',
                'icon' => 'fa-pencil',
            ]);
        } else {
            $row['action'] = join('', [
                $this->getViewHelper()->makeButtonLink([
                    'href' => '/admin/cms/page/edit/?pid=' . $row['nameHash'],
                    'class' => 'btn-primary btn-circle',
                    'icon' => 'fa-edit',
                ]),
                $this->getViewHelper()->makeButtonLink([
                    'href' => '/admin/cms/page/copy/?pid=' . $row['nameHash'],
                    'class' => 'btn-success btn-circle',
                    'icon' => 'fa-paste',
                ]),
                $this->getViewHelper()->makeButtonLink([
                    'href' => '/admin/cms/page/show/?pid=' . $row['nameHash'],
                    'class' => 'btn-info btn-circle',
                    'icon' => 'fa-info',
                ]),
                $this->getViewHelper()->makeButtonLink([
                    'href' => '/admin/cms/page/delete/?pid=' . $row['nameHash'],
                    'class' => 'btn-danger btn-circle',
                    'icon' => 'fa-times',
                ]),
            ]);
        }
        return $row;
    }
}