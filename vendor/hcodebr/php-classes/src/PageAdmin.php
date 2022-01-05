<?php

namespace Hcode;

class PageAdmin extends Page {

    // a pasta de views será diferente do front end.
    public function __construct($opts = array(), $tpl_dir = "/views/admin/") {

        // aproveitando o construtor da classe pai (Page)
        parent::__construct($opts, $tpl_dir);

        // notar que esta classe apenas muda a pasta de templates para admin
        // olha o poder da herança
    }

}

?>