<?php 

/*
  aula 111 - copiado do git do curso 
  https://github.com/hcodebr/ecommerce/blob/master/vendor/hcodebr/php-classes/src/Model/Product.php
*/

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Product extends Model {

	public static function listAll() {

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");

	}

	// aula 112 6"30
	// isso por causa da foto 'que não está na tabela produtos' conforme falou na aula
	public static function checkList($list) {

		// aula 112 7"06 - o "&" é para 'manipular a mesma variável na memória (?) conforme falado na aula'
		// significa que VAI ALTERAR O VALOR DENTRO DO ARRAY LIST 
		foreach ($list as &$row) { 
			
			$p = new Product();
			$p->setData($row);
			$row = $p->getValues();

		}

		return $list;

	}

	public function save() {

		$sql = new Sql();

		$results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(
			":idproduct"=>$this->getidproduct(),
			":desproduct"=>$this->getdesproduct(),
			":vlprice"=>$this->getvlprice(),
			":vlwidth"=>$this->getvlwidth(),
			":vlheight"=>$this->getvlheight(),
			":vllength"=>$this->getvllength(),
			":vlweight"=>$this->getvlweight(),
			":desurl"=>$this->getdesurl()
		));

		$this->setData($results[0]);

	}

	public function get($idproduct) {

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [
			':idproduct'=>$idproduct
		]);

		$this->setData($results[0]);

	}

	public function delete() {

		$sql = new Sql();

		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [
			':idproduct'=>$this->getidproduct()
		]);

	}

	// aula 111 28"04 verifica se a foto do produto existe.
	// se não existir usa uma padrão (quadrado cinza)
	public function checkPhoto() {

		if (file_exists(
			$_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR . 
			"img" . DIRECTORY_SEPARATOR . 
			"products" . DIRECTORY_SEPARATOR . 
			$this->getidproduct() . ".jpg"
			)) {

			$url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";

		} else {

			// imagem padrão (quadrado cinza), se a imagem do produto não existir
			$url = "/res/site/img/product.jpg";

		}

		return $this->setdesphoto($url);

	}

	// método existe na classe pai Model(). 
	// estamos sobrescrevendo aqui.
	// Precisamos desse artifício (mesmo nome aqui), para incluir a foto
	public function getValues()	{

		$this->checkPhoto();

		$values = parent::getValues(); // aula 111 25"37: chama o getValues() da classe pai, no caso Model()

		return $values;

	}

	// aula 111 - metodo chamado da classe inc-admin-products.php ao salvar produto
	public function setPhoto($file) {

		// separa a extensão do arquivo
		$extension = explode('.', $file['name']);   // extension agora é um array
		$extension = end($extension);				// agora extension tem apenas a última posição do array

		// usa a biblioteca GD para converter para jpg se subir formato diferente
		switch ($extension) {

			case "jpg":
			case "jpeg":
			$image = imagecreatefromjpeg($file["tmp_name"]); // "tmp_name" é o nome temporário que está no servidor
			break;

			case "gif":
			$image = imagecreatefromgif($file["tmp_name"]);
			break;

			case "png":
			$image = imagecreatefrompng($file["tmp_name"]);
			break;

		}

		// destino (onde salvar a imagem)
		$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR . 
			"img" . DIRECTORY_SEPARATOR . 
			"products" . DIRECTORY_SEPARATOR . 
			$this->getidproduct() . ".jpg";

		imagejpeg($image, $dist);

		imagedestroy($image); 

		$this->checkPhoto();

	}

	public function getFromURL($desurl) {

		$sql = new Sql();

		$rows = $sql->select("SELECT * FROM tb_products WHERE desurl = :desurl LIMIT 1", [
			':desurl'=>$desurl
		]);

		$this->setData($rows[0]);

	}

	public function getCategories() {

		$sql = new Sql();

		return $sql->select("
			SELECT * FROM tb_categories a INNER JOIN tb_productscategories b ON a.idcategory = b.idcategory WHERE b.idproduct = :idproduct
		", [

			':idproduct'=>$this->getidproduct()
		]);

	}

	public static function getPage($page = 1, $itemsPerPage = 10) {

		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products 
			ORDER BY desproduct
			LIMIT $start, $itemsPerPage;
		");

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];

	}

	public static function getPageSearch($search, $page = 1, $itemsPerPage = 10) {

		$start = ($page - 1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select("
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products 
			WHERE desproduct LIKE :search
			ORDER BY desproduct
			LIMIT $start, $itemsPerPage;
		", [
			':search'=>'%'.$search.'%'
		]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [
			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];

	}

}

 ?>