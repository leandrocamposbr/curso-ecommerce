<?php 

// informa os namespaces. Dentro de vendor tem vários. Aqui informe o que quer carregar
use \Hcode\PageAdmin; // aula 105, use para a nova classe PageAdmin.
use \Hcode\Model\User; // aula 106, nova classe User em um namespace só para Models
use \Hcode\Model\Category; // aula 109
use \Hcode\Model\Product; // aula 111

// aula 109 # a partir da 109, menos comentários e não repete comentáriod do que já fez igual
$app->get("/admin/categories", function() {
	
	User::verifyLogin(); // ver notas nas chamadas anteriores acima

	$categories = Category::listAll();	

	$page = new PageAdmin();

	$page->setTpl("categories", ["categories"=>$categories]);

});

// aula 109 # a partir da 109, menos comentários e não repete comentáriod do que já fez igual
$app->get("/admin/categories/create", function() {
	
	User::verifyLogin(); // ver notas nas chamadas anteriores acima

	$page = new PageAdmin();

	$page->setTpl("categories-create");

});

// aula 109 # a partir da 109, menos comentários e não repete comentáriod do que já fez igual
$app->post("/admin/categories/create", function() {
	
	User::verifyLogin(); // ver notas nas chamadas anteriores acima

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;

});

// aula 109 # a partir da 109, menos comentários e não repete comentáriod do que já fez igual
// URL chamada do botão delete da categoria: http://www.hcodecommerce.com.br/admin/categories/1/delete
$app->get("/admin/categories/:idcategory/delete", function($idcategory) {
	
	User::verifyLogin(); // ver notas nas chamadas anteriores acima

	$category = new Category();

	// carrega, deleta, redireciona

	$category->get((int)$idcategory);

	$category->delete();

	header("Location: /admin/categories");
	exit;

});

// aula 109 # a partir da 109, menos comentários e não repete comentáriod do que já fez igual
// URL chamada do botão editar da categoria: http://www.hcodecommerce.com.br/admin/categories/2 
$app->get("/admin/categories/:idcategory", function($idcategory) {
	
	User::verifyLogin(); // ver notas nas chamadas anteriores acima

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", array(
		'category'=>$category->getValues()
	));

});

// aula 109 # a partir da 109, menos comentários e não repete comentáriod do que já fez igual
// URL chamada do botão editar > salvar da categoria, formulário POST que envia:
// http://www.hcodecommerce.com.br/admin/categories/2	
$app->post("/admin/categories/:idcategory", function($idcategory) {
	
	User::verifyLogin(); // ver notas nas chamadas anteriores acima

	$category = new Category();

	// carrega dados atuais, atualiza com dados recebidos do post, atualiza no banco

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();

	header("Location: /admin/categories");
	exit;

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
		'products'=>[]
	]); 

});



?>