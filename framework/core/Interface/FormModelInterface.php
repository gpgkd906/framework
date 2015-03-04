<?php
namespace Framework\Core\Interface;

interface FormModelInterface {

    public function setFieldset();

    public function getFieldset();

    public function setModel();

    public function getModel();
    
    public function adapter();

    public function getData();

    public function setData();
}
