<?php
namespace Framework\ViewModel;

class JsonViewModel extends AbstractViewModel
{
    public function render()
    {
        $data = [
            "data" => $this->getData(),
            "childrens" => []
        ];
        foreach ($this->getChilds() as $child) {
            $subData = $child->getData();
            if (!empty($subData)) {
                $data["childrens"][] = $subData;
            }
        }
        return json_encode($data);
    }
}
