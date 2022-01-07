<?php

// aula 106, classe Model para o User 
// usada 1a vez para o login no admin

namespace Hcode\Model;

use \Hcode\DB\Sql;
// aula 106, 14"46' - boa prática é ter uma classe de getters e setters para a classe model (como é o caso dessa)
// e daí estender a classe model, que daí já saberá fazer seus getters/setters respectivos.
use \Hcode\Model;
use UConverter;

class User extends Model {

    const SESSION = "User"; // ver nota *1

    public static function login ($login, $password) {

        $sql = new Sql();

        // :LOGIN é para evitar SQL Injection
        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            // bind dos parâmetros
            ":LOGIN"=>$login
        ));

        // se não encotntrou nada, manda exceção
        if (count($results) === 0) {
            // a barra antes do Exception é porque ela está no escopo (namespace) principal do PHP
            // e não dentro do namespace \Hcode\Model. Não criamos a nossa própria exception.
            throw new \Exception("Usuário inexistente ou senha inválida. (1)", 1);
        }

        $data = $results[0];
        // verificar senha. Usa a função de verificação do php, que retorna true/false para um hash
        // ** A SENHA É 'admin' **
        if (password_verify($password, $data["despassword"]) === true) {
            $user = new User(); // eita, instancia a própria classe PORQUE ESTENDE MODEL!
            
            // 1o passo da aula 106, fez um setidUser passando o campo do bd idUser 
            // (setter do campo idUser) que será tratado no Model.php 
            //$user->setiduser($data["iduser"]);
            // fica assim:            
            //var_dump($user); 
            // resultado (com control U no browser):
            //object(Hcode\Model\User)#26 (1) {
            //    ["values":"Hcode\Model":private]=>
            //    array(1) {
            //      ["iduser"]=>string(1) "1"
            //    }
            // }

            // 2o passo da aula 106 22"30, em vez de passar igual campo a campo, quer passar o array 
            // inteiro recebido do select e daí criar métodos get/set dinâmicos conforme o número 
            // de campos e nomes dos campos recebidos no array vindo do select.
            //  Daí criou esse método passando o array inteiro que será tratadno no Model.php
            $user->setData($data); // notar que $user é uma instância de User() que estende Model() <- o método setData() está lá
            //var_dump($user); 
            // resultado (com control U no browser) é um array com o model completo setado!
            //object(Hcode\Model\User)#26 (1) {
            //    ["values":"Hcode\Model":private]=>
            //    array(6) {
            //      ["iduser"]=>string(1) "1"
            //      ["idperson"]=>string(1) "1"
            //      ["deslogin"]=>string(5) "admin"
            //      ["despassword"]=>string(60) "$2y$12$YlooCyNvyTji8bPRcrfNfOKnVMmZA9ViM2A3IpFjmrpIbp5ovNmga"
            //      ["inadmin"]=>string(1) "1"
            //      ["dtregister"]=>string(19) "2017-03-13 03:00:00"
            //    }
            // }
            //exit; // para não dar 'redirect' (o que não deixaria ver o resultado do var_dump());

            // aula 106 26"51 - criar a sessão do login
            // usou constante (*1) como opção de organização, para padronizar nas outras chamadas da sessão
            $_SESSION[User::SESSION] = $user->getValues(); // notar que $user é uma instância de User() que estende Model() <- o método getValues() está lá

            // aula 106 ainda, no passo 2 acima. 
            // Retorna o objeto com o model completo carregado dinamicamente!
            // (ver notas no Model.php=>setData())
            return $user;

        } else {
            throw new \Exception("Usuário inexistente ou senha inválida. (2)", 1);
        }

    }

    // aula 106 30"00 - método estátivco para verificar na sessão se está logado. 
    // chamado das páginas. Ex. da index.php
    public static function verifyLogin($inadmin = true) {

        // SESSION é uma constante criada lá em cima

        if (
            !isset($_SESSION[User::SESSION])
            || !$_SESSION[User::SESSION]
            || !(int)$_SESSION[User::SESSION]["iduser"] > 0
            || (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
            ) {
            
            header("Location: /admin/login");
            exit;
        }

    }

    // aula 106 34"37 - logour
    public static function logout() {
        $_SESSION[User::SESSION] = NULL; // Também poderia ser session_unset passando o nome da session
    }

}

?>