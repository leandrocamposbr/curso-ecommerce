<?php 

use \Hcode\PageAdmin; // aula 105, use para a nova classe PageAdmin.
use \Hcode\Model\User; // aula 106, nova classe User em um namespace só para Models

// aula 107 rota do CRUD dos usuários admin
$app->get('/admin/users', function() {

	// User::metodoXpto são métodos estáticos na nossa classe 
	// que está em \vendor\hcodebr\src\model\User.php

	// precisa estar logado. não esqueça do session_start no início dessa index.php
	User::verifyLogin(); // não passamos parâmetro porque na função é true por padrão o inAdmin;

	// aula 107 07"10
	$users = User::listAll(); //método na nossa classe User que retorna um array com dados dos usuários

	$page = new PageAdmin();

	// vai passar para o template \vies\admin\users.html (~linha 38) o array de dados de usuário
	$page->setTpl("users", array(
		"users"=>$users
	)); 

});

// aula 107 03"15 rota do create do usuário do admin
// VEJA QUE tem 2 rotas com mesmo nome "/admin/users/create"
// - se acessar com get, vem para essa, que retorna um html.
// - se acessar com post, vai para a outra, esperando receber dados para gravar
$app->get("/admin/users/create", function () {

	// precisa estar logado. não esqueça do session_start no início dessa index.php
	User::verifyLogin(); // não passamos parâmetro porque na função é true por padrão o inAdmin;

	$page = new PageAdmin();
	$page->setTpl("users-create"); // nome da página html

});

// ATENÇÃO COM A ORDEM DAS ROTAS  <=== ESSA DELETE FICOU NESSA POSIÇÃO COM UM OBJETIVO. VEJA NOS COMENTÁRIOS

// aula 107 05"05 rota para excluir usuário MÉTODO DELETE
// a rota vai receber o id do usuário que queremos alterar (:iduser é um padrão).
// - isso é BOA PRÁTICA PARA ROTA: acessando via get um id é solicitar de um usr específico
// o valor que vier em :iduser será recebido na variável $iduser da função
// - só o fato de colocar o parâmetro iduser obrigatório na rota, já é entendido que o será recebido na função
// Obs: No começo da aula, isso estava no final do código, mas em 13"34 copiou pra cá porque o slim framework pode se confundir
// com a chamada abaixo dessa que não tem '/delete' se ela estiver antes. 
// Este método será chamado do botão 'deleete' da lista de usuários administradores no painel
$app->get("/admin/users/:iduser/delete", function ($iduser) {

	// precisa estar logado. não esqueça do session_start no início dessa index.php
	User::verifyLogin(); // não passamos parâmetro porque na função é true por padrão o inAdmin;

	// aula 107 38"00
	// verifica se usuário existe no banco
	$user = new User();
	$user->get((int)$iduser); 
	
	$user->delete(); // neste método delete() da classe User vai chamar a STORED PROCEDURE sp_users_delete() no banco.

	// voltar e exibir a lista de usuários remanescentes
	header("Location: /admin/users");
	exit;

});

// aula 107 03"15 rota do update do usuário do admin
// a rota vai receber o id do usuário que queremos alterar (:iduser é um padrão).
// - isso é BOA PRÁTICA PARA ROTA: acessando via get um id é solicitar de um usr específico
// - será a mesma tela de usuário, só que preenchida
// o valor que vier em :iduser será recebido na variável $iduser da função
// - só o fato de colocar o parâmetro iduser obrigatório na rota, já é entendido que o será recebido na função
// Notar que tem outra rota igual a essa, mas com post (vai para um ou outra dependendo do método na chamada)
// Este método será chamado botão 'editar' na lista de usuários do painel de administradores, em uma URL assim:
// - http://www.hcodecommerce.com.br/admin/users/11
$app->get("/admin/users/:iduser", function ($iduser) {

	// precisa estar logado. não esqueça do session_start no início dessa index.php
	User::verifyLogin(); // não passamos parâmetro porque na função é true por padrão o inAdmin;

	// aula 107 28"00 - editar (alterar usuário)
	$user = new User();
	$user->get((int)$iduser); // vai retornar um array com dados do $iduser

	$page = new PageAdmin();
	$page->setTpl("users-update", array(
		"user"=>$user->getValues()	// notar que $user é uma instância de User() que estende Model() <- o método getValues() está lá
	));



});

// aula 107 03"50 rota para salvar de fato os dados no banco (POST)
// VEJA QUE tem 2 rotas com mesmo nome "/admin/users/create"
// - se acessar com get, vai para a primeir, que retorna um html.
// - se acessar com post, vem para essa, esperando receber dados para gravar
// os dados virão do users-create.html do template de admin
$app->post("/admin/users/create", function() {

	// precisa estar logado. não esqueça do session_start no início dessa index.php
	User::verifyLogin(); // não passamos parâmetro porque na função é true por padrão o inAdmin;

	//var_dump($_POST);
	/*
	array(6) { 
	["desperson"]=> string(23) "Teste da Silva pwd 1234" 
	["deslogin"]=> string(5) "silva" 
	["nrphone"]=> string(6) "642244" 
	["desemail"]=> string(20) "silva@xptoxxx.com.br" 
	["despassword"]=> string(4) "1234" 
	["inadmin"]=> string(1) "1" }
	*/

	$user = new User();

	// olha só...
	// $_POST receberá um array json, com os campos com os mesmos nomes do database
	// nosso método setData já está preparado para setters dinâmicos conforme campos recebidos
	
	// só tem que tratar com operador ternário o inadmin
	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$user->setData($_POST);
	//var_dump($user);
	/*
	object(Hcode\Model\User)#38 (1) { ["values":"Hcode\Model":private]=> array(6) { 
	["desperson"]=> string(23) "Teste da Silva pwd 1234" 
	["deslogin"]=> string(5) "silva" 
	["nrphone"]=> string(6) "642244" 
	["desemail"]=> string(20) "silva@xptoxxx.com.br" 
	["despassword"]=> string(4) "1234" 
	["inadmin"]=> int(1) } 
	}
	*/

	$user->save(); // neste método save() da classe User vai chamar a STORED PROCEDURE sp_users_save() no banco.

	// voltar e exibir o usuário criado
	header("Location: /admin/users");
	exit;
});

// aula 107 04"42 rota para salvar a edição
// Notar que tem outra rota igual a essa, mas com get (vai para um ou outra dependendo do método na chamada)
// Será chamado do form de edição de usuário admin, ao clicar no botão 'salvar'
// a ULR é essa: http://www.hcodecommerce.com.br/admin/users/11 
$app->post("/admin/users/:iduser", function ($iduser) {

	// precisa estar logado. não esqueça do session_start no início dessa index.php
	User::verifyLogin(); // não passamos parâmetro porque na função é true por padrão o inAdmin;

	$user = new User();
	$user->get((int)$iduser);

	// aula 107 33"13 

	// lembrar que $_POST receberá um array json, com os campos com os mesmos nomes do database
	// nosso método setData já está preparado para setters dinâmicos conforme campos recebidos
	
	// só tem que tratar com operador ternário o inadmin
	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	//  aqui falou da polêmica: não deveria alterar somente os campos modificados, em vez de todo o registro?
	// eita conforme aula UPDATE é um INSERT E DELETE no banco. Então 'tanto faz' e por isso update em tudo...
	$user->setData($_POST);
	
	$user->update(); 

	// voltar e exibir o usuário já alterado
	header("Location: /admin/users");
	exit;

});


?>