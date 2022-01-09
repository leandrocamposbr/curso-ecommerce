<?php 

session_start();
require_once("vendor/autoload.php");

// informa os namespaces. Dentro de vendor tem vários. Aqui informe o que quer carregar
use \Slim\Slim;	 
use \Hcode\Page; // aula 103 nosso namespace. Dentro de vendor tem várias. Essa é a nossa.
use \Hcode\PageAdmin; // aula 105, use para a nova classe PageAdmin.
use \Hcode\Model\User; // aula 106, nova classe User em um namespace só para Models

// aula 103 - aqui cria 'uma nova aplicação' no slim, para as rotas.
// antes chamávamos 'index.php, cadastro.php, xpto.php' com atributos na url.
// hoje, devido a SEO, rankeamento de busca etc, se usa rota...
// ... Passe um nome na URL e ele me manta para algum lugar(!)
// é isso o que o slim faz. (veja*1)
$app = new Slim();	// para instanciar direto sem 'use' seria '$app = new \Slim\Slim();'

$app->config('debug', true);

// nossas classes ficam em vendor\hcodebr\php-classes\src. Ex. new Page().

// "/" é a rota que estamos chamando e o 'bloco da rota' entre { }.
// (*1) aula 103 - "se chamarem meu site sem nenhum complemento na url ("/"), execute isso"
$app->get('/', function() {		
    
	// Leoc - original
	//echo "OK";

	// leoc - aula 102 depois de incluir a classe sql com namespace Hcode\DB
	//$sql = new Hcode\DB\Sql();
	//$results = $sql->select("SELECT * FROM tb_users");
	//echo json_encode($results);

	// aula 103, depois de criar a classe Page.
	// nossas classes ficam em vendor\hcodebr\php-classes\src
	$page = new Page(); // aqui já vai incluir o header 
	$page->setTpl("index"); // acrescenta o corpo

	// nesse ponto, vai chamar o __destruct() da classe e desenhar o footer

});

// aula 105 - 12"00' nova rota para a administração 
$app->get('/admin', function() {		
    
	// aula 106 29"40' verificar sessão de usuário (se está logado)
	// não esqueça do session_start no início dessa index.php
	User::verifyLogin(); // não passamos parâmetro porque na função é true por padrão o inAdmin;

	// nossas classes ficam em vendor\hcodebr\php-classes\src
	$page = new PageAdmin(); // aqui já vai incluir o header
	$page->setTpl("index"); // acrescenta o corpo

});

// aula 106 - nova rota para o login do admin
$app->get('/admin/login', function() {		

	// nossas classes ficam em vendor\hcodebr\php-classes\src
	// notar que login não header e footer. Então passa novas opções aqui, diferente das rotas acima.
	// esses parâmetros novos aí "header" e "footer" foram criados na aula 106.
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]); 
	$page->setTpl("login"); 

});

// aula 106 - nova rota para o POST do fomulário de login
$app->post('/admin/login', function() {

	// aula 106 7"55 - criar classe User, que é o "nosso DAO... nosso Model..." what?
	// ... com um método estático 'login' que recebe o post do formulario de login
	User::login($_POST["login"], $_POST["password"]);

	// redireciona para a home da administração
	header("Location: /admin");
	exit; 

});

// aula 106 35"03 rota do logout
$app->get('/admin/logout', function() {

	// essa rota será incluída no botão logout do template admin (views\admin\header.html)

	User::logout();
	header("Location: /admin/login");
	exit; 

});

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

// aula 108- Admin esqueceu a senha
$app->get('/admin/forgot', function() {		

	// nossas classes ficam em vendor\hcodebr\php-classes\src
	// notar que (igual ao login) aqui não tem header e footer. Então passa novas opções aqui, diferente das rotas acima.
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]); 
	$page->setTpl("forgot"); 

});

// aula 108 5"20
// post que vem do formulário da pagina forgot.html
$app->post("/admin/forgot", function() {

	// método na nossa classe User em 
	// \vendor\hcodebr\php-classes\src\Model\User.php
	$USER = User::getForgot($_POST["email"]);

	// aula 108 28"50
	header("Location: /admin/forgot/sent"); // rota está logo abaixo
	exit;

});

