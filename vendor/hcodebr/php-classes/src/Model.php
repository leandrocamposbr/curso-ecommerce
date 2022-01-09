<?php

// aula 106 - 15"15 - classe model para os getters e setters 
// (primeiramente para o login do model User (ver .\Model\User.php))

namespace Hcode; // está no namespace principal

class Model {

    private $values = [];

    // precisamos dos campos que estão no objeto, neste caso do usuário
    // para isso precisamos saber que o método foi chamado.
    // método mágico que executa sempre que uma método é chamado __call
    // conforme documentação do PHP:
    // - $name é o nome do método chamado.
    // - $arguments é um array enumerado contendo os parâmetros passados para $name
    public function __call($name, $args) {
        // verificar se foi chamado um método setxpto() ou getxpto() 
        // get será para retornar dados; set será para gravar dados.
        // exemplo de chamada foi $user->setiduser($data["iduser"]) na classe User.php ao validar o login.
        $method = substr($name, 0, 3); // pega as 3 primeiras posições da string
        $fieldName = substr($name, 3, strlen($name)); // pega restante da string a partir da posição 3

       // var_dump($name, $method, $fieldName); // string(9) "setiduser" string(3) "set" string(6) "iduser"
       // exit; // para não dar 'redirect' (o que não deixaria ver o resultado do var_dump());

        switch ($method) {
            case "get":
                return (isset($this->values[$fieldName])) ? $this->values[$fieldName] : NULL;
                break;
            case "set";
                $this->values[$fieldName] = $args[0];
                break;
        }

    }

    // aula 106 23"50 métdo setter do model User.
    // receberá o array do select, com todos os campos que o select encontrou
    // para fazer aqui um getter/setter automático e dinâmico (!)
    public function setData($data = array()) {
        foreach ($data as $key => $value) {
            // as chaves são PORQUE VAI CRIAR UM MÉTODO DINAMICAMENTEEEEEEEEEEEEEEEEEEE
            $this->{"set".$key}($value);               
        }
    }

    // aula 106 28:35 método getter do model User.
    public function getValues() {
        return $this->values;
    }

}


?>

