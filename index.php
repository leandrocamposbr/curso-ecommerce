<?php 

require_once("vendor/autoload.php");

// informa os namespaces. Dentro de vendor tem vários. Aqui informe o que quer carregar
use \Hcode\Page; // aula 103 nosso namespace. Dentro de vendor tem várias. Essa é a nossa.
use \Slim\Slim;	 

// aula 103 - aqui cria 'uma nova aplicação' no slim, para as rotas.
// antes chamávamos 'index.php, cadastro.php, xpto.php' com atributos na url.
// hoje, devido a SEO, rankeamento de busca etc, se usa rota...
// ... Passe um nome na URL e ele me manta para algum lugar(!)
// é isso o que o slim faz. (veja*1)
$app = new Slim();	// para instanciar direto sem 'use' seria '$app = new \Slim\Slim();'

$app->config('debug', true);

// "/" é a rota que estamos chamando e o bloco da rota.
// (*1) aula 103 - "se chamarem meu site sem nenhum complemento na url ("/"), execute isso"
$app->get('/', function() {		
    
	// Leoc - original
	//echo "OK";

	// leoc - aula 102 depois de incluir a classe sql com namespace Hcode\DB
	//$sql = new Hcode\DB\Sql();
	//$results = $sql->select("SELECT * FROM tb_users");
	//echo json_encode($results);

	// aula 103, depois de criar a classe Page.
	$page = new Page(); // aqui já vai incluir o header
	$page->setTpl("index"); // acrescenta o corpo

	// nesse ponto, vai chamar o __destruct() da classe e desenhar o footer

});

$app->run(); // aqui é que executa tudo acima.

 ?>