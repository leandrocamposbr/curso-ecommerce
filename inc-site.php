<?php

use \Hcode\Page; // aula 103 nosso namespace. Dentro de vendor tem várias. Essa é a nossa.
use \Hcode\Model\Product;
use \Hcode\Model\Category; // aula 113

// "/" é a rota que estamos chamando e o 'bloco da rota' entre { }.
// (*1) aula 103 - "se chamarem meu site sem nenhum complemento na url ("/"), execute isso"
$app->get('/', function() {		
    
	// Leoc - original
	//echo "OK";

	// leoc - aula 102 depois de incluir a classe sql com namespace Hcode\DB
	//$sql = new Hcode\DB\Sql();
	//$results = $sql->select("SELECT * FROM tb_users");
	//echo json_encode($results);

	// aula 112 - 4"52
	$products = Product::listAll(); // carrega todos os produtos do banco

	// aula 103, depois de criar a classe Page.
	// nossas classes ficam em vendor\hcodebr\php-classes\src
	$page = new Page(); // aqui já vai incluir o header 

	// aula acrescenta o corpo
	// aula 112 - 5"00 acrescentando dados para o template já com produtos 
	$page->setTpl("index", [
		'products'=>Product::checkList($products)
	]); 

	// nesse ponto, vai chamar o __destruct() da classe e desenhar o footer

});


// aula 110 # a partir da 109, menos comentários e não repete comentáriod do que já fez igual
// URL chamada ao clicar em uma categoria no site http://www.hcodecommerce.com.br/categories/7
$app->get("/categories/:idcategory", function($idcategory) {

	$category = new Category();

	$category->get((int)$idcategory);

	// construtor de Page() inclui o header 
	// setTpl(); // acrescenta o corpo
	// __destruct() da classe e desenha o footer
	$page = new Page(); 
	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>Product::checkList($category->getProducts())
	]); 

});


?>