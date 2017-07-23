<?php
namespace Framework\ViewModel;

interface ContainerInterface {

    public function setItems ($items);

    public function getItems ();

    public function setExportView ($exportView);

    public function getExportView ();
}