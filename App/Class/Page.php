<?php

namespace App\Class;

use Rain\Tpl;

class Page{

    private $tpl;
    private $options = array();
    private $defaults = array(
        "header"=>true,
        "footer"=>true,
        "data"=>array()
    );

    public function __construct($options = array(), $tpl_dir = "/App/views/")
    {
        $this->options = array_merge($this->defaults, $options);

        $config = array(
            "tpl_dir"    =>  $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
            "cache_dir"  =>  $_SERVER["DOCUMENT_ROOT"]."/App/views-cache/",
            "debug"      =>  false
        );

        Tpl::configure($config);

        $this->tpl = new Tpl;

        $this->setData($this->options);

        if($this->options['header'] === true){
            if(isset($this->options['header-data']))
                $this->setTpl("template".DIRECTORY_SEPARATOR."header", $this->options['header-data']);
            else
                $this->setTpl("template".DIRECTORY_SEPARATOR."header");
        }
    }

    private function setData($data = array())
    {
        foreach($data as $key => $value){
            $this->tpl->assign($key, $value);
        }
    }

    public function setTpl($name, $data = array(), $returnHTML = false)
    {
        $this->setData($data);

        return $this->tpl->draw($name, $returnHTML);
    }

    public function __destruct()
    {
        if($this->options['footer'] === true) $this->tpl->draw("template".DIRECTORY_SEPARATOR."footer");
    }
}