// aula 108 28"50
// rota chamada logo acima
$app->get("/admin/forgot/sent", function() {

	// nossas classes ficam em vendor\hcodebr\php-classes\src
	// notar que (igual ao login e forgot) aqui não tem header e footer. Então passa novas opções aqui, diferente das rotas acima.
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]); 

	$page->setTpl("forgot-sent");  // renderizar o template do e-mail enviado, que está em \views\admin\forgot-sent.html

});

// aula 108 32"34 
// rota para o link que usuario clica no e-mail de 'esqueci a senha'.
// a URL tem esse formato, conforme o teste que fiz: "http://www.hcodecommerce.com.br/admin/forgot/reset?code=NGFydytNaVlGd3g4ZS9qQThMZk5xdz09"
// notar que é a mesma rota do método POST abaixo desse
// essa é a solicitação (GET) a outra é o retorno (POST)
$app->get("/admin/forgot/reset", function() {

		// nosso método estático criado na classe User.
		// retorna a seleção correspondente ao id recebido na URL que está no link do e-mail que usuário recebeu no 'esqueci a senha'
		// desde que válida e com menos de 1 hora de emissão
		// (!) essa verificação do código é importante para garantir que não houve quebra de segurança. 
		$user = User::validForgotDecrypt($_GET["code"]); 

		// nossas classes ficam em vendor\hcodebr\php-classes\src
		// notar que (igual ao login e forgot) aqui não tem header e footer. Então passa novas opções aqui, diferente das rotas acima.
		$page = new PageAdmin([
			"header"=>false,
			"footer"=>false
		]); 
	
		// renderizar o template do e-mail enviado, que está em \views\admin\forgot-reset.html
        // O template espera variáveis, queestão entre chaves, ex. neste caso, o template espera:
        // {$name} que abaixo é "name"
        // {$code} que abaixo é "code"
		$page->setTpl("forgot-reset", array(
			"name"=>$user["desperson"],
			"code"=>$_GET["code"]
		));  
	
});

// aula 108 41"38
// método post para receber o formulário com a nova senha do usuário que abre quando ele clica no link 
// do seu e-mail para trocar a senha (sequencia de ações depois que ele clicou em 'esqueci a senha' no login admin)
// essa rota está lá no POST do form do template \views\admin\forgot-reset.html
// notar que é a mesma rota do método GET acima
// essa é a retorno (POST) a outra é a solicitação (GET)
$app->post("/admin/forgot/reset", function() {

	// verificar de novo se o código está ok, por segurança 

	$forgot = User::validForgotDecrypt($_POST["code"]);

	// novo método estático da classe user que vai salvar no banco que esse processo
	//  de recuperação já foi usado e não aceitar novamente, mesmo que no prazo de 1h
	User::setForgotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	// aula 108 50"00 criptografar a senha
	// indicou a documentação do php (google 'php password') vai prá cá:
	// https://www.php.net/manual/pt_BR/function.password-hash.php
	// no exemplo 1, usa PASSWORD_DEFAULT (tamanho da string pode variar) e não parametriza 'cost' (opcional)
	// no exemplo 2, usa PASSWORD_BCRYPT (string fixa em 60caracteres) e usa 'cost' que é o custo de processamento 
	//    para gerar a criptografia e desciptografar (quanto maior, mais segura, mas muito tempo)
	// nesse caso, a senha '12345' gerou "$2y$12$EXPB5yw9X4zICKg8g07Kr.XrE1BEepYJc4qCQ582t29pHPrlqmxo."
	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ["cost"=>12]);

	// método para setar a nova senha no banco, com hash
	$user->setPassword($password);

	//aula 108 47"09 confirmação visual ao usuário que a senha foi trocada, usando template \views\admin\forgot-reset-success.html

	// nossas classes ficam em vendor\hcodebr\php-classes\src
	// notar que (igual ao login e forgot) aqui não tem header e footer. Então passa novas opções aqui, diferente das rotas acima.
	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]); 
	
	// renderizar o template do e-mail enviado, que está em \views\admin\forgot-reset-success.html
	$page->setTpl("forgot-reset-success");  

});

$app->run(); // aqui é que executa tudo acima.

 ?>