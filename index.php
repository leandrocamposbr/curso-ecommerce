<?php 
session_start();
require_once("vendor/autoload.php");

// informa os namespaces. Dentro de vendor tem vários. Aqui informe o que quer carregar
use \Slim\Slim;

// aula 103 - aqui cria 'uma nova aplicação' no slim, para as rotas.
// antes chamávamos 'index.php, cadastro.php, xpto.php' com atributos na url. hoje, devido a SEO, rankeamento de busca etc, se usa rota...
// Passe um nome na URL e ele me manta para algum lugar(!) é isso o que o slim faz.
$app = new Slim();	// para instanciar direto sem 'use' seria '$app = new \Slim\Slim();'

$app->config('debug', true);

// includes para as rotas
require_once("inc-functions.php");
require_once("inc-site.php");
require_once("inc-admin.php");
require_once("inc-admin-users.php");
require_once("inc-admin-categories.php");
require_once("inc-admin-products.php");

$app->run();

?>