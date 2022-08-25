<?php

namespace App\Class;

use App\Class\Page;

class PageAdmin extends Page{

    public function __construct($options = array(), $tpl_dir = "/App/views/admin/")
    {
        parent::__construct($options, $tpl_dir);
    }

}