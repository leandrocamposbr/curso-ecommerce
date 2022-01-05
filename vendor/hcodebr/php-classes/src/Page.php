<?php

// ! classe estendida na PageAdmin

namespace Hcode;

use Rain\Tpl;

// leoc - baseado no 'example-simple.php da pasta do raintpl, conforme aula 103.

class Page {

    // leoc - aula 103 - as variáveis (parâmetros na chamada da classe) virão de acordo com a rota chamada no slim.

    private $tpl;
    private $options = [];
    private $defaults = [
        "data"=>[]
    ];    

    // leoc - __construct é um método mágico.
    // - o segundo parâmetro $tpl_dir foi incluido na aula 105 antes de estender na classe PageAdmin.
    public function __construct($opts = array(), $tpl_dir = "/views/") {
     
        // leoc - array_merge mescla as arrays (!). A ordem do merge é importante. O último parâmetro $opts, 
        // se vier no construtor, e algo nele der conflito com o $defaults, o que vale é $opts, que sobrepõe o conflito no $default 
        $this->options = array_merge($this->defaults, $opts);
       	
        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
            "debug"         => false // set to false to improve the speed
       );

       Tpl::configure( $config );

       $this->tpl = new Tpl;

       // leoc - dados estão na chave "data". 
       $this->setData($this->options["data"]);

       // leoc - desenhar cabeçalho que se repete em toda página, sempre que instanciar esta classe Page.
       $this->tpl->draw("header"); // o método espera o nome do arquivo (que está em tpl_dir)
       
    }

    // método otimizado para percorrer o array no parâmetro recebido. Feito assim para não repetir o código no __construct() e no setTpl()
    private function setData($data = array()) {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value);
        }
    }

    // corpo da página
    public function setTpl($name, $data = array(), $returnHTML = false) {
        
        $this->setData($data);

        return $this->tpl->draw($name, $returnHTML); // return apenas se precisar retornar. Não obrigatório.
    }

    public function __destruct() {
 
        // rodapé que se repete em todas as páginas
        $this->tpl->draw("footer"); // o método espera o nome do arquivo (que está em tpl_dir)

    }

}


?>