<?php 

use \Hcode\PageAdmin; // aula 105, use para a nova classe PageAdmin.
use \Hcode\Model\User; // aula 106, nova classe User em um namespace só para Models

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


?>