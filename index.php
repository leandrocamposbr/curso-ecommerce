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
	User::verifyLogin();

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

$app->run(); // aqui é que executa tudo acima.

 ?>