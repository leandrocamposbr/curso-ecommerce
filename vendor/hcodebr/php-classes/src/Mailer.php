<?php 

// aula 107 18"55 
// para ganhar tempo copiei do github da aula, aqui https://github.com/hcodebr/ecommerce/blob/master/vendor/hcodebr/php-classes/src/Mailer.php
// e acrescentei comentários conforme aula

// classe usada no método getForgot() da classe User().

namespace Hcode;

use Rain\Tpl;

// nossa clase não precisa do Use porque está no escopo principal (?) aula 107 18"55

class Mailer {
	
    // dados do e-mail remetente
	const USERNAME = "xxxx@xxxx.xx"; // e-mail de envio (para o teste foi o dalmo do gmail)
	const PASSWORD = "xxxxx"; // senha da conta de email (!) não subir para github publico   
	const NAME_FROM = "Dalmo - suporte";
    // os dados do e-mail destinatário estão nas variáveis e incluídas direto nos métodos a seguir

	private $mail;

	public function __construct($toAddress, $toName, $subject, $tplName, $data = array()) {

		$config = array(
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"]."/views/email/",
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			"debug"         => false
	    );

        /////////////////////////////////////////////////////
        //var_dump($toAddress);
        //var_dump($toName);
        //var_dump($subject);
        //var_dump($tplName);
        //var_dump($data);
        /*
        string(26) "leandrocamposdev@gmail.com" 
        string(31) "Leandro Campos Dev - senha 1234" 
        string(30) "Redefinir senha da Hcode Store" 
        string(6) "forgot" 
        array(2) { 
            ["name"]=> string(31) "Leandro Campos Dev - senha 1234" 
            ["link"]=> string(88) "http://www.hcodecommerce.com.br/admin/forgot/reset?code=NGFydytNaVlGd3g4ZS9qQThMZk5xdz09" 
        }
        */            
        /////////////////////////////////////////////////////

		Tpl::configure( $config );

		$tpl = new Tpl;

		foreach ($data as $key => $value) {
			$tpl->assign($key, $value);
		}

		$html = $tpl->draw($tplName, true); // o true é para jogar na variável, e não na tela

		$this->mail = new \PHPMailer; // a contrabarra é porque o PHPMailer está no escopo principal - aula 108 23"00

		//Tell PHPMailer to use SMTP
		$this->mail->isSMTP();

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$this->mail->SMTPDebug = 2;

		//Ask for HTML-friendly debug output
		$this->mail->Debugoutput = 'html';

		//Set the hostname of the mail server
		$this->mail->Host = 'smtp.gmail.com';
		//$this->mail->Host = 'smtp-mail.outlook.com';
		//$this->mail->Host = 'smtp.live.com';

		// use
		// $this->mail->Host = gethostbyname('smtp.gmail.com');
		// if your network does not support SMTP over IPv6

		//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
		$this->mail->Port = 587;

		//Set the encryption system to use - ssl (deprecated) or tls
		$this->mail->SMTPSecure = 'tls';

		//Whether to use SMTP authentication
		$this->mail->SMTPAuth = true;

		//Username to use for SMTP authentication - use full email address for gmail
		$this->mail->Username = Mailer::USERNAME;

		//Password to use for SMTP authentication
		$this->mail->Password = Mailer::PASSWORD;

		//Set who the message is to be sent from
		$this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

		//Set an alternative reply-to address
		//$this->mail->addReplyTo('replyto@example.com', 'First Last');

		//Set who the message is to be sent to
		$this->mail->addAddress($toAddress, $toName);

		//Set the subject line
		$this->mail->Subject = $subject;

		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$this->mail->msgHTML($html);

		//Replace the plain text body with one created manually
		$this->mail->AltBody = 'This is a plain-text message body';

		//Attach an image file
		//$mail->addAttachment('images/phpmailer_mini.png');

	}

	public function send() {
   
        //var_dump($this->mail);
        //exit;
   
        // https://pt.stackoverflow.com/questions/74612/configurar-phpmailer-com-hotmail
        if (!$this->mail->send()) {
            echo "Mailer Error: " . $this->mail->ErrorInfo;                
        }

        //return $this->mail->send();
	}

}

 ?>